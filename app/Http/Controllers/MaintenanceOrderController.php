<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MaintenanceOrder;
use App\Models\Ownership;
use App\Models\RepairPrice;
use App\Models\Technician;
use App\Models\User;
use App\Notifications\ComplaintCompleted;
use App\Notifications\ComplaintStatusChanged;
use App\Notifications\CostEstimationApproved;
use App\Notifications\TechnicianAssigned;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceOrderController extends Controller
{
    const PRIORITIES = ['urgent' => 24, 'medium' => 72, 'low' => 168]; // SLA in hours

    // ─── HELPER: Hitung SLA Deadline ───
    private function calculateSlaDeadline(string $priority): Carbon
    {
        $hours = self::PRIORITIES[$priority] ?? 72;
        return now()->addHours($hours);
    }

    // ─── HELPER: Kirim Notifikasi ───
    private function notifyReporter(MaintenanceOrder $order, string $type, $oldStatus = null, $newStatus = null): void
    {
        $reporter = $order->reporter;
        if (!$reporter || !$reporter->email) return;

        try {
            match ($type) {
                'status_changed' => $reporter->notify(new ComplaintStatusChanged($order, $oldStatus, $newStatus)),
                'assigned' => $reporter->notify(new TechnicianAssigned($order)),
                'completed' => $reporter->notify(new ComplaintCompleted($order)),
                'estimate_approved' => $reporter->notify(new CostEstimationApproved($order)),
                default => null,
            };
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim notifikasi: ' . $e->getMessage());
        }
    }

    // ─── HELPER: Smart Assignment ───
    private function findBestTechnician(MaintenanceOrder $order): ?Technician
    {
        $ownership = $order->ownership;
        $specialtyMap = [
            'Listrik' => ['Listrik'],
            'Air/Pipa' => ['Air/Pipa'],
            'Bangunan/Semen' => ['Bangunan/Semen', 'Keramik'],
            'Atap' => ['Atap', 'Bangunan/Semen'],
            'Kayu' => ['Kayu', 'Bangunan/Semen'],
            'Keramik' => ['Keramik', 'Bangunan/Semen'],
        ];

        $title = strtolower($order->complaint_title . ' ' . $order->complaint_description);
        $matchedSpecialties = [];
        foreach ($specialtyMap as $keyword => $specialties) {
            if (str_contains($title, strtolower($keyword))) {
                $matchedSpecialties = array_merge($matchedSpecialties, $specialties);
            }
        }
        if (empty($matchedSpecialties)) {
            $matchedSpecialties = ['Lainnya'];
        }

        $candidates = Technician::whereIn('specialty', $matchedSpecialties)
            ->where('status', 'Available')
            ->withCount(['maintenanceOrders as workload' => function ($q) {
                $q->whereIn('status', ['in_progress', 'pending', 'scheduled']);
            }])
            ->orderBy('workload', 'asc')
            ->get();

        if ($candidates->isEmpty()) {
            $candidates = Technician::where('status', 'Available')
                ->withCount(['maintenanceOrders as workload' => function ($q) {
                    $q->whereIn('status', ['in_progress', 'pending', 'scheduled']);
                }])
                ->orderBy('workload', 'asc')
                ->get();
        }

        return $candidates->first();
    }

    // ─── HELPER: Update SLA Status ───
    private function updateSlaStatus(): void
    {
        $orders = MaintenanceOrder::whereIn('status', ['waiting_approval', 'pending', 'scheduled', 'in_progress', 'reopened'])
            ->whereNotNull('sla_deadline')
            ->get();

        foreach ($orders as $order) {
            $deadline = $order->sla_deadline;
            $now = now();

            if ($now->greaterThan($deadline)) {
                if ($order->sla_status !== 'violated') {
                    $order->sla_status = 'violated';
                    $order->save();
                }
            } elseif ($now->diffInHours($deadline, false) <= 6 && $now->lessThan($deadline)) {
                if ($order->sla_status !== 'warning') {
                    $order->sla_status = 'warning';
                    $order->save();
                }
            } else {
                if ($order->sla_status !== 'on_track') {
                    $order->sla_status = 'on_track';
                    $order->save();
                }
            }
        }
    }

    // ─── FITUR ADMIN ───

    public function indexAdmin()
    {
        $this->updateSlaStatus();

        $query = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician']);

        if ($priority = request('priority')) {
            $query->where('priority', $priority);
        }

        $orders = $query->orderByRaw("FIELD(status, 'waiting_approval', 'pending', 'scheduled', 'in_progress', 'on_hold', 'reopened', 'done', 'rejected', 'cancelled')")
            ->latest()
            ->paginate(10);

        return view('admin.maintenance.index', compact('orders'));
    }

    public function showAdmin($id)
    {
        $order = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician', 'reporter'])->findOrFail($id);
        $technicians = Technician::where('status', 'Available')->get();
        $repairPrices = RepairPrice::all();
        $isWarrantyExpired = Carbon::now()->greaterThan($order->ownership->warranty_end_date);

        // Cari rekomendasi teknisi terbaik buat preview
        $recommendedTech = $order->technician_id ? null : $this->findBestTechnician($order);

        return view('admin.maintenance.show', compact('order', 'technicians', 'repairPrices', 'isWarrantyExpired', 'recommendedTech'));
    }

    // ─── SMART ASSIGN ───
    public function smartAssign($id)
    {
        $order = MaintenanceOrder::with('ownership')->findOrFail($id);

        $bestTech = $this->findBestTechnician($order);
        if (!$bestTech) {
            return back()->with('error', 'Tidak ada teknisi yang tersedia saat ini.');
        }

        $order->technician_id = $bestTech->id;
        $order->status = 'scheduled';
        $order->save();

        $bestTech->update(['status' => 'Busy']);

        $this->notifyReporter($order, 'assigned');

        return back()->with('success', 'Teknisi ' . $bestTech->name . ' berhasil ditugaskan secara otomatis!');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = MaintenanceOrder::findOrFail($id);
        $oldStatus = $order->status;

        $request->validate(['status' => 'required']);

        $costInput = $request->input('cost');
        $cleanCost = (int) $costInput;

        $order->status = $request->status;
        $notified = false;

        if ($request->status == 'in_progress' && $request->technician_id) {
            $order->technician_id = $request->technician_id;
            Technician::where('id', $request->technician_id)->update(['status' => 'Busy']);
            $this->notifyReporter($order, 'assigned');
            $notified = true;
        }

        if ($request->status == 'scheduled' && $request->technician_id) {
            $order->technician_id = $request->technician_id;
            Technician::where('id', $request->technician_id)->update(['status' => 'Busy']);
            $this->notifyReporter($order, 'assigned');
            $notified = true;
        }

        if ($request->status == 'done') {
            $order->completion_date = now();
            if ($order->technician_id) {
                Technician::where('id', $order->technician_id)->update(['status' => 'Available']);
            }
            $this->notifyReporter($order, 'completed');
            $notified = true;
        }

        if ($request->status == 'rejected') {
            $order->rejection_reason = $request->rejection_reason;
            if ($order->technician_id) {
                Technician::where('id', $order->technician_id)->update(['status' => 'Available']);
            }
        }

        if ($request->status == 'cancelled') {
            if ($order->technician_id) {
                Technician::where('id', $order->technician_id)->update(['status' => 'Available']);
            }
        }

        $order->scheduled_date = $request->scheduled_date ?? $order->scheduled_date;
        $order->admin_notes = $request->admin_notes ?? $order->admin_notes;

        if ($request->has('priority') && in_array($request->priority, ['urgent', 'medium', 'low'])) {
            $order->priority = $request->priority;
            $order->sla_deadline = $this->calculateSlaDeadline($request->priority);
        }

        if ($cleanCost > 0) {
            $order->cost = $cleanCost;
            if ($order->payment_status != 'Paid') {
                $order->payment_status = 'Unpaid';
            }
        } else {
            $order->cost = 0;
            if ($order->payment_status != 'Paid') {
                $order->payment_status = 'Free';
            }
        }

        $order->save();

        if (!$notified && $oldStatus != $order->status) {
            $this->notifyReporter($order, 'status_changed', $oldStatus, $order->status);
        }

        return redirect()->route('admin.maintenance.index')
            ->with('success', 'Status & data berhasil diperbarui.');
    }

    // ─── APPROVE / REJECT ESTIMASI ───
    public function approveEstimate($id)
    {
        $order = MaintenanceOrder::findOrFail($id);

        if ($order->cost_status !== 'pending') {
            return back()->with('error', 'Tidak ada estimasi yang menunggu persetujuan.');
        }

        $order->cost_status = 'approved';
        $order->cost = (int) $order->estimated_cost;
        $order->cost_approved_by = Auth::id();
        $order->cost_approved_at = now();
        $order->payment_status = 'Unpaid';
        $order->save();

        $this->notifyReporter($order, 'estimate_approved');

        return back()->with('success', 'Estimasi biaya disetujui. Pelanggan akan mendapat notifikasi.');
    }

    public function rejectEstimate(Request $request, $id)
    {
        $order = MaintenanceOrder::findOrFail($id);

        if ($order->cost_status !== 'pending') {
            return back()->with('error', 'Tidak ada estimasi yang menunggu persetujuan.');
        }

        $order->cost_status = 'rejected';
        $order->admin_notes = $request->input('rejection_note', $order->admin_notes);
        $order->save();

        return back()->with('success', 'Estimasi biaya ditolak. Teknisi akan diberi tahu.');
    }

    // ─── FITUR TEKNISI ───

    public function indexTechnician()
    {
        $user = Auth::user();
        if ($user->role !== 'teknisi') abort(403, 'Akses khusus teknisi.');

        $technician = Technician::where('user_id', $user->id)->first();

        if (!$technician) {
            return redirect()->route('dashboard')
                ->with('error', 'Akun Anda belum dihubungkan dengan Profil Teknisi oleh Admin.');
        }

        $pendingOrders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician'])
            ->where('status', 'pending')
            ->whereNull('technician_id')
            ->orderByDesc('complaint_date')
            ->get();

        $inProgressOrders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician'])
            ->where('status', 'in_progress')
            ->where('technician_id', $technician->id)
            ->orderByDesc('complaint_date')
            ->get();

        $scheduledOrders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician'])
            ->where('status', 'scheduled')
            ->where('technician_id', $technician->id)
            ->orderByDesc('complaint_date')
            ->get();

        $onHoldOrders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician'])
            ->where('status', 'on_hold')
            ->where('technician_id', $technician->id)
            ->orderByDesc('complaint_date')
            ->get();

        $doneOrders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'technician'])
            ->where('status', 'done')
            ->where('technician_id', $technician->id)
            ->orderByDesc('complaint_date')
            ->limit(10)
            ->get();

        return view('technician.maintenance.index', compact(
            'pendingOrders', 'inProgressOrders', 'scheduledOrders', 'onHoldOrders', 'doneOrders'
        ));
    }

    public function technicianUpdateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'teknisi') abort(403);

        $technician = Technician::where('user_id', $user->id)->first();

        $request->validate([
            'status' => 'required|in:waiting_approval,pending,scheduled,in_progress,on_hold,rejected,reopened,done,cancelled',
            'rejection_reason' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'admin_notes' => 'nullable|string',
        ]);

        $order = MaintenanceOrder::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Claim pending order
        if ($oldStatus === 'pending' && $newStatus === 'in_progress') {
            if ($order->technician_id !== null) {
                return back()->with('error', 'Pekerjaan ini sudah diambil teknisi lain.');
            }
            $order->technician_id = $technician->id;
            $technician->update(['status' => 'Busy']);
            $this->notifyReporter($order, 'assigned');
        }
        // Start scheduled work
        elseif ($oldStatus === 'scheduled' && $newStatus === 'in_progress') {
            if ($order->technician_id !== $technician->id) {
                return back()->with('error', 'Anda tidak berhak mengerjakan tugas teknisi lain.');
            }
            $technician->update(['status' => 'Busy']);
            $this->notifyReporter($order, 'status_changed', $oldStatus, $newStatus);
        }
        // Complete work
        elseif ($newStatus === 'done') {
            if ($order->technician_id !== $technician->id) {
                return back()->with('error', 'Anda tidak berhak menyelesaikan tugas teknisi lain.');
            }
            $order->completion_date = now();
            if ($order->technician_id) {
                Technician::where('id', $order->technician_id)->update(['status' => 'Available']);
            }
            $this->notifyReporter($order, 'completed');
        }
        // Handle other status transitions
        else {
            if ($order->technician_id !== null && $order->technician_id !== $technician->id) {
                return back()->with('error', 'Anda tidak berhak mengubah status pekerjaan teknisi lain.');
            }

            if ($newStatus === 'cancelled' || $newStatus === 'rejected') {
                if ($order->technician_id) {
                    Technician::where('id', $order->technician_id)->update(['status' => 'Available']);
                }
            }

            if ($oldStatus !== $newStatus) {
                $this->notifyReporter($order, 'status_changed', $oldStatus, $newStatus);
            }
        }

        $order->status = $newStatus;
        $order->scheduled_date = $request->scheduled_date ?? $order->scheduled_date;
        $order->admin_notes = $request->admin_notes ?? $order->admin_notes;
        $order->rejection_reason = $request->rejection_reason ?? $order->rejection_reason;
        $order->save();

        return back()->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    // ─── TEKNISI: INPUT ESTIMASI BIAYA ───
    public function technicianInputEstimate(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'teknisi') abort(403);

        $technician = Technician::where('user_id', $user->id)->first();
        $order = MaintenanceOrder::findOrFail($id);

        if ($order->technician_id !== $technician->id) {
            return back()->with('error', 'Ini bukan tugas Anda.');
        }

        $request->validate([
            'estimated_cost' => 'required|numeric|min:0',
            'estimated_description' => 'nullable|string',
        ]);

        $order->estimated_cost = $request->estimated_cost;
        $order->estimated_description = $request->estimated_description;
        $order->cost_status = 'pending';
        $order->save();

        return back()->with('success', 'Estimasi biaya telah dikirim ke Admin untuk disetujui.');
    }

    // ─── FITUR WARGA ───

    public function indexUser()
    {
        $orders = MaintenanceOrder::with(['ownership.unit', 'technician'])
            ->where('reporter_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('complaints.index', compact('orders'));
    }

    public function create()
    {
        $user = Auth::user();
        $myHomes = collect([]);

        if ($user->role === 'nasabah') {
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $myHomes = Ownership::where('customer_id', $customer->id)
                    ->where('status', 'Active')
                    ->with('unit')
                    ->get();
            }
        } elseif ($user->role === 'warga') {
            if ($user->ownership_id) {
                $myHomes = Ownership::where('id', $user->ownership_id)
                    ->where('status', 'Active')
                    ->with('unit')
                    ->get();
            }
        }

        return view('complaints.create', compact('myHomes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ownership_id' => 'required|exists:ownerships,id',
            'complaint_title' => 'required|string|max:255',
            'complaint_description' => 'required|string',
            'complaint_photo' => 'nullable|image|max:2048',
            'priority' => 'required|in:urgent,medium,low',
        ]);

        $photoPath = null;
        if ($request->hasFile('complaint_photo')) {
            $photoPath = $request->file('complaint_photo')->store('complaints', 'public');
        }

        $priority = $request->priority;
        $slaDeadline = $this->calculateSlaDeadline($priority);

        $order = MaintenanceOrder::create([
            'ownership_id' => $request->ownership_id,
            'reporter_id' => Auth::id(),
            'complaint_title' => $request->complaint_title,
            'complaint_description' => $request->complaint_description,
            'complaint_photo' => $photoPath,
            'complaint_date' => now(),
            'priority' => $priority,
            'status' => 'pending',
            'sla_deadline' => $slaDeadline,
            'cost' => 0,
            'payment_status' => 'Free',
            'cost_status' => 'none',
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Keluhan berhasil dikirim. Teknisi akan segera memproses keluhan Anda.');
    }

    // ─── KONFIRMASI BIAYA OLEH WARGA ───
    public function confirmCost($id)
    {
        $order = MaintenanceOrder::where('reporter_id', Auth::id())->findOrFail($id);

        if ($order->cost_status !== 'approved') {
            return back()->with('error', 'Belum ada estimasi biaya yang disetujui.');
        }

        $order->payment_status = 'Unpaid';
        $order->save();

        return back()->with('success', 'Biaya telah dikonfirmasi. Silakan lakukan pembayaran ke Admin.');
    }

    public function markAsPaid($id)
    {
        $order = MaintenanceOrder::findOrFail($id);

        if ($order->cost <= 0) {
            return back()->with('error', 'Tidak ada tagihan untuk pesanan ini.');
        }

        $order->update(['payment_status' => 'Paid']);

        return back()->with('success', 'Tagihan berhasil ditandai LUNAS.');
    }

    // ─── UPLOAD BUKTI PEMBAYARAN OLEH WARGA ───
    public function uploadPaymentProof(Request $request, $id)
    {
        $order = MaintenanceOrder::where('reporter_id', Auth::id())->findOrFail($id);

        if ($order->payment_status !== 'Unpaid') {
            return back()->with('error', 'Tidak perlu upload bukti pembayaran.');
        }

        $request->validate([
            'payment_proof' => 'required|image|max:5120',
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasinya.');
    }

    // ─── VERIFIKASI PEMBAYARAN OLEH ADMIN ───
    public function verifyPayment($id)
    {
        $order = MaintenanceOrder::findOrFail($id);

        if (!$order->payment_proof) {
            return back()->with('error', 'Belum ada bukti pembayaran yang diupload.');
        }

        $order->update([
            'payment_status' => 'Paid',
            'payment_proof_verified_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan ditandai LUNAS.');
    }

    public function showUser($id)
    {
        $order = MaintenanceOrder::where('reporter_id', Auth::id())->with('technician')->findOrFail($id);
        return view('complaints.show', compact('order'));
    }

    public function rateService(Request $request, $id)
    {
        $order = MaintenanceOrder::where('reporter_id', Auth::id())->findOrFail($id);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $order->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }

    public function showTechnician($id)
    {
        $user = Auth::user();
        if ($user->role !== 'teknisi') abort(403);

        $technician = Technician::where('user_id', $user->id)->first();
        $order = MaintenanceOrder::with(['ownership.unit', 'ownership.customer', 'reporter'])->findOrFail($id);

        if ($order->technician_id !== null && $order->technician_id !== $technician->id) {
            abort(403, 'Akses ditolak. Ini adalah pekerjaan milik teknisi lain.');
        }

        return view('technician.maintenance.show', compact('order'));
    }

    public function printTicket($id)
    {
        $order = MaintenanceOrder::with(['ownership.unit', 'technician'])
            ->where('reporter_id', Auth::id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('complaints.ticket', compact('order'));
        $pdf->setPaper('A5', 'landscape');

        return $pdf->stream('Tiket-Laporan-#'.$order->id.'.pdf');
    }
}

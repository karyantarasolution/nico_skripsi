<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceOrder;
use App\Models\Technician;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Update SLA statuses
        $this->updateSlaStatus();

        $user = Auth::user();

        if ($user->role === 'admin') {
            $totalUnits = Unit::count();
            $soldUnits = Unit::where('status', 'Terjual')->count();
            $availableUnits = Unit::where('status', 'Tersedia')->count();

            $technicians = Technician::count();
            $techAvailable = Technician::where('status', 'Available')->count();

            $pendingComplaints = MaintenanceOrder::where('status', 'waiting_approval')->count();
            $processComplaints = MaintenanceOrder::whereIn('status', ['in_progress', 'scheduled'])->count();
            $doneComplaints = MaintenanceOrder::where('status', 'done')->count();

            // Grafik: Keluhan per Bulan (6 bulan)
            $complaintsPerMonth = MaintenanceOrder::select(
                DB::raw('count(id) as total'),
                DB::raw("DATE_FORMAT(complaint_date, '%M') as month_name"),
                DB::raw('MONTH(complaint_date) as month')
            )
                ->whereYear('complaint_date', date('Y'))
                ->groupBy('month_name', 'month')
                ->orderBy('month')
                ->limit(6)
                ->pluck('total', 'month_name');

            // GRAFIK 1: Kerusakan Terbanyak (by judul keluhan - top 5)
            $topDamages = MaintenanceOrder::select('complaint_title', DB::raw('count(*) as total'))
                ->groupBy('complaint_title')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // GRAFIK 2: Performa Teknisi (jumlah selesai per teknisi)
            $techPerformance = Technician::withCount(['maintenanceOrders as total_done' => function ($q) {
                $q->where('status', 'done');
            }])
                ->orderByDesc('total_done')
                ->limit(5)
                ->get();

            // Rata-rata waktu penyelesaian (dalam jam)
            $avgResolution = MaintenanceOrder::where('status', 'done')
                ->whereNotNull('completion_date')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, complaint_date, completion_date)) as avg_hours'))
                ->value('avg_hours');

            // SLA Status Breakdown
            $slaOnTrack = MaintenanceOrder::where('sla_status', 'on_track')->count();
            $slaViolated = MaintenanceOrder::where('sla_status', 'violated')->count();
            $slaWarning = MaintenanceOrder::where('sla_status', 'warning')->count();

            // Statistik Prioritas
            $urgentCount = MaintenanceOrder::where('priority', 'urgent')->count();
            $mediumCount = MaintenanceOrder::where('priority', 'medium')->count();
            $lowCount = MaintenanceOrder::where('priority', 'low')->count();

            // Revenue stats
            $totalRevenue = MaintenanceOrder::where('payment_status', 'Paid')->sum('cost');
            $totalUnpaid = MaintenanceOrder::where('payment_status', 'Unpaid')->sum('cost');

            return view('dashboard', compact(
                'totalUnits', 'soldUnits', 'availableUnits',
                'technicians', 'techAvailable',
                'pendingComplaints', 'processComplaints', 'doneComplaints',
                'complaintsPerMonth', 'topDamages', 'techPerformance',
                'avgResolution', 'slaOnTrack', 'slaViolated', 'slaWarning',
                'urgentCount', 'mediumCount', 'lowCount',
                'totalRevenue', 'totalUnpaid'
            ));
        }

        if ($user->role === 'teknisi') {
            $technician = Technician::where('user_id', $user->id)->first();

            if (!$technician) {
                $pendingComplaints = 0;
                $processComplaints = 0;
                $doneComplaints = 0;
            } else {
                $pendingComplaints = MaintenanceOrder::where('status', 'pending')
                    ->whereNull('technician_id')
                    ->count();

                $processComplaints = MaintenanceOrder::whereIn('status', ['in_progress', 'scheduled'])
                    ->where('technician_id', $technician->id)
                    ->count();

                $doneComplaints = MaintenanceOrder::where('status', 'done')
                    ->where('technician_id', $technician->id)
                    ->count();
            }

            return view('dashboard', compact(
                'pendingComplaints', 'processComplaints', 'doneComplaints'
            ));
        }

        $myComplaintsTotal = MaintenanceOrder::where('reporter_id', $user->id)->count();
        $myComplaintsPending = MaintenanceOrder::where('reporter_id', $user->id)
            ->whereIn('status', ['waiting_approval', 'pending'])->count();
        $myComplaintsProcess = MaintenanceOrder::where('reporter_id', $user->id)
            ->whereIn('status', ['in_progress', 'scheduled'])->count();
        $myComplaintsDone = MaintenanceOrder::where('reporter_id', $user->id)
            ->where('status', 'done')->count();

        return view('dashboard', compact(
            'myComplaintsTotal', 'myComplaintsPending', 'myComplaintsProcess', 'myComplaintsDone'
        ));
    }

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
}

<?php

namespace App\Http\Controllers;

use App\Models\ScheduledMaintenance;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledMaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = ScheduledMaintenance::with(['technician', 'creator'])
            ->orderByRaw("FIELD(status, 'scheduled', 'in_progress', 'done', 'cancelled')")
            ->latest()
            ->paginate(10);

        return view('admin.scheduled-maintenance.index', compact('maintenances'));
    }

    public function create()
    {
        $technicians = Technician::where('status', 'Available')->get();
        return view('admin.scheduled-maintenance.create', compact('technicians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'facility_type' => 'required|in:Fasilitas Umum,Fasilitas Sosial,Infrastruktur,Lainnya',
            'location' => 'nullable|string',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date',
        ]);

        ScheduledMaintenance::create([
            'title' => $request->title,
            'description' => $request->description,
            'facility_type' => $request->facility_type,
            'location' => $request->location,
            'technician_id' => $request->technician_id,
            'scheduled_date' => $request->scheduled_date,
            'status' => 'scheduled',
            'created_by' => Auth::id(),
        ]);

        if ($request->technician_id) {
            Technician::where('id', $request->technician_id)->update(['status' => 'Busy']);
        }

        return redirect()->route('admin.scheduled-maintenance.index')
            ->with('success', 'Jadwal pemeliharaan berhasil dibuat.');
    }

    public function show(ScheduledMaintenance $scheduledMaintenance)
    {
        $maintenance = $scheduledMaintenance->load(['technician', 'creator']);
        return view('admin.scheduled-maintenance.show', compact('maintenance'));
    }

    public function edit(ScheduledMaintenance $scheduledMaintenance)
    {
        $technicians = Technician::all();
        return view('admin.scheduled-maintenance.edit', compact('scheduledMaintenance', 'technicians'));
    }

    public function update(Request $request, ScheduledMaintenance $scheduledMaintenance)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'facility_type' => 'required|in:Fasilitas Umum,Fasilitas Sosial,Infrastruktur,Lainnya',
            'location' => 'nullable|string',
            'technician_id' => 'nullable|exists:technicians,id',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:scheduled,in_progress,done,cancelled',
            'notes' => 'nullable|string',
        ]);

        $scheduledMaintenance->update($request->all());

        if ($request->status === 'done' && !$scheduledMaintenance->completion_date) {
            $scheduledMaintenance->update(['completion_date' => now()]);
        }

        return redirect()->route('admin.scheduled-maintenance.index')
            ->with('success', 'Jadwal pemeliharaan diperbarui.');
    }

    public function destroy(ScheduledMaintenance $scheduledMaintenance)
    {
        if ($scheduledMaintenance->technician_id) {
            Technician::where('id', $scheduledMaintenance->technician_id)->update(['status' => 'Available']);
        }
        $scheduledMaintenance->delete();

        return redirect()->route('admin.scheduled-maintenance.index')
            ->with('success', 'Jadwal pemeliharaan dihapus.');
    }
}

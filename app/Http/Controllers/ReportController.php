<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Models\MaintenanceOrder;
use App\Models\Ownership;
use App\Models\Technician;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan ini buat query statistik
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    // 1. REKAPITULASI KELUHAN
    public function complaintsPdf(Request $request)
    {
        $orders = MaintenanceOrder::with(['ownership.unit', 'technician'])
            ->orderBy('complaint_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.complaints', compact('orders'));

        return $pdf->stream('Laporan-Keluhan-Warga.pdf');
    }

    // 2. KINERJA TEKNISI
    public function techniciansPdf()
    {
        $technicians = Technician::withCount(['maintenanceOrders as total_jobs' => function ($query) {
            $query->where('status', 'done');
        }])->get();

        $pdf = Pdf::loadView('admin.reports.pdf.technicians', compact('technicians'));

        return $pdf->stream('Laporan-Data-Teknisi.pdf');
    }

    // 3. ANALISIS KATEGORI KERUSAKAN (STATISTIK)
    public function categoryStatsPdf()
    {
        // Hitung jumlah kasus berdasarkan Spesialisasi Teknisi
        // Contoh: Listrik (5), Air (2), dll.
        $stats = MaintenanceOrder::join('technicians', 'maintenance_orders.technician_id', '=', 'technicians.id')
            ->select('technicians.specialty', DB::raw('count(*) as total'))
            ->groupBy('technicians.specialty')
            ->get();

        $totalCases = $stats->sum('total');

        $pdf = Pdf::loadView('admin.reports.pdf.category_stats', compact('stats', 'totalCases'));

        return $pdf->stream('Laporan-Statistik-Kerusakan.pdf');
    }

    // 4. KINERJA KECEPATAN RESPON (SLA)
    public function slaPdf()
    {
        // Ambil data yang sudah selesai (Done)
        $orders = MaintenanceOrder::with(['ownership.customer', 'technician'])
            ->where('status', 'done')
            ->orderBy('complaint_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.sla', compact('orders'));

        return $pdf->stream('Laporan-SLA-Respon.pdf');
    }

    // 5. INDEKS KEPUASAN PELANGGAN (FEEDBACK)
    public function ratingsPdf()
    {
        // Ambil data yang sudah ada ratingnya
        $reviews = MaintenanceOrder::with(['ownership.customer', 'ownership.unit'])
            ->whereNotNull('rating')
            ->orderBy('rating', 'desc') // Rating tertinggi diatas
            ->get();

        // Hitung rata-rata rating
        $averageRating = $reviews->avg('rating');

        $pdf = Pdf::loadView('admin.reports.pdf.ratings', compact('reviews', 'averageRating'));

        return $pdf->stream('Laporan-Kepuasan-Pelanggan.pdf');
    }

    // 6. LAPORAN PENDAPATAN PERBAIKAN (FINANSIAL)
    public function financialPdf()
    {
        $orders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer'])
            ->where('cost', '>', 0) // Hanya ambil yang ada harganya (non-garansi)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $orders->where('payment_status', 'Paid')->sum('cost');
        $totalUnpaid = $orders->where('payment_status', 'Unpaid')->sum('cost');

        $pdf = Pdf::loadView('admin.reports.pdf.financial', compact('orders', 'totalRevenue', 'totalUnpaid'));

        return $pdf->stream('Laporan-Pendapatan-Perbaikan.pdf');
    }

    // 7. LAPORAN STATUS GARANSI UNIT
    public function warrantyPdf()
    {
        $ownerships = Ownership::with(['unit', 'customer'])
            ->orderBy('warranty_end_date', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.warranty', compact('ownerships'));

        return $pdf->stream('Laporan-Status-Garansi.pdf');
    }

    // 8. LAPORAN DATA & PENJUALAN UNIT
    public function unitsPdf()
    {
        // Ambil semua unit beserta data kepemilikannya (jika ada)
        $units = \App\Models\Unit::leftJoin('ownerships', function ($join) {
            $join->on('units.id', '=', 'ownerships.unit_id')
                ->where('ownerships.status', '=', 'Active');
        })
            ->leftJoin('customers', 'ownerships.customer_id', '=', 'customers.id')
            ->select('units.*', 'ownerships.purchase_method', 'customers.name as customer_name')
            ->orderBy('units.block')
            ->orderBy('units.number')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.units', compact('units'));
        $pdf->setPaper('A4', 'landscape'); // Format landscape agar tabel muat banyak

        return $pdf->stream('Laporan-Data-Unit.pdf');
    }

    // EXPORT EXCEL NASABAH
    public function customersExcel()
    {
        return Excel::download(new CustomersExport, 'Database-Nasabah-Sekumpul.xlsx');
    }
}

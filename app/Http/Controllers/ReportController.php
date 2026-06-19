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

    private function applyMonthFilter($query, Request $request, $dateColumn = 'complaint_date')
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if ($month && $year) {
            $query->whereMonth($dateColumn, $month)->whereYear($dateColumn, $year);
        } elseif ($month) {
            $query->whereMonth($dateColumn, $month);
        } elseif ($year) {
            $query->whereYear($dateColumn, $year);
        }

        return [$month, $year];
    }

    // 1. REKAPITULASI KELUHAN
    public function complaintsPdf(Request $request)
    {
        $orders = MaintenanceOrder::with(['ownership.unit', 'technician']);

        [$month, $year] = $this->applyMonthFilter($orders, $request);

        $orders = $orders->orderBy('complaint_date', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.complaints', compact('orders', 'month', 'year'));

        return $pdf->stream('Laporan-Keluhan-Warga.pdf');
    }

    // 2. KINERJA TEKNISI
    public function techniciansPdf(Request $request)
    {
        $technicians = Technician::withCount(['maintenanceOrders as total_jobs' => function ($query) use ($request) {
            $query->where('status', 'done');
            $month = $request->input('month');
            $year = $request->input('year');
            if ($month && $year) {
                $query->whereMonth('complaint_date', $month)->whereYear('complaint_date', $year);
            } elseif ($month) {
                $query->whereMonth('complaint_date', $month);
            } elseif ($year) {
                $query->whereYear('complaint_date', $year);
            }
        }])->get();

        $month = $request->input('month');
        $year = $request->input('year');

        $pdf = Pdf::loadView('admin.reports.pdf.technicians', compact('technicians', 'month', 'year'));

        return $pdf->stream('Laporan-Data-Teknisi.pdf');
    }

    // 3. ANALISIS KATEGORI KERUSAKAN (STATISTIK)
    public function categoryStatsPdf(Request $request)
    {
        $stats = MaintenanceOrder::join('technicians', 'maintenance_orders.technician_id', '=', 'technicians.id')
            ->select('technicians.specialty', DB::raw('count(*) as total'));

        [$month, $year] = $this->applyMonthFilter($stats, $request);

        $stats = $stats->groupBy('technicians.specialty')->get();

        $totalCases = $stats->sum('total');

        $pdf = Pdf::loadView('admin.reports.pdf.category_stats', compact('stats', 'totalCases', 'month', 'year'));

        return $pdf->stream('Laporan-Statistik-Kerusakan.pdf');
    }

    // 4. KINERJA KECEPATAN RESPON (SLA)
    public function slaPdf(Request $request)
    {
        $orders = MaintenanceOrder::with(['ownership.customer', 'technician'])
            ->where('status', 'done');

        [$month, $year] = $this->applyMonthFilter($orders, $request);

        $orders = $orders->orderBy('complaint_date', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.sla', compact('orders', 'month', 'year'));

        return $pdf->stream('Laporan-SLA-Respon.pdf');
    }

    // 5. INDEKS KEPUASAN PELANGGAN (FEEDBACK)
    public function ratingsPdf(Request $request)
    {
        $reviews = MaintenanceOrder::with(['ownership.customer', 'ownership.unit'])
            ->whereNotNull('rating');

        [$month, $year] = $this->applyMonthFilter($reviews, $request);

        $reviews = $reviews->orderBy('rating', 'desc')->get();

        $averageRating = $reviews->avg('rating');

        $pdf = Pdf::loadView('admin.reports.pdf.ratings', compact('reviews', 'averageRating', 'month', 'year'));

        return $pdf->stream('Laporan-Kepuasan-Pelanggan.pdf');
    }

    // 6. LAPORAN PENDAPATAN PERBAIKAN (FINANSIAL)
    public function financialPdf(Request $request)
    {
        $orders = MaintenanceOrder::with(['ownership.unit', 'ownership.customer'])
            ->where('cost', '>', 0);

        [$month, $year] = $this->applyMonthFilter($orders, $request, 'created_at');

        $orders = $orders->orderBy('created_at', 'desc')->get();

        $totalRevenue = $orders->where('payment_status', 'Paid')->sum('cost');
        $totalUnpaid = $orders->where('payment_status', 'Unpaid')->sum('cost');

        $pdf = Pdf::loadView('admin.reports.pdf.financial', compact('orders', 'totalRevenue', 'totalUnpaid', 'month', 'year'));

        return $pdf->stream('Laporan-Pendapatan-Perbaikan.pdf');
    }

    // 7. LAPORAN STATUS GARANSI UNIT
    public function warrantyPdf(Request $request)
    {
        $ownerships = Ownership::with(['unit', 'customer']);

        [$month, $year] = $this->applyMonthFilter($ownerships, $request, 'created_at');

        $ownerships = $ownerships->orderBy('warranty_end_date', 'asc')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.warranty', compact('ownerships', 'month', 'year'));

        return $pdf->stream('Laporan-Status-Garansi.pdf');
    }

    // 8. LAPORAN DATA & STATUS UNIT
    public function unitsPdf(Request $request)
    {
        $units = \App\Models\Unit::leftJoin('ownerships', function ($join) {
            $join->on('units.id', '=', 'ownerships.unit_id')
                ->where('ownerships.status', '=', 'Active');
        })
            ->leftJoin('customers', 'ownerships.customer_id', '=', 'customers.id')
            ->select('units.*', 'ownerships.purchase_method', 'customers.name as customer_name');

        [$month, $year] = $this->applyMonthFilter($units, $request, 'ownerships.created_at');

        $units = $units->orderBy('units.block')->orderBy('units.number')->get();

        $pdf = Pdf::loadView('admin.reports.pdf.units', compact('units', 'month', 'year'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan-Data-Unit.pdf');
    }

    // EXPORT EXCEL NASABAH
    public function customersExcel()
    {
        return Excel::download(new CustomersExport, 'Database-Nasabah-Sekumpul.xlsx');
    }
}

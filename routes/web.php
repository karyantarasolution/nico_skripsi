<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceOrderController;
use App\Http\Controllers\OwnershipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairPriceController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduledMaintenanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- AREA ADMIN (Staff) ---
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::resource('users', UserController::class);
        Route::resource('units', UnitController::class);
        Route::resource('technicians', TechnicianController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('ownerships', OwnershipController::class);
        Route::resource('prices', RepairPriceController::class);

        // MAINTENANCE (ADMIN)
        Route::get('maintenance', [MaintenanceOrderController::class, 'indexAdmin'])->name('maintenance.index');
        Route::get('maintenance/{id}', [MaintenanceOrderController::class, 'showAdmin'])->name('maintenance.show');
        Route::put('maintenance/{id}/assign', [MaintenanceOrderController::class, 'updateStatus'])->name('maintenance.update');

        Route::put('maintenance/{id}/mark-as-paid', [MaintenanceOrderController::class, 'markAsPaid'])
            ->name('maintenance.paid');

        // APPROVE / REJECT ESTIMASI BIAYA
        Route::put('maintenance/{id}/approve-estimate', [MaintenanceOrderController::class, 'approveEstimate'])
            ->name('maintenance.approve-estimate');
        Route::put('maintenance/{id}/reject-estimate', [MaintenanceOrderController::class, 'rejectEstimate'])
            ->name('maintenance.reject-estimate');

        // SMART ASSIGN
        Route::post('maintenance/{id}/smart-assign', [MaintenanceOrderController::class, 'smartAssign'])
            ->name('maintenance.smart-assign');

        // SCHEDULED MAINTENANCE
        Route::resource('scheduled-maintenance', ScheduledMaintenanceController::class);

        // REPORT CENTER
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

        Route::get('reports/print/complaints', [\App\Http\Controllers\ReportController::class, 'complaintsPdf'])->name('reports.complaints.pdf');
        Route::get('reports/print/technicians', [\App\Http\Controllers\ReportController::class, 'techniciansPdf'])->name('reports.technicians.pdf');
        Route::get('reports/print/category-stats', [\App\Http\Controllers\ReportController::class, 'categoryStatsPdf'])->name('reports.category.pdf');
        Route::get('reports/print/sla', [\App\Http\Controllers\ReportController::class, 'slaPdf'])->name('reports.sla.pdf');
        Route::get('reports/print/ratings', [\App\Http\Controllers\ReportController::class, 'ratingsPdf'])->name('reports.ratings.pdf');
        Route::get('reports/print/financial', [\App\Http\Controllers\ReportController::class, 'financialPdf'])->name('reports.financial.pdf');
        Route::get('reports/print/warranty', [\App\Http\Controllers\ReportController::class, 'warrantyPdf'])->name('reports.warranty.pdf');
        Route::get('reports/print/units', [\App\Http\Controllers\ReportController::class, 'unitsPdf'])->name('reports.units.pdf');
        Route::get('reports/excel/customers', [\App\Http\Controllers\ReportController::class, 'customersExcel'])->name('reports.customers.excel');
    });

    // --- AREA TEKNISI ---
    Route::get('/technician/maintenance', [MaintenanceOrderController::class, 'indexTechnician'])
        ->name('technician.maintenance.index');

    Route::get('/technician/maintenance/{id}', [MaintenanceOrderController::class, 'showTechnician'])
        ->name('technician.maintenance.show');

    Route::put('/technician/maintenance/{id}', [MaintenanceOrderController::class, 'technicianUpdateStatus'])
        ->name('technician.maintenance.update');

    // TEKNISI: INPUT ESTIMASI BIAYA
    Route::post('/technician/maintenance/{id}/estimate', [MaintenanceOrderController::class, 'technicianInputEstimate'])
        ->name('technician.maintenance.estimate');

    // --- AREA NASABAH & WARGA ---
    Route::get('/my-assets', [OwnershipController::class, 'myAssets'])->name('my.assets');

    Route::get('/complaints', [MaintenanceOrderController::class, 'indexUser'])->name('complaints.index');
    Route::get('/complaints/create', [MaintenanceOrderController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [MaintenanceOrderController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{id}', [MaintenanceOrderController::class, 'showUser'])->name('complaints.show');
    Route::put('/complaints/{id}/rate', [MaintenanceOrderController::class, 'rateService'])->name('complaints.rate');

    // WARGA: KONFIRMASI BIAYA
    Route::put('/complaints/{id}/confirm-cost', [MaintenanceOrderController::class, 'confirmCost'])
        ->name('complaints.confirm-cost');

    Route::get('/complaints/{id}/print', [MaintenanceOrderController::class, 'printTicket'])->name('complaints.print');
});

require __DIR__.'/auth.php';

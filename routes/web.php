<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaybillController;
use App\Livewire\Waybill\AuditorApprovals;
use App\Livewire\Waybill\CreateWaybill;
use App\Livewire\Waybill\DistOfficerWaybills;
use App\Livewire\Waybill\EditWaybill;
use App\Livewire\Waybill\SupplyChainApprovals;
use App\Livewire\Waybill\WarehouseApprovals;
use App\Livewire\Waybill\WaybillApprovals;
use App\Livewire\Waybill\WaybillApproved;
use App\Livewire\Waybill\WaybillDetails;
use App\Livewire\Waybill\WaybillList;
use App\Livewire\Waybill\WaybillRejected;
use App\Livewire\Waybill\WaybillReports;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== WAYBILL ROUTES =====
    // Static routes FIRST (specific paths)
    Route::get('/waybills/create', CreateWaybill::class)->name('waybills.create');

    // ===== ROLE-BASED ROUTES =====
    Route::get('/waybills/dist-officer', DistOfficerWaybills::class)->name('waybills.dist-officer');
    Route::get('/waybills/warehouse', WarehouseApprovals::class)->name('waybills.warehouse');
    Route::get('/waybills/auditor', AuditorApprovals::class)->name('waybills.auditor');
    Route::get('/waybills/supply-chain', SupplyChainApprovals::class)->name('waybills.supply-chain');

    // Generic views
    Route::get('/waybills/approvals', WaybillApprovals::class)->name('waybills.approvals');
    Route::get('/waybills/approved', WaybillApproved::class)->name('waybills.approved');
    Route::get('/waybills/rejected', WaybillRejected::class)->name('waybills.rejected');
    Route::get('/waybills/reports', WaybillReports::class)->name('waybills.reports');

    // Dynamic routes LAST (with parameters)
    Route::get('/waybills', WaybillList::class)->name('waybills.index');
    Route::get('/waybills/{waybill}', WaybillDetails::class)->name('waybills.show');
    Route::get('/waybills/{waybill}/edit', EditWaybill::class)->name('waybills.edit');

    // ✅ NEW PREVIEW ROUTE (Allowed at every stage)
    Route::get('/waybills/{waybill}/preview', [WaybillController::class, 'preview'])->name('waybills.preview');

    // ✅ LOCKED PDF ROUTE (Only works if isFullyApproved() returns true)
    Route::get('/waybills/{waybill}/pdf', [WaybillController::class, 'generatePdf'])->name('waybills.pdf');
});

// Auth routes
require __DIR__.'/auth.php';

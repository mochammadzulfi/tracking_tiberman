<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DeliveryProofController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Shipments routes
Route::middleware(['auth', 'role:view_only,creator,admin,superuser'])->group(function () {
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/map', [ShipmentController::class, 'map'])->name('shipments.map');
});

// Creator + Superuser: create & edit
Route::middleware(['auth', 'role:creator,superuser'])->group(function () {
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
});

// Admin + Superuser: scan QR
Route::middleware(['auth', 'role:admin,superuser'])->group(function () {
    Route::post('/shipments/{shipment}/scan', [ShipmentController::class, 'scan'])->name('shipments.scan');
});

// Delivery proofs: creator/admin/superuser can upload
Route::middleware(['auth', 'role:creator,admin,superuser'])->group(function () {
    Route::get('/shipments/{shipment}/proof/create', [DeliveryProofController::class, 'create'])->name('proofs.create');
    Route::post('/shipments/{shipment}/proof', [DeliveryProofController::class, 'store'])->name('proofs.store');
});

require __DIR__ . '/auth.php';

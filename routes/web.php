<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DeliveryProofController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\AuditController;
use Illuminate\Support\Facades\Route;

// Dashboard
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
Route::middleware('auth')->group(function () {

    // Creator & Superuser: create & store
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->name('shipments.store');

    // Index & Map (semua role bisa lihat)
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}/map', [ShipmentController::class, 'showMap'])->name('shipments.show.map');


    // Show detail (semua role bisa lihat)
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');

    // Creator & Superuser: edit, update, delete
    Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');

    // Admin & Superuser: scan QR
    Route::post('/shipments/{shipment}/scan', [ShipmentController::class, 'scan'])->name('shipments.scan');

    // Delivery proofs: creator/admin/superuser can upload
    Route::get('/shipments/{shipment}/proof/create', [DeliveryProofController::class, 'create'])->name('proofs.create');
    Route::post('/shipments/{shipment}/proof', [DeliveryProofController::class, 'store'])->name('proofs.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index'); // list group
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit'); // edit group
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update'); // update group
});

// Superuser only
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/fleets', [FleetController::class, 'index'])->name('fleets.index');
    Route::get('/fleets/create', [FleetController::class, 'create'])->name('fleets.create');
    Route::post('/fleets', [FleetController::class, 'store'])->name('fleets.store');
    Route::get('/fleets/{fleet}/edit', [FleetController::class, 'edit'])->name('fleets.edit');
    Route::put('/fleets/{fleet}', [FleetController::class, 'update'])->name('fleets.update');
    Route::delete('/fleets/{fleet}', [FleetController::class, 'destroy'])->name('fleets.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs', [AuditController::class, 'index'])->name('audit.index');
});

Route::middleware(['auth'])->group(function () {});

require __DIR__ . '/auth.php';

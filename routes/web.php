<?php

use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ──────────────────────────────────────────────────────────────
// Secure File Access (auth only, policy di controller)
// ──────────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // Akses file bukti pembayaran secara aman
    Route::get('payments/{payment}/proof', [PaymentController::class, 'proofFile'])
        ->name('payments.proof');
});

// ──────────────────────────────────────────────────────────────
// Customer Routes
// ──────────────────────────────────────────────────────────────

Route::middleware(['auth', 'customer'])->group(function () {

    // ── Order Routes ─────────────────────────────────────────
    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::post('orders/{order}/upload-design', [OrderController::class, 'uploadDesign'])
        ->name('orders.upload-design');

    // ── Payment Routes ───────────────────────────────────────
    Route::get('orders/{order}/payments/create', [PaymentController::class, 'create'])
        ->name('orders.payments.create');

    Route::post('orders/{order}/payments', [PaymentController::class, 'store'])
        ->name('orders.payments.store');

    // ── Appointment Routes ───────────────────────────────────
    Route::get('orders/{order}/appointments/create', [AppointmentController::class, 'create'])
        ->name('orders.appointments.create');

    Route::post('orders/{order}/appointments', [AppointmentController::class, 'store'])
        ->name('orders.appointments.store');
});

// ──────────────────────────────────────────────────────────────
// Admin Routes
// ──────────────────────────────────────────────────────────────

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Order Routes ─────────────────────────────────────────
    Route::resource('orders', AdminOrderController::class)
        ->only(['index', 'show']);

    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('orders.update-status');

    Route::patch('orders/{order}/material', [AdminOrderController::class, 'updateMaterial'])
        ->name('orders.update-material');

    // ── Payment Routes ───────────────────────────────────────
    Route::get('payments', [AdminPaymentController::class, 'index'])
        ->name('payments.index');

    Route::get('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])
        ->name('payments.verify');

    Route::patch('payments/{payment}/approve', [AdminPaymentController::class, 'approve'])
        ->name('payments.approve');

    Route::patch('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])
        ->name('payments.reject');

    // ── Appointment Routes ───────────────────────────────────
    Route::get('appointments', [AdminAppointmentController::class, 'index'])
        ->name('appointments.index');

    Route::patch('appointments/{appointment}/confirm', [AdminAppointmentController::class, 'confirm'])
        ->name('appointments.confirm');

    Route::patch('appointments/{appointment}/complete', [AdminAppointmentController::class, 'complete'])
        ->name('appointments.complete');
});


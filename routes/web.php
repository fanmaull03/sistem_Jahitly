<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Appointments\Index as AdminAppointmentsIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Orders\Index as AdminOrdersIndex;
use App\Livewire\Admin\Orders\Show as AdminOrdersShow;
use App\Livewire\Admin\Payments\Index as AdminPaymentsIndex;
use App\Livewire\Admin\Queue\Index as AdminQueueIndex;
use App\Livewire\Customer\Orders\Create as CustomerOrdersCreate;
use App\Livewire\Customer\Orders\Index as CustomerOrdersIndex;
use App\Livewire\Customer\Orders\Show as CustomerOrdersShow;
use App\Livewire\Customer\Payments\Create as CustomerPaymentsCreate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

    // ── Order Routes (Livewire) ──────────────────────────────
    Route::get('orders', CustomerOrdersIndex::class)
        ->name('orders.index');

    Route::get('orders/create', CustomerOrdersCreate::class)
        ->name('orders.create');

    Route::get('orders/{order}', CustomerOrdersShow::class)
        ->name('orders.show');

    // ── Payment Routes (Livewire) ────────────────────────────
    Route::get('orders/{order}/payments/create', CustomerPaymentsCreate::class)
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

    Route::get('dashboard', AdminDashboard::class)
        ->name('dashboard');

    Route::get('orders', AdminOrdersIndex::class)
        ->name('orders.index');

    Route::get('orders/{order}', AdminOrdersShow::class)
        ->name('orders.show');

    Route::get('queue', AdminQueueIndex::class)
        ->name('queue.index');

    Route::get('appointments', AdminAppointmentsIndex::class)
        ->name('appointments.index');

    Route::get('payments', AdminPaymentsIndex::class)
        ->name('payments.index');
});

require __DIR__.'/auth.php';

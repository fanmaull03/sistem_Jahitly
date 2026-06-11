<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\Appointments\Index as AdminAppointmentsIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Fabrics\Index as AdminFabricsIndex;
use App\Livewire\Admin\Orders\Index as AdminOrdersIndex;
use App\Livewire\Admin\Orders\Show as AdminOrdersShow;
use App\Livewire\Admin\Payments\Index as AdminPaymentsIndex;
use App\Livewire\Admin\Queue\Index as AdminQueueIndex;
use App\Livewire\Customer\Appointments\Create as CustomerAppointmentsCreate;
use App\Livewire\Customer\Orders\CancelOrder as CustomerOrdersCancelOrder;
use App\Livewire\Customer\Orders\Create as CustomerOrdersCreate;
use App\Livewire\Customer\Orders\Index as CustomerOrdersIndex;
use App\Livewire\Customer\Orders\Show as CustomerOrdersShow;
use App\Livewire\Customer\Payments\Create as CustomerPaymentsCreate;
use App\Livewire\Customer\Payments\History as CustomerPaymentsHistory;
use App\Livewire\Customer\Payments\RejectedPaymentHandler as CustomerPaymentsRejectedPaymentHandler;
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
    Route::post('/testimonials', [\App\Http\Controllers\TestimonialController::class, 'store'])->name('testimonials.store');
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

    Route::get('orders/{order}/cancel', CustomerOrdersCancelOrder::class)
        ->name('orders.cancel');

    // ── Payment Routes (Livewire) ────────────────────────────
    Route::get('payments', CustomerPaymentsHistory::class)
        ->name('payments.index');

    Route::get('orders/{order}/payments/create', CustomerPaymentsCreate::class)
        ->name('payments.create');

    Route::get('payments/history', CustomerPaymentsHistory::class)
        ->name('payments.history');

    Route::get('orders/{order}/payments/history', CustomerPaymentsHistory::class)
        ->name('payments.history.order');

    Route::get('payments/{payment}/rejected', CustomerPaymentsRejectedPaymentHandler::class)
        ->name('payments.rejected');

    Route::post('orders/{order}/payments', [PaymentController::class, 'store'])
        ->name('payments.store');

    // ── Appointment Routes (Livewire) ────────────────────────
    Route::get('orders/{order}/appointments/create', CustomerAppointmentsCreate::class)
        ->name('orders.appointments.create');
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

    Route::get('fabrics', AdminFabricsIndex::class)
        ->name('fabrics.index');
});

require __DIR__.'/auth.php';

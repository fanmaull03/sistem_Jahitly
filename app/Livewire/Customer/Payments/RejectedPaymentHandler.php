<?php

namespace App\Livewire\Customer\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RejectedPaymentHandler extends Component
{
    public Payment $payment;

    public function mount(Payment $payment): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($payment->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pembayaran ini.');
        }

        if ($payment->status !== 'ditolak') {
            abort(404, 'Pembayaran ini bukan pembayaran yang ditolak.');
        }

        $this->payment = $payment->load(['order', 'order.service']);
    }

    /**
     * Redirect ke halaman pembuatan pembayaran baru
     */
    public function retryPayment(): void
    {
        $this->redirect(
            route('payments.create', ['order' => $this->payment->order_id]),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.customer.payments.rejected-payment-handler')
            ->layout('layouts.app');
    }
}

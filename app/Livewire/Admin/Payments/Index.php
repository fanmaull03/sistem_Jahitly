<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
use App\Notifications\PaymentStatusUpdated;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public bool $showProofModal = false;
    public ?int $activePaymentId = null;
    public bool $showRejectForm = false;
    public string $rejectionNote = '';

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function getPaymentsProperty()
    {
        return Payment::with(['order.service', 'customer'])
            ->where('status', 'menunggu_verifikasi')
            ->oldest()
            ->paginate(15);
    }

    public function openProof(int $paymentId): void
    {
        $this->activePaymentId = $paymentId;
        $this->showProofModal = true;
        $this->showRejectForm = false;
        $this->rejectionNote = '';
    }

    public function closeProof(): void
    {
        $this->showProofModal = false;
        $this->activePaymentId = null;
        $this->showRejectForm = false;
        $this->rejectionNote = '';
    }

    public function startReject(): void
    {
        $this->showRejectForm = true;
    }

    public function cancelReject(): void
    {
        $this->showRejectForm = false;
        $this->rejectionNote = '';
    }

    public function approvePayment(int $paymentId): void
    {
        $payment = Payment::with(['order.payments', 'customer'])->findOrFail($paymentId);

        if ($payment->status !== 'menunggu_verifikasi') {
            session()->flash('error', 'Pembayaran sudah diproses.');
            return;
        }

        $payment->update([
            'status' => 'terverifikasi',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_note' => null,
        ]);

        if ($payment->customer) {
            $payment->customer->notify(new PaymentStatusUpdated($payment, $payment->status));
        }

        $order = $payment->order;
        $totalVerified = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');
        $estimatedPrice = (float) $order->estimated_price;

        $message = 'Pembayaran berhasil diverifikasi.';

        if ($estimatedPrice > 0 && $totalVerified >= $estimatedPrice) {
            $message .= ' Pesanan ' . $order->order_number . ' sudah LUNAS.';
        } else {
            $kekurangan = max(0, $estimatedPrice - $totalVerified);
            $message .= ' Sisa pembayaran pesanan ' . $order->order_number
                . ': Rp ' . number_format($kekurangan, 0, ',', '.') . '.';
        }

        $this->closeProof();
        session()->flash('success', $message);
    }

    public function rejectPayment(int $paymentId): void
    {
        $this->validate([
            'rejectionNote' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'rejectionNote.required' => 'Alasan penolakan harus diisi.',
            'rejectionNote.min' => 'Alasan penolakan minimal 10 karakter.',
            'rejectionNote.max' => 'Alasan penolakan maksimal 2000 karakter.',
        ]);

        $payment = Payment::with(['order', 'customer'])->findOrFail($paymentId);

        if ($payment->status !== 'menunggu_verifikasi') {
            session()->flash('error', 'Pembayaran sudah diproses.');
            return;
        }

        $payment->update([
            'status' => 'ditolak',
            'rejection_note' => $this->rejectionNote,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if ($payment->customer) {
            $payment->customer->notify(new PaymentStatusUpdated($payment, $payment->status));
        }

        $this->closeProof();
        session()->flash('success', 'Pembayaran ditolak. Customer akan melihat alasan penolakan.');
    }

    public function getActivePaymentProperty(): ?Payment
    {
        if (! $this->activePaymentId) {
            return null;
        }

        return Payment::with(['order.service', 'customer'])->find($this->activePaymentId);
    }

    public function render(): View
    {
        return view('livewire.admin.payments.index', [
            'payments' => $this->payments,
            'activePayment' => $this->activePayment,
        ])->layout('layouts.admin');
    }
}

<?php

namespace App\Livewire\Admin\Payments;

use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Notifications\OrderStatusUpdated;
use App\Notifications\PaymentStatusUpdated;
use App\Services\OrderBusinessRulesService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?int $activePaymentId = null;
    public string $rejectionNote = '';

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function openProof(int $paymentId): void
    {
        $this->activePaymentId = $paymentId;
        $this->rejectionNote = '';
        $this->resetValidation();
    }

    public function closeProof(): void
    {
        $this->activePaymentId = null;
        $this->rejectionNote = '';
        $this->resetValidation();
    }

    public function approvePayment(int $paymentId): void
    {
        $payment = Payment::with(['order.payments', 'order.service', 'customer'])->findOrFail($paymentId);

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
        $totalVerified = $order->payments->fresh()->where('status', 'terverifikasi')->sum('amount');

        // Reload order to get fresh data
        $order = $order->fresh(['payments', 'service', 'appointment']);
        $totalVerified = $order->payments->where('status', 'terverifikasi')->sum('amount');
        $estimatedPrice = (float) $order->estimated_price;

        $message = 'Pembayaran berhasil diverifikasi.';

        if ($estimatedPrice > 0 && $totalVerified >= $estimatedPrice) {
            $message .= ' Pesanan ' . $order->order_number . ' sudah LUNAS.';
        } else {
            $kekurangan = max(0, $estimatedPrice - $totalVerified);
            $message .= ' Sisa pembayaran pesanan ' . $order->order_number
                . ': Rp ' . number_format($kekurangan, 0, ',', '.') . '.';
        }

        // ── Auto-advance logic berdasarkan payment type ──

        // DP terverifikasi → cek apakah bisa masuk antrian
        if ($payment->payment_type === 'dp') {
            if (in_array($order->status, ['menunggu_dp', 'menunggu_fitting'])) {
                // Cek bahan
                if ($order->material_source === 'customer' || $order->material_status === 'ready') {
                    // Bahan ready → masuk antrian
                    $order->update([
                        'status' => 'dalam_antrian',
                        'material_status' => $order->material_status ?? 'ready',
                    ]);

                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'status' => 'dalam_antrian',
                        'changed_by' => auth()->id(),
                        'notes' => 'DP terverifikasi, bahan siap. Pesanan masuk antrian produksi.',
                    ]);

                    if ($order->customer) {
                        $order->customer->notify(new OrderStatusUpdated(
                            $order,
                            'Pesanan #' . $order->order_number . ' masuk antrian produksi.'
                        ));
                    }

                    $message .= ' Pesanan otomatis masuk antrian produksi.';
                } else {
                    // Bahan belum ready → menunggu bahan
                    $order->update(['status' => 'menunggu_bahan']);

                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'status' => 'menunggu_bahan',
                        'changed_by' => auth()->id(),
                        'notes' => 'DP terverifikasi. Menunggu kesiapan bahan.',
                    ]);

                    $message .= ' Pesanan menunggu kesiapan bahan.';
                }
            }
        }

        // Pelunasan terverifikasi → siap diambil
        if ($payment->payment_type === 'pelunasan' && $order->status === 'selesai_produksi') {
            $check = app(OrderBusinessRulesService::class)->canMarkReadyForPickup($order->fresh('payments'));

            if ($check['can_proceed']) {
                $order->update(['status' => 'siap_diambil']);

                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'siap_diambil',
                    'changed_by' => auth()->id(),
                    'notes' => 'Pelunasan terverifikasi. Pesanan siap diambil.',
                ]);

                if ($order->customer) {
                    $order->customer->notify(new OrderStatusUpdated(
                        $order,
                        'Pesanan #' . $order->order_number . ' siap untuk diambil!'
                    ));
                }

                $message .= ' Pesanan siap diambil.';
            }
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
        if (!$this->activePaymentId) {
            return null;
        }

        return Payment::with(['order.service', 'customer'])->find($this->activePaymentId);
    }

    public function getPaymentsProperty()
    {
        return Payment::with(['order.service', 'customer'])
            ->latest()
            ->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.admin.payments.index', [
            'payments' => $this->payments,
            'activePayment' => $this->activePayment,
        ])->layout('layouts.admin');
    }
}

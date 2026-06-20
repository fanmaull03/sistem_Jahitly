<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CancelOrder extends Component
{
    public Order $order;
    public string $cancellationReason = '';
    public bool $showConfirmation = false;

    public function mount(Order $order): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order->load(['service', 'payments']);

        // Validasi apakah order dapat dibatalkan
        if (! $this->canCancelOrder()) {
            abort(403, 'Pesanan ini tidak dapat dibatalkan.');
        }
    }

    /**
     * Cek apakah order dapat dibatalkan
     */
    public function canCancelOrder(): bool
    {
        // Status yang tidak dapat dibatalkan
        $nonCancellableStatuses = ['dijahit', 'selesai_produksi', 'siap_diambil', 'selesai', 'ditolak', 'dibatalkan'];

        if (in_array($this->order->status, $nonCancellableStatuses)) {
            return false;
        }

        // Jika ada pembayaran terverifikasi, tidak dapat dibatalkan
        if ($this->order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, array<int|string, string>>
     */
    protected function rules(): array
    {
        return [
            'cancellationReason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'cancellationReason.required' => 'Alasan pembatalan harus diisi.',
            'cancellationReason.min' => 'Alasan pembatalan minimal 10 karakter.',
            'cancellationReason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ];
    }

    public function submitCancellation()
    {
        $this->validate();

        // Perbarui status order ke 'dibatalkan'
        $this->order->update([
            'status' => 'dibatalkan',
            'cancelled_at' => now(),
            'cancellation_reason' => $this->cancellationReason,
        ]);

        // Tambahkan log status
        $this->order->statusLogs()->create([
            'status' => 'dibatalkan',
            'changed_by' => auth()->id(),
            'notes' => 'Dibatalkan oleh customer: ' . $this->cancellationReason,
        ]);

        // Redirect dengan pesan sukses
        session()->flash('success', 'Pesanan berhasil dibatalkan.');
        
        return $this->redirect(
            route('orders.index'),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.customer.orders.cancel-order')
            ->layout('layouts.app');
    }
}

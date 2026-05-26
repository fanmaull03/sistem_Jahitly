<?php

namespace App\Livewire\Customer\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public ?Order $order = null;
    public string $statusFilter = 'all';

    public function mount(?Order $order = null): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order && $order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order;
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * @return array<string, string>
     */
    public function getStatusesProperty(): array
    {
        return [
            'all' => 'Semua Status',
            'belum_bayar' => 'Belum Bayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Ditolak',
            'terverifikasi' => 'Terverifikasi',
        ];
    }

    public function getPaymentsProperty()
    {
        $query = auth()->user()
            ->payments()
            ->with(['order', 'order.service']);

        if ($this->order) {
            $query->where('order_id', $this->order->id);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'terverifikasi' => 'bg-green-100 text-green-800 border-green-200',
            'ditolak' => 'bg-red-100 text-red-800 border-red-200',
            'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'belum_bayar' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'belum_bayar' => 'Belum Bayar',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel(string $method): string
    {
        return match ($method) {
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            'cash' => 'Tunai',
            default => 'Tidak Diketahui',
        };
    }

    public function render(): View
    {
        return view('livewire.customer.payments.history', [
            'payments' => $this->payments,
            'statuses' => $this->statuses,
        ])->layout('layouts.app');
    }
}

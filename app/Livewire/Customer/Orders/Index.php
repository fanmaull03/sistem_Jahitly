<?php

namespace App\Livewire\Customer\Orders;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
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
            'all' => 'Semua',
            'menunggu' => 'Menunggu',
            'proses' => 'Sedang Proses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    public function getOrdersProperty()
    {
        $query = auth()->user()
            ->orders()
            ->with(['service', 'payments'])
            ->latest();

        if ($this->statusFilter === 'menunggu') {
            $query->whereIn('status', ['menunggu_konfirmasi', 'menunggu_fitting', 'menunggu_dp', 'menunggu_bahan']);
        } elseif ($this->statusFilter === 'proses') {
            $query->whereIn('status', ['dalam_antrian', 'dijahit', 'selesai_produksi', 'siap_diambil']);
        } elseif ($this->statusFilter === 'selesai') {
            $query->where('status', 'selesai');
        } elseif ($this->statusFilter === 'ditolak') {
            $query->where('status', 'ditolak');
        } elseif ($this->statusFilter === 'dibatalkan') {
            $query->where('status', 'dibatalkan');
        }

        return $query->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.customer.orders.index', [
            'orders' => $this->orders,
            'statuses' => $this->statuses,
        ])->layout('layouts.app');
    }
}

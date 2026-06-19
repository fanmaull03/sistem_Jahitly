<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    public string $statusFilter = 'all';
    public string $serviceTypeFilter = 'all';

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingServiceTypeFilter(): void
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
            'menunggu_appointment' => 'Menunggu Appointment',
            'menunggu_bahan' => 'Menunggu Bahan',
            'diproses' => 'Diproses',
            'dijahit' => 'Dijahit',
            'finishing' => 'Finishing',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getServiceTypesProperty(): array
    {
        return [
            'all' => 'Semua Layanan',
            'vermak' => 'Vermak',
            'seragam' => 'Seragam',
            'custom' => 'Custom',
        ];
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['customer', 'service', 'payments'])->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->serviceTypeFilter !== 'all') {
            $query->whereHas('service', function ($serviceQuery) {
                $serviceQuery->where('type', $this->serviceTypeFilter);
            });
        }

        if ($this->search !== '') {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('order_number', 'like', $searchTerm)
                    ->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                        $customerQuery->where('name', 'like', $searchTerm);
                    });
            });
        }

        return $query->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.admin.orders.index', [
            'orders' => $this->orders,
            'statuses' => $this->statuses,
            'serviceTypes' => $this->serviceTypes,
        ])->layout('layouts.admin');
    }
}

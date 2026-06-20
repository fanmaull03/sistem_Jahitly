<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;
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
        if (!auth()->check() || !auth()->user()->isAdmin()) {
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
     * Quick accept order from list.
     */
    public function quickAccept(int $orderId): void
    {
        $order = Order::with(['service', 'customer'])->findOrFail($orderId);

        if ($order->status !== 'menunggu_konfirmasi') {
            session()->flash('error', 'Pesanan tidak sedang menunggu konfirmasi.');
            return;
        }

        $requiresFitting = in_array($order->service->type, ['custom', 'seragam'], true);
        $newStatus = $requiresFitting ? 'menunggu_fitting' : 'menunggu_dp';

        $order->update(['status' => $newStatus]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => auth()->id(),
            'notes' => 'Pesanan diterima oleh admin.',
        ]);

        if ($order->customer) {
            $msg = $requiresFitting
                ? 'Pesanan #' . $order->order_number . ' diterima. Silakan atur jadwal fitting.'
                : 'Pesanan #' . $order->order_number . ' diterima. Silakan lakukan pembayaran DP.';
            $order->customer->notify(new OrderStatusUpdated($order, $msg));
        }

        session()->flash('success', 'Pesanan #' . $order->order_number . ' berhasil diterima.');
    }

    /**
     * @return array<string, string>
     */
    public function getStatusesProperty(): array
    {
        return [
            'all' => 'Semua Status',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'menunggu_fitting' => 'Menunggu Fitting',
            'menunggu_dp' => 'Menunggu DP',
            'menunggu_bahan' => 'Menunggu Bahan',
            'dalam_antrian' => 'Dalam Antrian',
            'dijahit' => 'Dijahit',
            'selesai_produksi' => 'Selesai Produksi',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
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

    public function getPendingCountProperty(): int
    {
        return Order::where('status', 'menunggu_konfirmasi')->count();
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
            'pendingCount' => $this->pendingCount,
        ])->layout('layouts.admin');
    }
}

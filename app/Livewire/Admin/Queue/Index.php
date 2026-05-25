<?php

namespace App\Livewire\Admin\Queue;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    /**
     * @var list<string>
     */
    public array $queueStatuses = ['diproses', 'dijahit', 'finishing'];

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->ensureQueuePositions();
    }

    /**
     * @return array<string, \Illuminate\Support\Collection>
     */
    public function getQueueGroupsProperty(): array
    {
        $orders = Order::with(['customer', 'service'])
            ->whereIn('status', $this->queueStatuses)
            ->orderBy('status')
            ->orderBy('queue_position')
            ->orderBy('created_at')
            ->get()
            ->groupBy('status');

        $groups = [];
        foreach ($this->queueStatuses as $status) {
            $groups[$status] = $orders->get($status, collect());
        }

        return $groups;
    }

    public function moveUp(int $orderId): void
    {
        $this->swapQueuePosition($orderId, 'up');
    }

    public function moveDown(int $orderId): void
    {
        $this->swapQueuePosition($orderId, 'down');
    }

    private function swapQueuePosition(int $orderId, string $direction): void
    {
        $order = Order::findOrFail($orderId);

        if (! in_array($order->status, $this->queueStatuses, true)) {
            return;
        }

        $this->ensureQueuePositionsForStatus($order->status);

        $comparison = $direction === 'up' ? '<' : '>';
        $sortDirection = $direction === 'up' ? 'desc' : 'asc';

        $neighbor = Order::where('status', $order->status)
            ->where('queue_position', $comparison, $order->queue_position)
            ->orderBy('queue_position', $sortDirection)
            ->first();

        if (! $neighbor) {
            return;
        }

        $currentPosition = $order->queue_position;
        $order->update(['queue_position' => $neighbor->queue_position]);
        $neighbor->update(['queue_position' => $currentPosition]);

        session()->flash('success', 'Prioritas antrian berhasil diperbarui.');
    }

    private function ensureQueuePositions(): void
    {
        foreach ($this->queueStatuses as $status) {
            $this->ensureQueuePositionsForStatus($status);
        }
    }

    private function ensureQueuePositionsForStatus(string $status): void
    {
        $orders = Order::where('status', $status)
            ->orderBy('queue_position')
            ->orderBy('created_at')
            ->get();

        $position = 1;
        foreach ($orders as $order) {
            if ($order->queue_position !== $position) {
                $order->update(['queue_position' => $position]);
            }
            $position++;
        }
    }

    public function render(): View
    {
        return view('livewire.admin.queue.index', [
            'queueGroups' => $this->queueGroups,
        ])->layout('layouts.admin');
    }
}

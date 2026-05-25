<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Order $order;
    /**
     * @var array<int, array{key: string, label: string}>
     */
    public array $statusSteps = [
        ['key' => 'menunggu_appointment', 'label' => 'Menunggu Appointment'],
        ['key' => 'menunggu_bahan', 'label' => 'Menunggu Bahan'],
        ['key' => 'diproses', 'label' => 'Diproses'],
        ['key' => 'dijahit', 'label' => 'Dijahit'],
        ['key' => 'finishing', 'label' => 'Finishing'],
        ['key' => 'selesai', 'label' => 'Selesai'],
    ];
    public int $currentStepIndex = 0;
    public int $progressPercent = 0;
    public bool $hasPendingPayment = false;

    public function mount(Order $order): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order->load(['service', 'statusLogs.user', 'designFiles', 'payments']);
        $this->hasPendingPayment = $this->order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        $this->syncProgress();
    }

    public function getPaymentStatusLabelProperty(): string
    {
        return match ($this->order->payment_status) {
            'dp' => 'DP Terverifikasi',
            'menunggu' => 'Menunggu Verifikasi',
            'lunas' => 'Lunas',
            default => 'Belum Bayar',
        };
    }

    public function getPaymentStatusClassesProperty(): string
    {
        return match ($this->order->payment_status) {
            'dp' => 'bg-sky-100 text-sky-800',
            'menunggu' => 'bg-amber-100 text-amber-800',
            'lunas' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    private function syncProgress(): void
    {
        $statusKeys = array_column($this->statusSteps, 'key');
        $index = array_search($this->order->status, $statusKeys, true);

        $this->currentStepIndex = $index === false ? 0 : (int) $index;

        $stepCount = count($this->statusSteps);
        if ($stepCount === 0) {
            $this->progressPercent = 0;
            return;
        }

        $this->progressPercent = (int) round((($this->currentStepIndex + 1) / $stepCount) * 100);
    }

    public function render(): View
    {
        return view('livewire.customer.orders.show', [
            'statusLogs' => $this->order->statusLogs->sortBy('created_at'),
        ])->layout('layouts.app');
    }
}

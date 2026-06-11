<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Order $order;
    /**
     * @var array<int, array{key: string, label: string, description: string}>
     */
    public array $statusSteps = [
        ['key' => 'menunggu_appointment', 'label' => 'Pesanan Diterima', 'description' => 'Pesanan Anda telah diterima dan sedang diproses.'],
        ['key' => 'menunggu_bahan', 'label' => 'Fitting & Ukur', 'description' => 'Proses pengukuran dan fitting di studio.'],
        ['key' => 'diproses', 'label' => 'Antrian Produksi', 'description' => 'Bahan disiapkan dan dipotong.'],
        ['key' => 'dijahit', 'label' => 'Proses Jahit', 'description' => 'Pakaian Anda sedang ditangani oleh Penjahit Ahli kami.'],
        ['key' => 'finishing', 'label' => 'Finishing & QC', 'description' => 'Pengecekan kualitas dan finishing akhir.'],
        ['key' => 'selesai', 'label' => 'Siap Diambil', 'description' => 'Pesanan selesai dan siap untuk diambil.'],
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

        $this->order = $order->load(['service', 'fabric', 'statusLogs.user', 'designFiles', 'payments', 'appointment']);
        $this->hasPendingPayment = $this->order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        $this->syncProgress();
    }

    /**
     * Cek apakah customer boleh membayar.
     * - Vermak: langsung bisa bayar
     * - Custom/Seragam: hanya bisa bayar setelah appointment selesai
     */
    public function getCanPayProperty(): bool
    {
        $serviceType = $this->order->service->type ?? '';

        // Vermak tidak perlu appointment
        if ($serviceType === 'vermak') {
            return true;
        }

        // Custom & Seragam: cek appointment selesai
        return $this->order->appointment
            && $this->order->appointment->status === 'selesai';
    }

    /**
     * Cek apakah pesanan ini membutuhkan appointment.
     */
    public function getNeedsAppointmentProperty(): bool
    {
        $serviceType = $this->order->service->type ?? '';
        return in_array($serviceType, ['seragam', 'custom'], true);
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
            'canCancel' => $this->canCancelOrder(),
        ])->layout('layouts.app');
    }

    /**
     * Cek apakah order dapat dibatalkan
     */
    public function canCancelOrder(): bool
    {
        // Status yang tidak dapat dibatalkan
        $nonCancellableStatuses = ['dijahit', 'finishing', 'selesai', 'dibatalkan'];

        if (in_array($this->order->status, $nonCancellableStatuses)) {
            return false;
        }

        // Jika ada pembayaran terverifikasi, tidak dapat dibatalkan
        if ($this->order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
            return false;
        }

        return true;
    }
}

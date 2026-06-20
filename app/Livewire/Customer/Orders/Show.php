<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Order $order;

    /**
     * Timeline status steps sesuai alur bisnis baru.
     *
     * @var array<int, array{key: string, label: string, description: string}>
     */
    public array $statusSteps = [];

    public int $currentStepIndex = 0;
    public int $progressPercent = 0;
    public bool $hasPendingPayment = false;

    public function mount(Order $order): void
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order->load(['service', 'fabric', 'statusLogs.user', 'designFiles', 'payments', 'appointment', 'testimonial']);
        $this->hasPendingPayment = $this->order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        $this->buildStatusSteps();
        $this->syncProgress();
    }

    /**
     * Build status steps berdasarkan tipe layanan.
     * Custom/Seragam punya fitting, Vermak langsung ke DP.
     */
    private function buildStatusSteps(): void
    {
        $needsFitting = $this->order->requiresFitting();

        $steps = [
            ['key' => 'menunggu_konfirmasi', 'label' => 'Pesanan Dikirim', 'description' => 'Pesanan Anda sedang ditinjau oleh admin.'],
        ];

        if ($needsFitting) {
            $steps[] = ['key' => 'menunggu_fitting', 'label' => 'Jadwal Fitting', 'description' => 'Silakan atur jadwal fitting untuk pengukuran.'];
        }

        $steps = array_merge($steps, [
            ['key' => 'menunggu_dp', 'label' => 'Pembayaran DP', 'description' => 'Lakukan pembayaran DP sesuai nominal yang ditetapkan admin.'],
            ['key' => 'menunggu_bahan', 'label' => 'Persiapan Bahan', 'description' => 'Bahan sedang dipersiapkan oleh penjahit.'],
            ['key' => 'dalam_antrian', 'label' => 'Antrian Produksi', 'description' => 'Pesanan dalam antrian menunggu giliran pengerjaan.'],
            ['key' => 'dijahit', 'label' => 'Proses Jahit', 'description' => 'Pakaian Anda sedang ditangani oleh Penjahit Ahli kami.'],
            ['key' => 'selesai_produksi', 'label' => 'Selesai Produksi', 'description' => 'Produksi selesai. Silakan lakukan pelunasan pembayaran.'],
            ['key' => 'siap_diambil', 'label' => 'Siap Diambil', 'description' => 'Pesanan siap untuk Anda ambil di workshop.'],
            ['key' => 'selesai', 'label' => 'Selesai', 'description' => 'Pesanan telah selesai. Terima kasih!'],
        ]);

        $this->statusSteps = $steps;
    }

    /**
     * Cek apakah customer boleh membayar.
     * - DP: saat status menunggu_dp dan dp_amount sudah ditetapkan
     * - Pelunasan: saat status selesai_produksi
     */
    public function getCanPayProperty(): bool
    {
        if ($this->order->status === 'menunggu_dp') {
            return $this->order->dp_amount > 0;
        }

        if ($this->order->status === 'selesai_produksi') {
            return true;
        }

        return false;
    }

    /**
     * Cek tipe pembayaran yang harus dilakukan.
     */
    public function getPaymentTypeProperty(): string
    {
        if ($this->order->status === 'menunggu_dp') {
            return 'dp';
        }
        return 'pelunasan';
    }

    /**
     * Cek apakah pesanan ini membutuhkan fitting.
     */
    public function getNeedsAppointmentProperty(): bool
    {
        return $this->order->requiresFitting();
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
        $currentStatus = $this->order->status;

        // Untuk status terminal (ditolak, dibatalkan), set ke step 0
        if (in_array($currentStatus, ['ditolak', 'dibatalkan'], true)) {
            $this->currentStepIndex = -1;
            $this->progressPercent = 0;
            return;
        }

        // Jika Vermak dan status skip fitting step, cari match di steps
        $index = array_search($currentStatus, $statusKeys, true);

        // Handle: jika status tidak ada di steps (misal menunggu_bahan di-skip karena bahan ready)
        if ($index === false) {
            $this->currentStepIndex = 0;
            $this->progressPercent = 0;
            return;
        }

        $this->currentStepIndex = (int) $index;

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

    public function markAsCompleted(): void
    {
        if ($this->order->status !== 'siap_diambil') {
            return;
        }

        $this->order->update(['status' => 'selesai']);

        \App\Models\OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'selesai',
            'changed_by' => auth()->id(),
            'notes' => 'Customer mengonfirmasi pesanan telah diambil dan selesai.',
        ]);

        $this->syncProgress();
        session()->flash('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai.');
    }

    // Properti untuk Testimonial
    public int $rating = 5;
    public string $review = '';

    public function submitTestimonial(): void
    {
        if ($this->order->status !== 'selesai' || $this->order->testimonial) {
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        \App\Models\Testimonial::create([
            'order_id' => $this->order->id,
            'customer_id' => auth()->id(),
            'rating' => $this->rating,
            'review' => $this->review,
            'is_featured' => false,
        ]);

        $this->order->load('testimonial');
        session()->flash('success', 'Terima kasih atas ulasan Anda!');
    }
}

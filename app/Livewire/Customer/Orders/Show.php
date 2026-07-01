<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Customer Orders Show Component - Detail pesanan untuk customer
 * 
 * Menampilkan informasi lengkap pesanan dengan:
 * - Timeline status progress dengan langkah-langkah sesuai tipe layanan
 * - Informasi pembayaran (DP/pelunasan)
 * - Riwayat status log
 * - Opsi pembayaran dan pembatalan (jika eligible)
 * - Preview desain dan appointment info
 * 
 * Timeline berbeda untuk:
 * - Custom/Seragam: dengan tahap fitting
 * - Perbaikan/Vermak: langsung ke pengiriman
 * 
 * Fitur:
 * - Progress bar yang dinamis sesuai status
 * - Badge status pembayaran
 * - Validasi pembatalan (tidak boleh ada pembayaran terverifikasi)
 */
class Show extends Component
{
    // ──────────────────────────────────────────────────────────
    // Properties
    // ──────────────────────────────────────────────────────────

    public Order $order;

    /**
     * Timeline status steps sesuai alur bisnis.
     * Dibangun dinamis berdasarkan tipe layanan (custom vs vermak)
     *
     * @var array<int, array{key: string, label: string, description: string}>
     */
    public array $statusSteps = [];

    /** Indeks step yang sedang aktif */
    public int $currentStepIndex = 0;
    
    /** Progress percentage untuk progress bar */
    public int $progressPercent = 0;
    
    /** Cek apakah ada pembayaran menunggu verifikasi */
    public bool $hasPendingPayment = false;

    // ──────────────────────────────────────────────────────────
    // Lifecycle Hooks
    // ──────────────────────────────────────────────────────────

    /**
     * Mount component - Validasi akses dan setup data
     * 
     * Memastikan:
     * - User sudah login dan customer
     * - User adalah pemilik order
     * - Load relasi yang diperlukan
     * - Build status timeline
     * - Sync progress bar
     * 
     * @param Order $order Order yang ditampilkan
     * @return void
     */
    public function mount(Order $order): void
    {
        // Validasi user customer
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Validasi user adalah pemilik order
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Load relasi yang diperlukan untuk view
        $this->order = $order->load([
            'service', 
            'fabric', 
            'statusLogs.user', 
            'designFiles', 
            'payments', 
            'appointment', 
            'testimonial'
        ]);
        
        // Check pembayaran pending
        $this->hasPendingPayment = $this->order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        // Build timeline steps
        $this->buildStatusSteps();
        $this->syncProgress();
    }

    // ──────────────────────────────────────────────────────────
    // Timeline & Progress Management
    // ──────────────────────────────────────────────────────────

    /**
     * Build status steps timeline berdasarkan tipe layanan
     * 
     * Aturan:
     * - Custom/Seragam: Include fitting step
     * - Vermak: Skip fitting, langsung pengiriman
     * 
     * @return void
     */
    private function buildStatusSteps(): void
    {
        $needsFitting = $this->order->requiresFitting();

        $steps = [
            ['key' => 'menunggu_konfirmasi', 'label' => 'Pesanan Dikirim', 'description' => 'Pesanan Anda sedang ditinjau oleh admin.'],
        ];

        // Tambah fitting jika perlu
        if ($needsFitting) {
            $steps[] = ['key' => 'menunggu_fitting', 'label' => 'Jadwal Fitting', 'description' => 'Silakan atur jadwal fitting untuk pengukuran.'];
        }

        // Tambah pembayaran & bahan steps untuk non-vermak
        if ($this->order->service->type !== 'vermak') {
            $steps = array_merge($steps, [
                ['key' => 'menunggu_dp', 'label' => 'Pembayaran DP', 'description' => 'Lakukan pembayaran DP sesuai nominal yang ditetapkan admin.'],
                ['key' => 'menunggu_bahan', 'label' => 'Persiapan Bahan', 'description' => 'Bahan sedang dipersiapkan oleh penjahit.'],
            ]);
        } else {
            // Untuk vermak: pengiriman pakaian
            $steps = array_merge($steps, [
                ['key' => 'menunggu_pakaian_dikirim', 'label' => 'Pengiriman Pakaian', 'description' => 'Kirim atau antar pakaian Anda ke workshop kami.'],
                ['key' => 'pakaian_dikirim', 'label' => 'Pakaian Dikirim', 'description' => 'Menunggu admin mengonfirmasi penerimaan pakaian.'],
            ]);
        }

        // Produksi steps (sama untuk semua)
        $steps = array_merge($steps, [
            ['key' => 'dalam_antrian', 'label' => 'Antrian Produksi', 'description' => 'Pesanan dalam antrian menunggu giliran pengerjaan.'],
            ['key' => 'dijahit', 'label' => 'Proses Jahit', 'description' => 'Pakaian Anda sedang ditangani oleh Penjahit Ahli kami.'],
            ['key' => 'selesai_produksi', 'label' => 'Selesai Produksi', 'description' => 'Produksi selesai. Silakan lakukan pelunasan pembayaran.'],
            ['key' => 'siap_diambil', 'label' => 'Siap Diambil', 'description' => 'Pesanan siap untuk Anda ambil di workshop.'],
            ['key' => 'selesai', 'label' => 'Selesai', 'description' => 'Pesanan telah selesai. Terima kasih!'],
        ]);

        $this->statusSteps = $steps;
    }

    /**
     * Sync progress bar dengan status order saat ini
     * 
     * Menghitung:
     * - Indeks step aktual dari status order
     * - Percentage progress (step_aktual / total_steps * 100)
     * 
     * Catatan: Status terminal (ditolak, dibatalkan) = progress 0
     * 
     * @return void
     */
    private function syncProgress(): void
    {
        $statusKeys = array_column($this->statusSteps, 'key');
        $currentStatus = $this->order->status;

        // Status terminal (ditolak, dibatalkan) = no progress
        if (in_array($currentStatus, ['ditolak', 'dibatalkan'], true)) {
            $this->currentStepIndex = -1;
            $this->progressPercent = 0;
            return;
        }

        // Cari indeks status saat ini dalam steps
        $index = array_search($currentStatus, $statusKeys, true);

        if ($index === false) {
            $this->currentStepIndex = 0;
            $this->progressPercent = 0;
            return;
        }

        $this->currentStepIndex = (int) $index;
        $stepCount = count($this->statusSteps);

        $this->progressPercent = $stepCount > 0 
            ? (int) round((($this->currentStepIndex + 1) / $stepCount) * 100)
            : 0;
    }

    // ──────────────────────────────────────────────────────────
    // Payment-related Properties & Methods
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah customer boleh membuat pembayaran saat ini
     * 
     * Bisa membayar jika:
     * - Status menunggu_dp DAN dp_amount sudah ditetapkan
     * - Status selesai_produksi (pelunasan)
     * 
     * @return bool
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
     * Mendapatkan tipe pembayaran yang harus dilakukan
     * 
     * @return string 'dp' atau 'pelunasan'
     */
    public function getPaymentTypeProperty(): string
    {
        if ($this->order->status === 'menunggu_dp') {
            return 'dp';
        }
        return 'pelunasan';
    }

    /**
     * Cek apakah pesanan membutuhkan fitting
     * 
     * @return bool
     */
    public function getNeedsAppointmentProperty(): bool
    {
        return $this->order->requiresFitting();
    }

    /**
     * Mendapatkan label status pembayaran dalam bahasa user-friendly
     * 
     * @return string Label yang ditampilkan
     */
    public function getPaymentStatusLabelProperty(): string
    {
        return match ($this->order->payment_status) {
            'dp' => 'DP Terverifikasi',
            'menunggu' => 'Menunggu Verifikasi',
            'lunas' => 'Lunas',
            default => 'Belum Bayar',
        };
    }

    /**
     * Mendapatkan kelas CSS untuk badge status pembayaran
     * 
     * @return string Kelas CSS Tailwind
     */
    public function getPaymentStatusClassesProperty(): string
    {
        return match ($this->order->payment_status) {
            'dp' => 'bg-sky-100 text-sky-800',
            'menunggu' => 'bg-amber-100 text-amber-800',
            'lunas' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    // ──────────────────────────────────────────────────────────
    // Cancellation Methods
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah order dapat dibatalkan oleh customer
     * 
     * Aturan pembatalan:
     * - Status tidak boleh dalam tahap akhir (dijahit, selesai_produksi, siap, selesai)
     * - Tidak boleh ada pembayaran terverifikasi
     * 
     * @return bool true jika order dapat dibatalkan
     */
    public function canCancelOrder(): bool
    {
        $nonCancellableStatuses = [
            'dijahit', 
            'selesai_produksi', 
            'siap_diambil', 
            'selesai', 
            'ditolak', 
            'dibatalkan'
        ];

        if (in_array($this->order->status, $nonCancellableStatuses)) {
            return false;
        }

        // Jika ada pembayaran terverifikasi, tidak dapat dibatalkan
        if ($this->order->payments->where('status', 'terverifikasi')->isNotEmpty()) {
            return false;
        }

        return true;
    }

    // ──────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────

    /**
     * Render view dengan data yang sudah disiapkan
     * 
     * @return View
     */
    public function render(): View
    {
        return view('livewire.customer.orders.show', [
            'statusLogs' => $this->order->statusLogs->sortBy('created_at'),
            'canCancel' => $this->canCancelOrder(),
        ])->layout('layouts.app');
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

    public function confirmClothesSent(): void
    {
        if ($this->order->status !== 'menunggu_pakaian_dikirim') {
            return;
        }

        $this->order->update(['status' => 'pakaian_dikirim']);

        \App\Models\OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'pakaian_dikirim',
            'changed_by' => auth()->id(),
            'notes' => 'Customer mengonfirmasi telah mengirim/menyerahkan pakaian ke tempat jahit.',
        ]);

        $this->syncProgress();
        session()->flash('success', 'Status pakaian berhasil diperbarui! Menunggu konfirmasi penerimaan dari admin.');
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
            'comment' => $this->review ?? '',
        ]);

        $this->order->load('testimonial');
        session()->flash('success', 'Rating dan feedback Anda telah berhasil dikirim!');
    }
}

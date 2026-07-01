<?php

namespace App\Livewire\Customer\Payments;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Payment History Component - Riwayat pembayaran customer
 * 
 * Menampilkan daftar pembayaran yang telah dibuat customer dengan fitur:
 * - Filter berdasarkan status pembayaran (semua, belum bayar, menunggu, ditolak, terverifikasi)
 * - Pagination 10 items per halaman
 * - Dapat melihat history pembayaran untuk semua order atau order spesifik
 * - Status badge dengan warna yang berbeda untuk tiap status
 * 
 * Security:
 * - Customer hanya bisa melihat pembayaran mereka sendiri
 * - Jika filter order, harus milik customer yang login
 */
class History extends Component
{
    use WithPagination;

    // ──────────────────────────────────────────────────────────
    // Properties
    // ──────────────────────────────────────────────────────────

    /** Order spesifik (optional) - jika ada, tampilkan pembayaran untuk order ini saja */
    public ?Order $order = null;
    
    /** Filter status pembayaran (all|belum_bayar|menunggu_verifikasi|ditolak|terverifikasi) */
    public string $statusFilter = 'all';

    // ──────────────────────────────────────────────────────────
    // Lifecycle Hooks
    // ──────────────────────────────────────────────────────────

    /**
     * Mount component - Validasi akses
     * 
     * Memastikan:
     * - User sudah login dan customer
     * - Jika filter order, user adalah pemilik order
     * 
     * @param Order|null $order Order spesifik (optional)
     * @return void
     */
    public function mount(?Order $order = null): void
    {
        // Validasi user customer
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Validasi user adalah pemilik order (jika filter order)
        if ($order && $order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order;
    }

    /**
     * Reset pagination saat filter status berubah
     * 
     * @return void
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    // ──────────────────────────────────────────────────────────
    // Data Properties & Accessors
    // ──────────────────────────────────────────────────────────

    /**
     * Mendapatkan daftar opsi filter status pembayaran
     * 
     * @return array<string, string> Status options untuk dropdown
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

    /**
     * Mendapatkan daftar pembayaran dengan filter & pagination
     * 
     * Query membaca pembayaran customer dengan:
     * - Filter order (jika ada)
     * - Filter status (jika tidak 'all')
     * - Diurutkan dari terbaru
     * - Pagination 10 per halaman
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaymentsProperty()
    {
        $query = auth()->user()
            ->payments()
            ->with(['order', 'order.service']);

        // Filter order spesifik (jika ada)
        if ($this->order) {
            $query->where('order_id', $this->order->id);
        }

        // Filter status pembayaran
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate(10);
    }

    // ──────────────────────────────────────────────────────────
    // UI Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Mendapatkan kelas CSS untuk badge status pembayaran
     * 
     * Memberikan warna berbeda untuk tiap status:
     * - Terverifikasi: Hijau
     * - Ditolak: Merah
     * - Menunggu: Kuning
     * - Belum Bayar: Abu-abu
     * 
     * @param string $status Status pembayaran
     * @return string Kelas CSS Tailwind untuk badge
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

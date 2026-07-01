<?php

namespace App\Livewire\Customer\Orders;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * CancelOrder Component - Form pembatalan pesanan oleh customer
 * 
 * Komponen ini memungkinkan customer untuk membatalkan pesanan mereka
 * dengan syarat:
 * - Order hanya dapat dibatalkan dari status tertentu
 * - Tidak ada pembayaran yang sudah terverifikasi
 * - Alasan pembatalan harus diberikan (minimal 10 karakter)
 * 
 * Flow:
 * 1. Customer mengisi alasan pembatalan
 * 2. Sistem memvalidasi kondisi pembatalan
 * 3. Order diubah status menjadi 'dibatalkan'
 * 4. Log status dibuat untuk tracking
 */
class CancelOrder extends Component
{
    // ──────────────────────────────────────────────────────────
    // Properties
    // ──────────────────────────────────────────────────────────

    public Order $order;
    
    /** Alasan pembatalan yang diberikan customer */
    public string $cancellationReason = '';
    
    /** Kontrol tampil/sembunyikan modal konfirmasi */
    public bool $showConfirmation = false;

    // ──────────────────────────────────────────────────────────
    // Lifecycle Hooks
    // ──────────────────────────────────────────────────────────

    /**
     * Mount component - Validasi akses dan kondisi pembatalan
     * 
     * Memastikan:
     * - User sudah login
     * - User adalah customer (bukan admin)
     * - User adalah pemilik order
     * - Order bisa dibatalkan (status & pembayaran)
     * 
     * @param Order $order Order yang akan dibatalkan
     * @return void
     */
    public function mount(Order $order): void
    {
        // Validasi user sudah login dan customer
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Validasi user adalah pemilik order
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $this->order = $order->load(['service', 'payments']);

        // Validasi order bisa dibatalkan
        if (!$this->canCancelOrder()) {
            abort(403, 'Pesanan ini tidak dapat dibatalkan.');
        }
    }

    // ──────────────────────────────────────────────────────────
    // Validation & Business Rules
    // ──────────────────────────────────────────────────────────

    /**
     * Validasi aturan pembatalan order
     * 
     * Aturan:
     * - Status tidak boleh dalam tahap akhir (dijahit, selesai_produksi, siap, selesai)
     * - Tidak boleh ada pembayaran terverifikasi
     * 
     * @return bool true jika order dapat dibatalkan
     */
    public function canCancelOrder(): bool
    {
        // Status yang tidak boleh dibatalkan (tahap akhir produksi)
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

    /**
     * Aturan validasi form pembatalan
     * 
     * @return array Validation rules
     */
    protected function rules(): array
    {
        return [
            'cancellationReason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * Pesan error validasi dalam Bahasa Indonesia
     * 
     * @return array Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'cancellationReason.required' => 'Alasan pembatalan harus diisi.',
            'cancellationReason.min' => 'Alasan pembatalan minimal 10 karakter.',
            'cancellationReason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Actions
    // ──────────────────────────────────────────────────────────

    /**
     * Proses pembatalan order dengan alasan yang sudah divalidasi
     * 
     * Steps:
     * 1. Validasi input (alasan pembatalan)
     * 2. Cek ulang kondisi pembatalan
     * 3. Update status order menjadi 'dibatalkan'
     * 4. Catat log status untuk tracking
     * 5. Redirect ke halaman order index
     * 
     * @return \Livewire\Redirector
     */
    public function submitCancellation()
    {
        // Validasi input form
        $this->validate();

        // Perbarui status order ke 'dibatalkan' dengan reason
        $this->order->update([
            'status' => 'dibatalkan',
            'cancelled_at' => now(),
            'cancellation_reason' => $this->cancellationReason,
        ]);

        // Tambahkan log status untuk tracking/audit trail
        $this->order->statusLogs()->create([
            'status' => 'dibatalkan',
            'changed_by' => auth()->id(),
            'notes' => 'Dibatalkan oleh customer: ' . $this->cancellationReason,
        ]);

        // Flash success message
        session()->flash('success', 'Pesanan berhasil dibatalkan.');
        
        // Redirect ke halaman order index
        return $this->redirect(
            route('orders.index'),
            navigate: true
        );
    }

    public function render(): View
    {
        return view('livewire.customer.orders.cancel-order')
            ->layout('layouts.app');
    }
}

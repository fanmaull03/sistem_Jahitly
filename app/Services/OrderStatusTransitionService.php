<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;

/**
 * OrderStatusTransitionService - Mengelola transisi status order
 * 
 * Service ini menangani semua logika status transition:
 * - Validasi transisi status yang allowed
 * - Cek pre-conditions sebelum transition
 * - Execute transition dengan side effects (notification, logging)
 * - Provide helpful error messages
 * 
 * Prinsip Design:
 * - Single Responsibility: hanya handle status transitions
 * - Return structured response dengan status & messages
 * - No direct database updates (immutable logic)
 */
class OrderStatusTransitionService
{
    // ──────────────────────────────────────────────────────────
    // Setup & Dependencies
    // ──────────────────────────────────────────────────────────

    private OrderBusinessRulesService $businessRules;
    private PaymentService $paymentService;

    public function __construct()
    {
        $this->businessRules = app(OrderBusinessRulesService::class);
        $this->paymentService = app(PaymentService::class);
    }

    // ──────────────────────────────────────────────────────────
    // Accept Order
    // ──────────────────────────────────────────────────────────

    /**
     * Menerima pesanan dari status 'menunggu_konfirmasi'
     * 
     * Menentukan status berikutnya berdasarkan tipe layanan:
     * - Custom/Seragam: menunggu_fitting (butuh measurement)
     * - Vermak: menunggu_pakaian_dikirim (customer bawa pakaian)
     * - Lainnya: menunggu_dp (langsung bayar DP)
     * 
     * @param Order $order Order yang diterima
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string, newStatus?: string}
     */
    public function acceptOrder(Order $order, int $adminId): array
    {
        // Validasi status saat ini
        if ($order->status !== 'menunggu_konfirmasi') {
            return [
                'success' => false,
                'message' => 'Pesanan ini tidak sedang menunggu konfirmasi.'
            ];
        }

        // Tentukan status berikutnya berdasarkan tipe layanan
        $newStatus = $this->determineNextStatusAfterAcceptance($order);

        // Update order
        $order->update(['status' => $newStatus]);

        // Log status change
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => $adminId,
            'notes' => 'Pesanan diterima oleh admin.',
        ]);

        // Notify customer
        $this->notifyCustomer(
            $order,
            $this->getAcceptanceMessage($order, $newStatus)
        );

        return [
            'success' => true,
            'message' => 'Pesanan berhasil diterima.',
            'newStatus' => $newStatus,
        ];
    }

    /**
     * Tentukan status berikutnya setelah acceptance berdasarkan service type
     * 
     * @param Order $order
     * @return string Status berikutnya
     */
    private function determineNextStatusAfterAcceptance(Order $order): string
    {
        $serviceType = $order->service->type;

        return match($serviceType) {
            'vermak' => 'menunggu_pakaian_dikirim',
            default => $this->businessRules->requiresFitting($serviceType)
                ? 'menunggu_fitting'
                : 'menunggu_dp',
        };
    }

    /**
     * Generate acceptance message untuk notification
     * 
     * @param Order $order
     * @param string $newStatus Status baru
     * @return string Pesan untuk customer
     */
    private function getAcceptanceMessage(Order $order, string $newStatus): string
    {
        return match($newStatus) {
            'menunggu_fitting' => 'Pesanan #' . $order->order_number . ' telah diterima. Silakan atur jadwal fitting.',
            'menunggu_pakaian_dikirim' => 'Pesanan #' . $order->order_number . ' telah diterima. Silakan kirim/antar pakaian Anda ke workshop kami.',
            default => 'Pesanan #' . $order->order_number . ' telah diterima. Silakan lakukan pembayaran DP.'
        };
    }

    // ──────────────────────────────────────────────────────────
    // Move to Queue
    // ──────────────────────────────────────────────────────────

    /**
     * Pindahkan order ke antrian produksi
     * 
     * Pre-conditions:
     * - DP terverifikasi (untuk non-vermak)
     * - Material ready
     * - Fitting selesai (jika diperlukan)
     * 
     * @param Order $order Order yang dipindahkan
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string, errors?: list<string>}
     */
    public function moveToQueue(Order $order, int $adminId): array
    {
        // Cek pre-conditions
        $check = $this->businessRules->canMoveToQueue($order);
        
        if (!$check['can_proceed']) {
            return [
                'success' => false,
                'message' => 'Order tidak dapat masuk antrian produksi.',
                'errors' => $check['blocking_reasons'],
            ];
        }

        // Update status
        $order->update(['status' => 'dalam_antrian']);

        // Log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'dalam_antrian',
            'changed_by' => $adminId,
            'notes' => 'Order masuk antrian produksi.',
        ]);

        // Notify
        $this->notifyCustomer(
            $order,
            'Pesanan #' . $order->order_number . ' masuk antrian produksi. Segera kami proses.'
        );

        return [
            'success' => true,
            'message' => 'Pesanan berhasil masuk antrian produksi.',
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Update Production Status
    // ──────────────────────────────────────────────────────────

    /**
     * Update status produksi (dijahit, selesai_produksi, siap_diambil, selesai)
     * 
     * @param Order $order Order yang diupdate
     * @param string $newStatus Status baru (dijahit|selesai_produksi|siap_diambil|selesai)
     * @param int $adminId Admin yang melakukan action
     * @param string|null $notes Catatan optional
     * @return array{success: bool, message: string, errors?: list<string>}
     */
    public function updateProductionStatus(
        Order $order,
        string $newStatus,
        int $adminId,
        ?string $notes = null
    ): array
    {
        // Validasi transisi
        if (!$this->businessRules->canTransition($order->status, $newStatus)) {
            return [
                'success' => false,
                'message' => 'Status transition tidak valid: ' . $order->status . ' → ' . $newStatus,
            ];
        }

        // Special check untuk siap_diambil: harus lunas
        if ($newStatus === 'siap_diambil') {
            $check = $this->businessRules->canMarkReadyForPickup($order);
            if (!$check['can_proceed']) {
                return [
                    'success' => false,
                    'message' => 'Order tidak dapat ditandai siap diambil.',
                    'errors' => $check['blocking_reasons'],
                ];
            }
        }

        // Update status
        $order->update(['status' => $newStatus]);

        // Log
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => $adminId,
            'notes' => $notes ?? 'Status diupdate menjadi: ' . $newStatus,
        ]);

        // Notify
        $message = $this->getProductionStatusMessage($order, $newStatus);
        $this->notifyCustomer($order, $message);

        return [
            'success' => true,
            'message' => 'Status produksi berhasil diupdate.',
        ];
    }

    /**
     * Generate pesan untuk production status update
     * 
     * @param Order $order
     * @param string $newStatus
     * @return string Pesan untuk customer
     */
    private function getProductionStatusMessage(Order $order, string $newStatus): string
    {
        return match($newStatus) {
            'dijahit' => 'Pesanan #' . $order->order_number . ' sedang dalam proses penjahitan.',
            'selesai_produksi' => 'Produksi pesanan #' . $order->order_number . ' selesai. Silakan lakukan pelunasan pembayaran.',
            'siap_diambil' => 'Pesanan #' . $order->order_number . ' siap untuk diambil di workshop kami.',
            'selesai' => 'Terima kasih! Pesanan #' . $order->order_number . ' telah selesai. Kami tunggu feedback Anda.',
            default => 'Status pesanan #' . $order->order_number . ' diupdate.',
        };
    }

    // ──────────────────────────────────────────────────────────
    // Helper: Notify Customer
    // ──────────────────────────────────────────────────────────

    /**
     * Kirim notifikasi ke customer
     * 
     * @param Order $order Order yang dinotify
     * @param string $message Pesan yang dikirim
     * @return void
     */
    private function notifyCustomer(Order $order, string $message): void
    {
        $customer = $order->customer;
        if ($customer) {
            $customer->notify(new OrderStatusUpdated($order, $message));
        }
    }

    // ──────────────────────────────────────────────────────────
    // Status Validation Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah order dapat transition ke status tertentu
     * 
     * @param Order $order
     * @param string $targetStatus Status tujuan
     * @return bool
     */
    public function canTransitionTo(Order $order, string $targetStatus): bool
    {
        return $this->businessRules->canTransition($order->status, $targetStatus);
    }

    /**
     * Dapatkan next statuses yang valid untuk order
     * 
     * @param Order $order
     * @return array<string> List status yang dapat dituju
     */
    public function getNextValidStatuses(Order $order): array
    {
        return $this->businessRules->getNextStatuses($order->status);
    }
}

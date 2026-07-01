<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;

/**
 * OrderRejectionService - Menangani penolakan order
 * 
 * Service ini mengenkapsulasi logika penolakan pesanan:
 * - Validasi kondisi penolakan (order harus dalam status menunggu_konfirmasi)
 * - Record alasan penolakan
 * - Logging audit trail
 * - Notifikasi ke customer
 * 
 * Catatan: Penolakan hanya bisa dilakukan pada order yang baru,
 * sebelum production dimulai.
 */
class OrderRejectionService
{
    // ──────────────────────────────────────────────────────────
    // Reject Order
    // ──────────────────────────────────────────────────────────

    /**
     * Tolak pesanan dengan alasan
     * 
     * Validasi:
     * - Order harus dalam status menunggu_konfirmasi
     * - Alasan harus diisi (minimal 10 karakter, maksimal 2000)
     * 
     * @param Order $order Order yang ditolak
     * @param string $reason Alasan penolakan
     * @param int $adminId Admin yang melakukan penolakan
     * @return array{success: bool, message: string, errors?: list<string>}
     */
    public function rejectOrder(Order $order, string $reason, int $adminId): array
    {
        // Validasi status
        if ($order->status !== 'menunggu_konfirmasi') {
            return [
                'success' => false,
                'message' => 'Pesanan ini tidak sedang menunggu konfirmasi, tidak bisa ditolak.',
            ];
        }

        // Validasi alasan
        $validation = $this->validateReason($reason);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'Alasan penolakan tidak valid.',
                'errors' => $validation['errors'],
            ];
        }

        // Update order
        $order->update([
            'status' => 'ditolak',
            'rejection_reason' => $reason,
        ]);

        // Log penolakan
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'ditolak',
            'changed_by' => $adminId,
            'notes' => 'Pesanan ditolak: ' . $reason,
        ]);

        // Notify customer
        $this->notifyCustomer(
            $order,
            'Pesanan #' . $order->order_number . ' ditolak. Alasan: ' . $reason
        );

        return [
            'success' => true,
            'message' => 'Pesanan berhasil ditolak. Notifikasi telah dikirim ke customer.',
        ];
    }

    /**
     * Cek apakah order bisa ditolak
     * 
     * Order hanya dapat ditolak jika:
     * - Status adalah menunggu_konfirmasi
     * - Belum dalam tahap produksi
     * 
     * @param Order $order
     * @return array{can_reject: bool, reason?: string}
     */
    public function canRejectOrder(Order $order): array
    {
        if ($order->status !== 'menunggu_konfirmasi') {
            return [
                'can_reject' => false,
                'reason' => 'Pesanan ini tidak dapat ditolak. Status: ' . $order->status,
            ];
        }

        return ['can_reject' => true];
    }

    // ──────────────────────────────────────────────────────────
    // Validation
    // ──────────────────────────────────────────────────────────

    /**
     * Validasi format & panjang alasan penolakan
     * 
     * Rules:
     * - Harus diisi (required)
     * - Minimal 10 karakter
     * - Maksimal 2000 karakter
     * 
     * @param string $reason Alasan yang akan divalidasi
     * @return array{valid: bool, errors?: list<string>}
     */
    private function validateReason(string $reason): array
    {
        $errors = [];

        $trimmed = trim($reason);
        if (empty($trimmed)) {
            $errors[] = 'Alasan penolakan harus diisi.';
        }

        $length = strlen($trimmed);
        if ($length < 10) {
            $errors[] = 'Alasan penolakan minimal 10 karakter.';
        }

        if ($length > 2000) {
            $errors[] = 'Alasan penolakan maksimal 2000 karakter.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Helper: Notify Customer
    // ──────────────────────────────────────────────────────────

    /**
     * Kirim notifikasi penolakan ke customer
     * 
     * @param Order $order Order yang ditolak
     * @param string $message Pesan untuk customer
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
    // Retrieve Information
    // ──────────────────────────────────────────────────────────

    /**
     * Dapatkan alasan penolakan (jika ada)
     * 
     * @param Order $order
     * @return string|null Alasan penolakan atau null jika tidak ditolak
     */
    public function getRejectionReason(Order $order): ?string
    {
        return $order->status === 'ditolak' ? $order->rejection_reason : null;
    }

    /**
     * Cek apakah order ditolak
     * 
     * @param Order $order
     * @return bool
     */
    public function isRejected(Order $order): bool
    {
        return $order->status === 'ditolak';
    }
}

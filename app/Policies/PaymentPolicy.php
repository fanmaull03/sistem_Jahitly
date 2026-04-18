<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    // ──────────────────────────────────────────────────────────
    // View — Melihat detail pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh melihat detail pembayaran.
     *
     * Akses diberikan kepada:
     * - Customer pemilik pembayaran
     * - Admin
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->customer_id || $user->isAdmin();
    }

    // ──────────────────────────────────────────────────────────
    // Create — Membuat pembayaran baru untuk sebuah order
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh membuat pembayaran.
     *
     * Hanya customer yang bisa membuat pembayaran.
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    // ──────────────────────────────────────────────────────────
    // Verify — Memverifikasi (approve/reject) pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh memverifikasi pembayaran.
     *
     * Hanya admin yang bisa memverifikasi, dan hanya jika
     * status pembayaran masih "menunggu_verifikasi".
     */
    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin() && $payment->status === 'menunggu_verifikasi';
    }

    // ──────────────────────────────────────────────────────────
    // ViewProof — Melihat file bukti pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh melihat file bukti pembayaran.
     *
     * Akses diberikan kepada:
     * - Customer pemilik pembayaran
     * - Admin
     *
     * Dan hanya jika file bukti memang ada.
     */
    public function viewProof(User $user, Payment $payment): bool
    {
        if (! $payment->proof_file_path) {
            return false;
        }

        return $user->id === $payment->customer_id || $user->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\User;

class AppointmentPolicy
{
    // ──────────────────────────────────────────────────────────
    // Create — Membuat appointment baru untuk sebuah order
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh membuat appointment untuk order ini.
     *
     * Syarat:
     * - User adalah customer
     * - Order milik customer tersebut
     * - Layanan order bertipe seragam atau custom
     * - Order belum memiliki appointment aktif (yang bukan dibatalkan)
     */
    public function create(User $user, Order $order): bool
    {
        if (! $user->isCustomer()) {
            return false;
        }

        if ($order->customer_id !== $user->id) {
            return false;
        }

        // Hanya untuk layanan seragam dan custom
        $order->loadMissing('service');
        if (! in_array($order->service->type, ['seragam', 'custom'], true)) {
            return false;
        }

        // Cek apakah sudah ada appointment aktif (bukan dibatalkan)
        $order->loadMissing('appointment');
        if ($order->appointment && $order->appointment->status !== 'dibatalkan') {
            return false;
        }

        return true;
    }

    // ──────────────────────────────────────────────────────────
    // View — Melihat detail appointment
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh melihat detail appointment.
     *
     * Akses diberikan kepada:
     * - Customer pemilik appointment
     * - Admin
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->customer_id || $user->isAdmin();
    }

    // ──────────────────────────────────────────────────────────
    // Manage — Mengelola appointment (confirm, complete)
    // ──────────────────────────────────────────────────────────

    /**
     * Menentukan apakah user boleh mengelola appointment.
     *
     * Hanya admin yang bisa confirm dan complete.
     */
    public function manage(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }
}

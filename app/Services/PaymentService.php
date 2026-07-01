<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Collection;

/**
 * PaymentService - Layanan untuk mengelola logika pembayaran
 * 
 * Service ini mengenkapsulasi semua logika pembayaran yang sebelumnya
 * tersebar di berbagai models dan controllers, menerapkan prinsip DRY.
 */
class PaymentService
{
    /**
     * Menghitung status pembayaran order berdasarkan pembayaran yang ada
     * 
     * Aturan:
     * - Jika ada pelunasan terverifikasi -> 'lunas'
     * - Jika ada DP terverifikasi -> 'dp'
     * - Jika ada pembayaran menunggu verifikasi -> 'menunggu'
     * - Default -> 'belum_bayar'
     * 
     * @param Order $order Order yang akan dicek status pembayarannya
     * @return string Status pembayaran (lunas|dp|menunggu|belum_bayar)
     */
    public function calculateOrderPaymentStatus(Order $order): string
    {
        $payments = $order->payments;

        // Cek apakah ada pelunasan terverifikasi
        if ($this->hasVerifiedPaymentType($payments, 'pelunasan')) {
            return 'lunas';
        }

        // Cek apakah ada DP terverifikasi
        if ($this->hasVerifiedPaymentType($payments, 'dp')) {
            return 'dp';
        }

        // Cek apakah ada pembayaran yang menunggu verifikasi
        if ($this->hasPendingPayments($payments)) {
            return 'menunggu';
        }

        return 'belum_bayar';
    }

    /**
     * Mendapatkan semua pembayaran yang sudah terverifikasi
     * 
     * @param Order $order Order yang akan diambil pembayarannya
     * @return Collection Koleksi pembayaran terverifikasi
     */
    public function getVerifiedPayments(Order $order): Collection
    {
        return $order->payments()
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->get();
    }

    /**
     * Mendapatkan pembayaran DP yang terverifikasi (jika ada)
     * 
     * @param Order $order Order yang akan diambil pembayarannya
     * @return Payment|null Pembayaran DP terverifikasi atau null
     */
    public function getVerifiedDpPayment(Order $order): ?Payment
    {
        return $order->payments()
            ->where('payment_type', 'dp')
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->first();
    }

    /**
     * Mendapatkan pembayaran pelunasan yang terverifikasi (jika ada)
     * 
     * @param Order $order Order yang akan diambil pembayarannya
     * @return Payment|null Pembayaran pelunasan terverifikasi atau null
     */
    public function getVerifiedFinalPayment(Order $order): ?Payment
    {
        return $order->payments()
            ->where('payment_type', 'pelunasan')
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->first();
    }

    /**
     * Cek apakah order sudah ada pembayaran terverifikasi
     * 
     * @param Order $order Order yang akan dicek
     * @return bool
     */
    public function hasAnyVerifiedPayment(Order $order): bool
    {
        return $order->payments()
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->exists();
    }

    /**
     * Cek apakah order bisa dibatalkan (tidak memiliki pembayaran terverifikasi)
     * 
     * @param Order $order Order yang akan dicek
     * @return bool
     */
    public function canOrderBeCancelled(Order $order): bool
    {
        return !$this->hasAnyVerifiedPayment($order);
    }

    /**
     * Mendapatkan total pembayaran terverifikasi
     * 
     * @param Order $order Order yang akan dihitung pembayarannya
     * @return float Total pembayaran terverifikasi
     */
    public function getTotalVerifiedAmount(Order $order): float
    {
        return $order->payments()
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->sum('amount');
    }

    /**
     * Cek apakah ada pembayaran dengan tipe tertentu yang terverifikasi
     * 
     * @param Collection $payments Koleksi pembayaran
     * @param string $paymentType Tipe pembayaran (dp|pelunasan)
     * @return bool
     */
    private function hasVerifiedPaymentType(Collection $payments, string $paymentType): bool
    {
        return $payments
            ->where('payment_type', $paymentType)
            ->where('status', PaymentStatus::TERVERIFIKASI->value)
            ->isNotEmpty();
    }

    /**
     * Cek apakah ada pembayaran yang menunggu verifikasi
     * 
     * @param Collection $payments Koleksi pembayaran
     * @return bool
     */
    private function hasPendingPayments(Collection $payments): bool
    {
        return $payments
            ->where('status', PaymentStatus::MENUNGGU_VERIFIKASI->value)
            ->isNotEmpty();
    }
}

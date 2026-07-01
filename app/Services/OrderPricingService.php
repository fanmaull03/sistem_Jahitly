<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderStatusUpdated;

/**
 * OrderPricingService - Mengelola pricing dan pembayaran DP
 * 
 * Service ini menangani logika terkait harga order:
 * - Set nominal DP untuk order
 * - Update estimasi harga
 * - Calculate harga dengan berbagai faktor (quantity, material, queue, etc)
 * - Validasi harga
 * - Notifikasi ke customer tentang harga
 * 
 * Principle:
 * - Delegate complex calculation ke PaymentService & OrderBusinessRulesService
 * - Focus on DP & pricing specific logic
 */
class OrderPricingService
{
    // ──────────────────────────────────────────────────────────
    // Dependencies
    // ──────────────────────────────────────────────────────────

    private OrderBusinessRulesService $businessRules;

    public function __construct()
    {
        $this->businessRules = app(OrderBusinessRulesService::class);
    }

    // ──────────────────────────────────────────────────────────
    // Set DP Amount
    // ──────────────────────────────────────────────────────────

    /**
     * Set nominal DP untuk order
     * 
     * Validasi:
     * - DP harus numerik
     * - Minimal Rp 1.000
     * - DP tidak boleh melebihi estimasi harga
     * 
     * @param Order $order Order yang di-set DP-nya
     * @param float $dpAmount Nominal DP
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string, errors?: list<string>}
     */
    public function setDpAmount(Order $order, float $dpAmount, int $adminId): array
    {
        // Validasi DP
        $validation = $this->validateDpAmount($dpAmount, $order);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'DP tidak valid.',
                'errors' => $validation['errors'],
            ];
        }

        // Update order
        $order->update(['dp_amount' => $dpAmount]);

        // Notify customer
        $this->notifyCustomerAboutDp($order, $dpAmount);

        return [
            'success' => true,
            'message' => 'Nominal DP berhasil ditetapkan. Notifikasi dikirim ke customer.',
        ];
    }

    /**
     * Validasi nominal DP
     * 
     * Rules:
     * - Harus numerik (bukan string)
     * - Minimal Rp 1.000
     * - Maksimal sama dengan estimasi harga (atau 0 jika belum ada estimasi)
     * 
     * @param float $dpAmount Nominal DP yang divalidasi
     * @param Order $order Order untuk context (estimasi harga)
     * @return array{valid: bool, errors?: list<string>}
     */
    private function validateDpAmount(float $dpAmount, Order $order): array
    {
        $errors = [];

        if ($dpAmount < 1000) {
            $errors[] = 'Nominal DP minimal Rp 1.000.';
        }

        // Jika ada estimasi harga, DP tidak boleh lebih dari itu
        if ($order->estimated_price && $dpAmount > $order->estimated_price) {
            $errors[] = 'DP tidak boleh melebihi estimasi harga (Rp ' . 
                       number_format($order->estimated_price, 0, ',', '.') . ').';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Update Estimated Price & Date
    // ──────────────────────────────────────────────────────────

    /**
     * Update estimasi harga dan tanggal selesai berdasarkan kondisi order
     * 
     * Faktor-faktor yang mempengaruhi:
     * - Quantity
     * - Harga service base
     * - Material yang dipilih
     * - Status material (PO atau ready)
     * - Queue position
     * 
     * @param Order $order Order yang di-recalculate
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string, new_price?: float, new_date?: string}
     */
    public function recalculateEstimation(Order $order, int $adminId): array
    {
        // Calculate menggunakan service
        $estimation = $this->businessRules->calculateEstimation($order);

        // Update order
        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        // Notify customer tentang perubahan
        $this->notifyCustomerAboutPriceChange(
            $order,
            $estimation['estimated_price'],
            $estimation['estimated_finish_date']
        );

        return [
            'success' => true,
            'message' => 'Estimasi harga dan tanggal selesai berhasil diperbarui.',
            'new_price' => $estimation['estimated_price'],
            'new_date' => $estimation['estimated_finish_date']->format('d-m-Y'),
        ];
    }

    /**
     * Manually set estimasi harga
     * 
     * Digunakan jika admin ingin override automatic calculation
     * (misal ada special case atau custom adjustment)
     * 
     * @param Order $order
     * @param float $price Harga manual
     * @param int $adminId Admin yang melakukan action
     * @return array{success: bool, message: string}
     */
    public function setEstimatedPrice(Order $order, float $price, int $adminId): array
    {
        // Validasi harga
        $validation = $this->validatePrice($price);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => 'Harga tidak valid.',
            ];
        }

        $oldPrice = $order->estimated_price;

        // Update order
        $order->update(['estimated_price' => $price]);

        // Notify jika ada perubahan signifikan
        if ($oldPrice && abs($price - $oldPrice) > ($oldPrice * 0.1)) {
            $this->notifyCustomerAboutPriceChange(
                $order,
                $price,
                $order->estimated_finish_date
            );
        }

        return [
            'success' => true,
            'message' => 'Estimasi harga berhasil diubah.',
        ];
    }

    /**
     * Validasi format harga
     * 
     * @param float $price Harga yang divalidasi
     * @return array{valid: bool}
     */
    private function validatePrice(float $price): array
    {
        $valid = $price > 0;

        return ['valid' => $valid];
    }

    // ──────────────────────────────────────────────────────────
    // Get Pricing Information
    // ──────────────────────────────────────────────────────────

    /**
     * Dapatkan sisa yang harus dibayar (pelunasan)
     * 
     * = estimated_price - total_dp_terverifikasi
     * 
     * @param Order $order
     * @return float Sisa pembayaran
     */
    public function getRemainingPayment(Order $order): float
    {
        $order->loadMissing('payments');

        $totalVerifiedDp = $order->payments()
            ->where('payment_type', 'dp')
            ->where('status', 'terverifikasi')
            ->sum('amount');

        $remaining = max(0, $order->estimated_price - $totalVerifiedDp);

        return (float) $remaining;
    }

    /**
     * Cek apakah DP sudah ditetapkan
     * 
     * @param Order $order
     * @return bool
     */
    public function isDpSet(Order $order): bool
    {
        return $order->dp_amount && $order->dp_amount > 0;
    }

    /**
     * Format harga untuk display
     * 
     * @param float $amount Jumlah yang diformat
     * @return string Harga dalam format Rp X.XXX
     */
    public function formatPrice(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    // ──────────────────────────────────────────────────────────
    // Notifications
    // ──────────────────────────────────────────────────────────

    /**
     * Notifikasi ke customer tentang DP yang sudah ditetapkan
     * 
     * @param Order $order
     * @param float $dpAmount
     * @return void
     */
    private function notifyCustomerAboutDp(Order $order, float $dpAmount): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        $message = 'Admin telah menetapkan DP pesanan #' . $order->order_number . 
                   ' sebesar ' . $this->formatPrice($dpAmount) . 
                   '. Silakan lakukan pembayaran.';

        $customer->notify(new OrderStatusUpdated($order, $message));
    }

    /**
     * Notifikasi ke customer tentang perubahan harga
     * 
     * @param Order $order
     * @param float $newPrice Harga baru
     * @param \Carbon\Carbon $estimatedDate Tanggal selesai estimasi
     * @return void
     */
    private function notifyCustomerAboutPriceChange(Order $order, float $newPrice, $estimatedDate): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        $message = 'Estimasi harga pesanan #' . $order->order_number . 
                   ' adalah ' . $this->formatPrice($newPrice) . 
                   ' dengan target selesai ' . $estimatedDate->format('d/m/Y') . '.';

        $customer->notify(new OrderStatusUpdated($order, $message));
    }
}

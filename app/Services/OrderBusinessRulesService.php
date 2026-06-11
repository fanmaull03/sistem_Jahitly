<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderBusinessRulesService
{
    /**
     * Daftar tipe layanan yang memerlukan appointment dan DP sebelum diproses.
     *
     * @var list<string>
     */
    private const REQUIRES_APPOINTMENT_TYPES = ['seragam', 'custom'];

    // ──────────────────────────────────────────────────────────
    // 1. canMoveToProcessing
    // ──────────────────────────────────────────────────────────

    /**
     * Mengecek apakah pesanan boleh dipindahkan ke status "diproses".
     *
     * Aturan bisnis:
     * - Untuk layanan seragam & custom:
     *     • Harus ada appointment dengan status "selesai"
     *     • Harus ada payment dengan type "dp" dan status "terverifikasi"
     * - Untuk semua layanan:
     *     • material_status harus "ready" (bukan "po" atau null)
     *
     * @param  Order  $order  Pesanan yang akan dicek (relasi service, appointment, payments harus tersedia)
     * @return array{can_proceed: bool, blocking_reasons: list<string>}
     */
    public function canMoveToProcessing(Order $order): array
    {
        $blockingReasons = [];

        // Eager-load relasi yang dibutuhkan jika belum di-load
        $order->loadMissing(['service', 'appointment', 'payments']);

        $serviceType = $order->service->type;

        // ── Rules khusus seragam & custom ──────────────────────
        if (in_array($serviceType, self::REQUIRES_APPOINTMENT_TYPES, true)) {

            // Cek appointment selesai
            $appointmentCompleted = $order->appointment
                && $order->appointment->status === 'selesai';

            if (! $appointmentCompleted) {
                $blockingReasons[] = 'Appointment belum selesai. Untuk layanan ' . $serviceType
                    . ', customer harus menyelesaikan appointment terlebih dahulu.';
            }

            // Cek DP terverifikasi
            $hasDpVerified = $order->payments
                ->where('payment_type', 'dp')
                ->where('status', 'terverifikasi')
                ->isNotEmpty();

            if (! $hasDpVerified) {
                $blockingReasons[] = 'Pembayaran DP belum terverifikasi. Untuk layanan ' . $serviceType
                    . ', DP harus sudah diverifikasi sebelum proses dimulai.';
            }
        }

        // ── Rule universal: material harus ready ───────────────
        if ($order->material_status !== 'ready') {
            $currentStatus = $order->material_status ?? 'belum ditentukan';
            $blockingReasons[] = 'Bahan belum siap (status saat ini: ' . $currentStatus
                . '). Bahan harus berstatus "ready" sebelum pesanan dapat diproses.';
        }

        return [
            'can_proceed' => empty($blockingReasons),
            'blocking_reasons' => $blockingReasons,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // 2. canMarkAsComplete
    // ──────────────────────────────────────────────────────────

    /**
     * Mengecek apakah pesanan boleh ditandai sebagai "selesai".
     *
     * Syarat: total pembayaran terverifikasi harus >= estimated_price pesanan.
     *
     * @param  Order  $order  Pesanan yang akan dicek (relasi payments harus tersedia)
     * @return array{can_proceed: bool, blocking_reasons: list<string>}
     */
    public function canMarkAsComplete(Order $order): array
    {
        $blockingReasons = [];

        $order->loadMissing('payments');

        $totalVerified = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');

        $estimatedPrice = (float) $order->estimated_price;

        if ($estimatedPrice > 0 && $totalVerified < $estimatedPrice) {
            $kekurangan = $estimatedPrice - $totalVerified;
            $blockingReasons[] = 'Pembayaran belum lunas. Total terverifikasi: Rp '
                . number_format($totalVerified, 0, ',', '.')
                . ' dari Rp ' . number_format($estimatedPrice, 0, ',', '.')
                . ' (kekurangan Rp ' . number_format($kekurangan, 0, ',', '.') . ').';
        }

        if ($estimatedPrice <= 0) {
            $blockingReasons[] = 'Estimasi harga belum ditetapkan untuk pesanan ini.';
        }

        return [
            'can_proceed' => empty($blockingReasons),
            'blocking_reasons' => $blockingReasons,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // 3. calculateEstimation
    // ──────────────────────────────────────────────────────────

    /**
     * Menghitung estimasi harga dan tanggal selesai untuk sebuah pesanan.
     *
     * Formula durasi:
     *   base_duration_days
     *   + (jumlah pesanan aktif dalam antrian × rata-rata durasi layanan)
     *   + (7 hari tambahan jika material_status = "po")
     *
     * Formula harga:
     *   base_price × quantity
     *
     * @param  Order  $order  Pesanan yang akan dihitung (relasi service harus tersedia)
     * @return array{estimated_price: float, estimated_finish_date: \Carbon\Carbon}
     */
    public function calculateEstimation(Order $order): array
    {
        $order->loadMissing(['service', 'fabric']);

        $service = $order->service;

        // ── Hitung estimasi harga ──────────────────────────────
        $quantity = max(1, (int) $order->quantity);
        $estimatedPrice = (float) $service->base_price * $quantity;

        // Tambahkan harga bahan jika menggunakan bahan dari penjahit
        if ($order->fabric) {
            $estimatedPrice += (float) $order->fabric->price_per_meter * $quantity;
        }

        // ── Hitung estimasi durasi ─────────────────────────────
        $baseDuration = (int) $service->base_duration_days;

        // Hitung pesanan aktif (belum selesai) sebagai antrian
        $activeOrderCount = Order::whereNotIn('status', ['selesai'])
            ->where('id', '!=', $order->id)
            ->count();

        // Rata-rata durasi semua layanan
        $averageDuration = (float) Service::avg('base_duration_days');
        $averageDuration = max(1, $averageDuration); // minimal 1 hari

        // Tambahan hari karena antrian
        $queueDays = (int) ceil($activeOrderCount * $averageDuration);

        // Tambahan hari jika bahan PO
        // Prioritas: gunakan po_days dari fabric jika ada, fallback ke 7 hari
        $poDays = 0;
        if ($order->material_status === 'po') {
            if ($order->fabric && $order->fabric->po_days) {
                $poDays = $order->fabric->po_days;
            } else {
                $poDays = 7;
            }
        }

        $totalDays = $baseDuration + $queueDays + $poDays;

        $estimatedFinishDate = Carbon::now()->addDays($totalDays);

        return [
            'estimated_price' => $estimatedPrice,
            'estimated_finish_date' => $estimatedFinishDate,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // 4. isAppointmentSlotAvailable
    // ──────────────────────────────────────────────────────────

    /**
     * Mengecek apakah slot waktu appointment tersedia.
     *
     * Setiap appointment diasumsikan memakan waktu 1 jam.
     * Slot dianggap tidak tersedia jika ada appointment lain yang
     * waktunya overlap (dalam rentang ±1 jam) dan belum dibatalkan.
     *
     * @param  \Carbon\Carbon  $datetime  Waktu yang ingin dicek ketersediaannya
     * @return bool  True jika slot tersedia, false jika bentrok
     */
    public function isAppointmentSlotAvailable(Carbon $datetime): bool
    {
        $slotStart = $datetime->copy();
        $slotEnd = $datetime->copy()->addHour();

        // Cek apakah ada appointment yang overlap dan tidak dibatalkan.
        // Overlap terjadi jika: existing_start < slot_end AND existing_end > slot_start
        $conflicting = Appointment::where('status', '!=', 'dibatalkan')
            ->where('appointment_date', '<', $slotEnd)
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL 1 HOUR) > ?', [$slotStart])
            ->exists();

        return ! $conflicting;
    }

    // ──────────────────────────────────────────────────────────
    // 5. generateOrderNumber
    // ──────────────────────────────────────────────────────────

    /**
     * Generate nomor pesanan unik dengan format: JTL-YYYYMMDD-XXXX
     *
     * X = 4 digit angka random. Method ini memastikan nomor yang
     * dihasilkan unik di tabel orders sebelum dikembalikan.
     *
     * @return string  Nomor pesanan unik, contoh: JTL-20260419-3847
     */
    public function generateOrderNumber(): string
    {
        $dateSegment = Carbon::now()->format('Ymd');

        do {
            $randomSegment = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $orderNumber = 'JTL-' . $dateSegment . '-' . $randomSegment;
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}

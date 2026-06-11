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

    /**
     * Jam kerja workshop.
     */
    private const OPEN_HOUR = 8;   // 08:00
    private const CLOSE_HOUR = 19; // 19:00
    private const BREAK_START = 12; // 12:00
    private const BREAK_END = 13;   // 13:00

    /**
     * Durasi satu appointment dalam jam.
     */
    private const APPOINTMENT_DURATION_HOURS = 1;

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
     *   base_price × quantity + (fabric price_per_meter × quantity jika ada)
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
    // 4. Appointment Slot Management
    // ──────────────────────────────────────────────────────────

    /**
     * Mengecek apakah slot waktu appointment tersedia.
     *
     * Setiap appointment memakan waktu 1 jam.
     * Jam kerja: 08:00-19:00, istirahat: 12:00-13:00.
     * Slot dianggap tidak tersedia jika ada appointment lain yang overlap.
     *
     * @param  \Carbon\Carbon  $datetime  Waktu yang ingin dicek ketersediaannya
     * @return bool  True jika slot tersedia, false jika bentrok
     */
    public function isAppointmentSlotAvailable(Carbon $datetime): bool
    {
        $hour = (int) $datetime->format('H');

        // Cek jam operasional (08:00 - 19:00)
        if ($hour < self::OPEN_HOUR || $hour >= self::CLOSE_HOUR) {
            return false;
        }

        // Cek jam istirahat (12:00 - 13:00)
        if ($hour >= self::BREAK_START && $hour < self::BREAK_END) {
            return false;
        }

        $slotStart = $datetime->copy();
        $slotEnd = $datetime->copy()->addHours(self::APPOINTMENT_DURATION_HOURS);

        // Cek apakah ada appointment yang overlap dan tidak dibatalkan.
        // Overlap terjadi jika: existing_start < slot_end AND existing_end > slot_start
        $conflicting = Appointment::where('status', '!=', 'dibatalkan')
            ->where('appointment_date', '<', $slotEnd)
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL ? HOUR) > ?', [
                self::APPOINTMENT_DURATION_HOURS,
                $slotStart,
            ])
            ->exists();

        return ! $conflicting;
    }

    /**
     * Mendapatkan semua slot jam yang tersedia pada tanggal tertentu.
     *
     * @param  Carbon  $date  Tanggal yang ingin dicek
     * @return array<int, array{hour: int, time: string, available: bool, label: string}>
     */
    public function getAvailableSlots(Carbon $date): array
    {
        $slots = [];

        for ($hour = self::OPEN_HOUR; $hour < self::CLOSE_HOUR; $hour++) {
            $datetime = $date->copy()->setTime($hour, 0, 0);
            $isBreak = ($hour >= self::BREAK_START && $hour < self::BREAK_END);

            if ($isBreak) {
                $slots[] = [
                    'hour' => $hour,
                    'time' => sprintf('%02d:00', $hour),
                    'available' => false,
                    'label' => 'Jam Istirahat',
                ];
                continue;
            }

            // Untuk waktu yang sudah lewat hari ini, tandai tidak tersedia
            $isPast = $datetime->isPast();

            $available = !$isPast && $this->isAppointmentSlotAvailable($datetime);

            $label = 'Tersedia';
            if ($isPast) {
                $label = 'Sudah Lewat';
            } elseif (!$available) {
                $label = 'Sudah Terbooking';
            }

            $slots[] = [
                'hour' => $hour,
                'time' => sprintf('%02d:00', $hour),
                'available' => $available,
                'label' => $label,
            ];
        }

        return $slots;
    }

    /**
     * Mendapatkan daftar slot yang sudah di-booking pada tanggal tertentu.
     *
     * @param  Carbon  $date  Tanggal yang dicek
     * @return array<int, array{hour: int, time: string, customer: string, status: string}>
     */
    public function getBookedSlots(Carbon $date): array
    {
        $appointments = Appointment::with('customer')
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('appointment_date')
            ->get();

        return $appointments->map(function ($appointment) {
            return [
                'hour' => (int) $appointment->appointment_date->format('H'),
                'time' => $appointment->appointment_date->format('H:i'),
                'customer' => $appointment->customer->name ?? 'Unknown',
                'status' => $appointment->status,
            ];
        })->toArray();
    }

    // ──────────────────────────────────────────────────────────
    // 5. Fabric Stock Deduction
    // ──────────────────────────────────────────────────────────

    /**
     * Mengurangi stok bahan kain saat pesanan mulai diproses.
     * Setiap item pesanan menggunakan 1 meter bahan.
     *
     * @param  Order  $order  Pesanan yang bahan-nya akan dikurangi
     */
    public function deductFabricStock(Order $order): void
    {
        $order->loadMissing('fabric');

        if (! $order->fabric) {
            return;
        }

        $metersUsed = max(1, (int) $order->quantity);
        $order->fabric->deductStock($metersUsed);
    }

    // ──────────────────────────────────────────────────────────
    // 6. generateOrderNumber
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

    /**
     * Cek apakah layanan membutuhkan appointment.
     */
    public function requiresAppointment(string $serviceType): bool
    {
        return in_array($serviceType, self::REQUIRES_APPOINTMENT_TYPES, true);
    }
}

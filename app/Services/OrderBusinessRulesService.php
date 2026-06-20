<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;

class OrderBusinessRulesService
{
    /**
     * Tipe layanan yang memerlukan fitting/appointment.
     *
     * @var list<string>
     */
    private const REQUIRES_FITTING_TYPES = ['seragam', 'custom'];

    /**
     * Jam kerja workshop.
     */
    private const OPEN_HOUR = 8;
    private const CLOSE_HOUR = 19;
    private const BREAK_START = 12;
    private const BREAK_END = 13;

    /**
     * Durasi satu appointment dalam jam.
     */
    private const APPOINTMENT_DURATION_HOURS = 1;

    // ──────────────────────────────────────────────────────────
    // Status Transition Rules
    // ──────────────────────────────────────────────────────────

    /**
     * Daftar transisi status yang diperbolehkan.
     *
     * @return array<string, list<string>>
     */
    public function getAllowedTransitions(): array
    {
        return [
            'menunggu_konfirmasi' => ['ditolak', 'menunggu_pakaian_dikirim', 'menunggu_fitting', 'menunggu_dp', 'dalam_antrian'],
            'menunggu_pakaian_dikirim' => ['pakaian_dikirim', 'dibatalkan'],
            'pakaian_dikirim'    => ['dalam_antrian'],
            'menunggu_fitting'   => ['menunggu_dp', 'dalam_antrian', 'dibatalkan'],
            'menunggu_dp'        => ['menunggu_bahan', 'dalam_antrian', 'dibatalkan'],
            'menunggu_bahan'     => ['dalam_antrian', 'dibatalkan'],
            'dalam_antrian'      => ['dijahit'],
            'dijahit'            => ['selesai_produksi'],
            'selesai_produksi'   => ['siap_diambil'],
            'siap_diambil'       => ['selesai'],
            // Terminal states — no outgoing transitions
            'selesai'            => [],
            'ditolak'            => [],
            'dibatalkan'         => [],
        ];
    }

    /**
     * Cek apakah transisi dari $from ke $to diperbolehkan.
     */
    public function canTransition(string $from, string $to): bool
    {
        $allowed = $this->getAllowedTransitions();

        return isset($allowed[$from]) && in_array($to, $allowed[$from], true);
    }

    /**
     * Daftar status berikutnya yang valid dari status saat ini.
     *
     * @return list<string>
     */
    public function getNextStatuses(string $currentStatus): array
    {
        $allowed = $this->getAllowedTransitions();
        return $allowed[$currentStatus] ?? [];
    }

    // ──────────────────────────────────────────────────────────
    // Business Rule Checks
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah pesanan bisa masuk antrian produksi.
     * Syarat: DP terverifikasi + bahan ready.
     *
     * @return array{can_proceed: bool, blocking_reasons: list<string>}
     */
    public function canMoveToQueue(Order $order): array
    {
        $blockingReasons = [];
        $order->loadMissing(['service', 'appointment', 'payments']);

        // Cek fitting selesai (untuk custom/seragam/vermak)
        if ($this->requiresFitting($order->service->type)) {
            $fittingDone = $order->appointment && $order->appointment->status === 'selesai';
            if (!$fittingDone) {
                $blockingReasons[] = 'Fitting belum selesai.';
            }
        }

        if ($order->service->type !== 'vermak') {
            // Cek DP terverifikasi
            $hasDpVerified = $order->payments
                ->where('payment_type', 'dp')
                ->where('status', 'terverifikasi')
                ->isNotEmpty();

            if (!$hasDpVerified) {
                $blockingReasons[] = 'Pembayaran DP belum terverifikasi.';
            }

            // Cek material ready
            if ($order->material_status !== 'ready') {
                $blockingReasons[] = 'Bahan belum siap (status: ' . ($order->material_status ?? 'belum ditentukan') . ').';
            }
        }

        return [
            'can_proceed' => empty($blockingReasons),
            'blocking_reasons' => $blockingReasons,
        ];
    }

    /**
     * Cek apakah pesanan bisa ditandai siap diambil.
     * Syarat: pelunasan lunas (total pembayaran >= estimated_price).
     *
     * @return array{can_proceed: bool, blocking_reasons: list<string>}
     */
    public function canMarkReadyForPickup(Order $order): array
    {
        $blockingReasons = [];
        $order->loadMissing('payments');

        $totalVerified = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');

        $estimatedPrice = (float) $order->estimated_price;

        if ($estimatedPrice <= 0) {
            $blockingReasons[] = 'Estimasi harga belum ditetapkan.';
        } elseif ($totalVerified < $estimatedPrice) {
            $kekurangan = $estimatedPrice - $totalVerified;
            $blockingReasons[] = 'Pembayaran belum lunas. Kekurangan: Rp ' . number_format($kekurangan, 0, ',', '.') . '.';
        }

        return [
            'can_proceed' => empty($blockingReasons),
            'blocking_reasons' => $blockingReasons,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Helper: requiresFitting
    // ──────────────────────────────────────────────────────────

    /**
     * Cek apakah layanan membutuhkan fitting.
     */
    public function requiresFitting(string $serviceType): bool
    {
        return in_array($serviceType, self::REQUIRES_FITTING_TYPES, true);
    }

    // ──────────────────────────────────────────────────────────
    // Estimation
    // ──────────────────────────────────────────────────────────

    /**
     * Menghitung estimasi harga dan tanggal selesai.
     *
     * @return array{estimated_price: float, estimated_finish_date: Carbon}
     */
    public function calculateEstimation(Order $order): array
    {
        $order->loadMissing(['service', 'fabric']);

        $service = $order->service;
        $quantity = max(1, (int) $order->quantity);
        $estimatedPrice = (float) $service->base_price * $quantity;

        // Tambahkan harga bahan jika menggunakan bahan dari penjahit
        if ($order->fabric) {
            $estimatedPrice += (float) $order->fabric->price_per_meter * $quantity;
        }

        // Hitung estimasi durasi
        $baseDuration = (int) $service->base_duration_days;

        $activeOrderCount = Order::whereNotIn('status', ['selesai', 'ditolak', 'dibatalkan'])
            ->where('id', '!=', $order->id)
            ->count();

        $averageDuration = max(1, (float) Service::avg('base_duration_days'));
        $queueDays = (int) ceil($activeOrderCount * $averageDuration);

        $poDays = 0;
        if ($order->material_status === 'po') {
            $poDays = $order->po_days ?? ($order->fabric?->po_days ?? 7);
        }

        $totalDays = $baseDuration + $queueDays + $poDays;
        $estimatedFinishDate = Carbon::now()->addDays($totalDays);

        return [
            'estimated_price' => $estimatedPrice,
            'estimated_finish_date' => $estimatedFinishDate,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Appointment Slot Management
    // ──────────────────────────────────────────────────────────

    /**
     * Mengecek apakah slot waktu appointment tersedia.
     */
    public function isAppointmentSlotAvailable(Carbon $datetime): bool
    {
        $hour = (int) $datetime->format('H');

        if ($hour < self::OPEN_HOUR || $hour >= self::CLOSE_HOUR) {
            return false;
        }

        if ($hour >= self::BREAK_START && $hour < self::BREAK_END) {
            return false;
        }

        $slotStart = $datetime->copy();
        $slotEnd = $datetime->copy()->addHours(self::APPOINTMENT_DURATION_HOURS);

        $conflicting = Appointment::where('status', '!=', 'dibatalkan')
            ->where('appointment_date', '<', $slotEnd)
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL ? HOUR) > ?', [
                self::APPOINTMENT_DURATION_HOURS,
                $slotStart,
            ])
            ->exists();

        return !$conflicting;
    }

    /**
     * Mendapatkan semua slot jam yang tersedia pada tanggal tertentu.
     *
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
    // Fabric Stock Deduction
    // ──────────────────────────────────────────────────────────

    /**
     * Mengurangi stok bahan kain saat pesanan mulai diproses.
     */
    public function deductFabricStock(Order $order): void
    {
        $order->loadMissing('fabric');

        if (!$order->fabric) {
            return;
        }

        $metersUsed = max(1, (int) $order->quantity);
        $order->fabric->deductStock($metersUsed);
    }

    // ──────────────────────────────────────────────────────────
    // Generate Order Number
    // ──────────────────────────────────────────────────────────

    /**
     * Generate nomor pesanan unik: JTL-YYYYMMDD-XXXX
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

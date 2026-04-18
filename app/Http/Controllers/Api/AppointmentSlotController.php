<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentSlotController extends Controller
{
    /**
     * Jam operasional mulai dan selesai.
     */
    private const OPERATING_HOUR_START = 8;
    private const OPERATING_HOUR_END = 17;

    // ──────────────────────────────────────────────────────────
    // availableSlots — Daftar slot yang tersedia
    // ──────────────────────────────────────────────────────────

    /**
     * Mengembalikan daftar slot jam yang masih tersedia di tanggal tertentu.
     *
     * Jam operasional: 08:00 - 17:00, per 1 jam.
     * Slot yang sudah terisi (appointment aktif) akan ditandai unavailable.
     *
     * Query parameter:
     * - date (required): format YYYY-MM-DD
     *
     * Response JSON:
     * {
     *   "date": "2026-04-20",
     *   "slots": [
     *     { "time": "08:00", "available": true },
     *     { "time": "09:00", "available": false },
     *     ...
     *   ]
     * }
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = Carbon::parse($request->input('date'));

        // Ambil semua appointment aktif (bukan dibatalkan) di tanggal tersebut
        $bookedSlots = Appointment::where('status', '!=', 'dibatalkan')
            ->whereDate('appointment_date', $date)
            ->pluck('appointment_date')
            ->map(fn ($dt) => Carbon::parse($dt)->format('H:00'))
            ->toArray();

        // Generate semua slot dari jam operasional
        $slots = [];
        for ($hour = self::OPERATING_HOUR_START; $hour < self::OPERATING_HOUR_END; $hour++) {
            $timeFormatted = str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
            $slotDatetime = $date->copy()->setHour($hour)->setMinute(0)->setSecond(0);

            // Slot tidak tersedia jika sudah terisi atau sudah lewat (untuk hari ini)
            $isBooked = in_array($timeFormatted, $bookedSlots, true);
            $isPast = $slotDatetime->isPast();

            $slots[] = [
                'time' => $timeFormatted,
                'available' => ! $isBooked && ! $isPast,
            ];
        }

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'slots' => $slots,
        ]);
    }
}

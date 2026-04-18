<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Order;
use App\Services\OrderBusinessRulesService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Inject OrderBusinessRulesService via constructor.
     */
    public function __construct(
        private readonly OrderBusinessRulesService $orderBusinessRules
    ) {}

    // ──────────────────────────────────────────────────────────
    // 1. create — Form booking appointment
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan form booking appointment dengan informasi slot tersedia.
     *
     * Policy check memastikan:
     * - Customer hanya bisa booking untuk order miliknya
     * - Order harus bertipe seragam/custom
     * - Belum ada appointment aktif untuk order ini
     */
    public function create(Order $order): View
    {
        Gate::authorize('create', [Appointment::class, $order]);

        $order->load('service');

        return view('appointments.create', compact('order'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. store — Simpan appointment baru
    // ──────────────────────────────────────────────────────────

    /**
     * Menyimpan appointment baru setelah validasi slot.
     *
     * Alur:
     * 1. Authorize via AppointmentPolicy
     * 2. Validasi input via StoreAppointmentRequest
     * 3. Parse datetime dan validasi jam operasional (08:00-17:00)
     * 4. Cek ketersediaan slot via OrderBusinessRulesService
     * 5. Buat record appointment dengan status "menunggu"
     */
    public function store(StoreAppointmentRequest $request, Order $order): RedirectResponse
    {
        Gate::authorize('create', [Appointment::class, $order]);

        $validated = $request->validated();

        $appointmentDate = Carbon::parse($validated['appointment_date']);

        // Validasi jam operasional (08:00 - 17:00)
        $hour = (int) $appointmentDate->format('H');
        if ($hour < 8 || $hour >= 17) {
            return redirect()
                ->back()
                ->withErrors(['appointment_date' => 'Jam appointment harus antara 08:00 - 17:00.'])
                ->withInput();
        }

        // Cek ketersediaan slot via business rules service
        if (! $this->orderBusinessRules->isAppointmentSlotAvailable($appointmentDate)) {
            return redirect()
                ->back()
                ->withErrors(['appointment_date' => 'Slot waktu ini sudah terisi. Silakan pilih waktu lain.'])
                ->withInput();
        }

        // Buat appointment
        Appointment::create([
            'order_id' => $order->id,
            'customer_id' => auth()->id(),
            'appointment_date' => $appointmentDate,
            'status' => 'menunggu',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Appointment berhasil dibuat untuk tanggal '
                . $appointmentDate->translatedFormat('l, d F Y H:i') . '. Menunggu konfirmasi admin.');
    }
}

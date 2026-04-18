<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\OrderStatusLog;
use App\Services\OrderBusinessRulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    /**
     * Inject OrderBusinessRulesService via constructor.
     */
    public function __construct(
        private readonly OrderBusinessRulesService $orderBusinessRules
    ) {}

    // ──────────────────────────────────────────────────────────
    // 1. index — Daftar semua appointment
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan semua appointment dengan kemampuan filter berdasarkan tanggal.
     *
     * Query parameters:
     * - date (optional): filter berdasarkan tanggal (format YYYY-MM-DD)
     */
    public function index(Request $request): View
    {
        $query = Appointment::with(['order.service', 'customer'])
            ->latest('appointment_date');

        // Filter berdasarkan tanggal jika parameter diberikan
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->input('date'));
        }

        $appointments = $query->paginate(15);

        return view('admin.appointments.index', compact('appointments'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. confirm — Konfirmasi appointment
    // ──────────────────────────────────────────────────────────

    /**
     * Mengkonfirmasi appointment, mengubah status menjadi "terkonfirmasi".
     *
     * Hanya bisa dilakukan jika status saat ini adalah "menunggu".
     */
    public function confirm(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('manage', $appointment);

        if ($appointment->status !== 'menunggu') {
            return redirect()
                ->back()
                ->with('error', 'Hanya appointment dengan status "menunggu" yang bisa dikonfirmasi.');
        }

        $appointment->update(['status' => 'terkonfirmasi']);

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Appointment untuk pesanan '
                . $appointment->order->order_number . ' berhasil dikonfirmasi.');
    }

    // ──────────────────────────────────────────────────────────
    // 3. complete — Tandai appointment selesai
    // ──────────────────────────────────────────────────────────

    /**
     * Menandai appointment sebagai selesai dan otomatis update status order
     * jika syarat lain sudah terpenuhi.
     *
     * Alur:
     * 1. Validasi status appointment harus "terkonfirmasi"
     * 2. Update appointment status → selesai
     * 3. Cek apakah order bisa pindah ke "diproses" via canMoveToProcessing
     * 4. Jika bisa, update status order dan buat status log
     * 5. Jika belum bisa, informasikan alasan via flash message
     */
    public function complete(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('manage', $appointment);

        if ($appointment->status !== 'terkonfirmasi') {
            return redirect()
                ->back()
                ->with('error', 'Hanya appointment dengan status "terkonfirmasi" yang bisa ditandai selesai.');
        }

        // Update status appointment
        $appointment->update(['status' => 'selesai']);

        $order = $appointment->order;
        $message = 'Appointment berhasil ditandai selesai.';

        // Cek apakah order bisa otomatis pindah ke "diproses"
        $check = $this->orderBusinessRules->canMoveToProcessing($order);

        if ($check['can_proceed']) {
            // Update status order ke diproses
            $order->update(['status' => 'diproses']);

            // Simpan status log
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'diproses',
                'changed_by' => auth()->id(),
                'notes' => 'Status otomatis berubah setelah appointment selesai dan semua syarat terpenuhi.',
            ]);

            $message .= ' Pesanan ' . $order->order_number
                . ' otomatis dipindahkan ke status "diproses" karena semua syarat terpenuhi.';
        } else {
            $message .= ' Namun pesanan ' . $order->order_number
                . ' belum bisa diproses karena: '
                . implode(' ', $check['blocking_reasons']);
        }

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', $message);
    }
}

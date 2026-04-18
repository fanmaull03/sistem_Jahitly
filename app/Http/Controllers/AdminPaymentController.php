<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectPaymentRequest;
use App\Models\Payment;
use App\Services\OrderBusinessRulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    /**
     * Inject OrderBusinessRulesService via constructor.
     */
    public function __construct(
        private readonly OrderBusinessRulesService $orderBusinessRules
    ) {}

    // ──────────────────────────────────────────────────────────
    // 1. index — Daftar pembayaran menunggu verifikasi
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan daftar semua pembayaran yang menunggu verifikasi.
     *
     * Diurutkan dari yang terlama (FIFO) agar pembayaran yang
     * lebih dulu masuk bisa diverifikasi terlebih dahulu.
     */
    public function index(): View
    {
        $payments = Payment::with(['order.service', 'customer'])
            ->where('status', 'menunggu_verifikasi')
            ->oldest()
            ->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. verify — Detail pembayaran + preview bukti
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan detail pembayaran lengkap beserta preview bukti transfer.
     *
     * Admin bisa melihat informasi pembayaran dan file bukti sebelum
     * memutuskan untuk approve atau reject.
     */
    public function verify(Payment $payment): View
    {
        Gate::authorize('verify', $payment);

        $payment->load(['order.service', 'customer', 'order.payments']);

        // Hitung konteks pembayaran untuk order ini
        $order = $payment->order;
        $totalVerified = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');
        $estimatedPrice = (float) $order->estimated_price;
        $remainingAfterThis = max(0, $estimatedPrice - $totalVerified - (float) $payment->amount);

        return view('admin.payments.verify', compact(
            'payment',
            'totalVerified',
            'estimatedPrice',
            'remainingAfterThis'
        ));
    }

    // ──────────────────────────────────────────────────────────
    // 3. approve — Setujui pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menyetujui pembayaran dan mengupdate status terkait.
     *
     * Alur:
     * 1. Authorize via PaymentPolicy
     * 2. Update status payment → terverifikasi
     * 3. Catat verified_by dan verified_at
     * 4. Cek apakah order sudah lunas penuh (total terverifikasi >= estimated_price)
     * 5. Jika lunas, informasikan admin via flash message
     */
    public function approve(Payment $payment): RedirectResponse
    {
        Gate::authorize('verify', $payment);

        // Update status pembayaran
        $payment->update([
            'status' => 'terverifikasi',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_note' => null, // Bersihkan jika sebelumnya pernah ditolak
        ]);

        // Cek apakah order sudah lunas penuh
        $order = $payment->order;
        $order->load('payments');

        $totalVerified = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');

        $estimatedPrice = (float) $order->estimated_price;

        $message = 'Pembayaran berhasil diverifikasi.';

        if ($estimatedPrice > 0 && $totalVerified >= $estimatedPrice) {
            $message .= ' Pesanan ' . $order->order_number . ' sudah LUNAS'
                . ' (Total: Rp ' . number_format($totalVerified, 0, ',', '.')
                . ' / Rp ' . number_format($estimatedPrice, 0, ',', '.') . ').';
        } else {
            $kekurangan = max(0, $estimatedPrice - $totalVerified);
            $message .= ' Sisa pembayaran pesanan ' . $order->order_number
                . ': Rp ' . number_format($kekurangan, 0, ',', '.') . '.';
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', $message);
    }

    // ──────────────────────────────────────────────────────────
    // 4. reject — Tolak pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menolak pembayaran dengan alasan.
     *
     * Alur:
     * 1. Authorize via PaymentPolicy
     * 2. Validasi rejection_note via RejectPaymentRequest
     * 3. Update status payment → ditolak
     * 4. Catat rejection_note, verified_by, verified_at
     */
    public function reject(RejectPaymentRequest $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('verify', $payment);

        $validated = $request->validated();

        // Update status pembayaran menjadi ditolak
        $payment->update([
            'status' => 'ditolak',
            'rejection_note' => $validated['rejection_note'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Pembayaran ditolak. Customer akan melihat alasan penolakan.');
    }
}

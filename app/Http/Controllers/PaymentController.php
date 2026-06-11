<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // 1. create — Form pembayaran
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan form pembayaran untuk pesanan tertentu.
     *
     * Otomatis menentukan tipe pembayaran (DP atau pelunasan)
     * berdasarkan riwayat pembayaran sebelumnya:
     * - Jika belum ada DP terverifikasi → tipe = dp
     * - Jika sudah ada DP terverifikasi → tipe = pelunasan
     *
     * Juga menghitung sisa yang harus dibayar untuk pelunasan.
     */
    public function create(Order $order): View
    {
        // Pastikan customer hanya bisa membayar pesanan miliknya
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['service', 'payments']);

        // Tentukan tipe pembayaran berdasarkan riwayat
        $hasVerifiedDp = $order->payments
            ->where('payment_type', 'dp')
            ->where('status', 'terverifikasi')
            ->isNotEmpty();

        $paymentType = $hasVerifiedDp ? 'pelunasan' : 'dp';

        // Hitung total yang sudah dibayar (terverifikasi)
        $totalPaid = $order->payments
            ->where('status', 'terverifikasi')
            ->sum('amount');

        // Hitung sisa yang harus dibayar
        $remainingAmount = max(0, (float) $order->estimated_price - (float) $totalPaid);

        // Cek apakah ada pembayaran yang masih menunggu verifikasi
        $hasPendingPayment = $order->payments
            ->where('status', 'menunggu_verifikasi')
            ->isNotEmpty();

        return view('payments.create', compact(
            'order',
            'paymentType',
            'totalPaid',
            'remainingAmount',
            'hasPendingPayment'
        ));
    }

    // ──────────────────────────────────────────────────────────
    // 2. store — Simpan pembayaran baru
    // ──────────────────────────────────────────────────────────

    /**
     * Menyimpan data pembayaran baru.
     *
     * Alur:
     * 1. Validasi input via StorePaymentRequest
     * 2. Tentukan tipe pembayaran (dp/pelunasan) otomatis
     * 3. Upload file bukti ke storage/app/private/payment-proofs/ (jika ada)
     * 4. Tentukan status awal:
     *    - transfer/qris → menunggu_verifikasi
     *    - cash → menunggu_verifikasi (admin harus tetap verifikasi)
     * 5. Buat record payment
     */
    public function store(StorePaymentRequest $request, Order $order): RedirectResponse
    {
        // Pastikan customer hanya bisa membayar pesanan miliknya
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $validated = $request->validated();

        $order->load('payments');

        // Tentukan tipe pembayaran otomatis
        $hasVerifiedDp = $order->payments
            ->where('payment_type', 'dp')
            ->where('status', 'terverifikasi')
            ->isNotEmpty();

        $paymentType = $hasVerifiedDp ? 'pelunasan' : 'dp';

        // Upload file bukti jika ada
        $proofFilePath = null;
        if ($request->hasFile('proof_file')) {
            $proofFilePath = $request->file('proof_file')
                ->store('payment-proofs', 'local');
        }

        // Status awal selalu menunggu_verifikasi
        // Admin harus memverifikasi semua jenis pembayaran
        $status = 'menunggu_verifikasi';

        // Simpan record payment
        $payment = Payment::create([
            'order_id' => $order->id,
            'customer_id' => auth()->id(),
            'payment_type' => $paymentType,
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'proof_file_path' => $proofFilePath,
            'status' => $status,
        ]);

        $typeLabel = $paymentType === 'dp' ? 'DP' : 'Pelunasan';

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pembayaran ' . $typeLabel . ' sebesar Rp '
                . number_format($validated['amount'], 0, ',', '.')
                . ' berhasil dikirim dan menunggu verifikasi.');
    }

    // ──────────────────────────────────────────────────────────
    // 3. proofFile — Akses file bukti secara aman
    // ──────────────────────────────────────────────────────────

    /**
     * Mengakses file bukti pembayaran secara aman.
     *
     * File disimpan di storage/app/private/ sehingga tidak bisa
     * diakses langsung via URL publik. Route ini memastikan hanya
     * customer pemilik atau admin yang bisa mengakses file.
     */
    public function proofFile(Payment $payment)
    {
        // Authorize via PaymentPolicy
        Gate::authorize('viewProof', $payment);

        $filePath = storage_path('app/private/' . $payment->proof_file_path);

        if (! file_exists($filePath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->file($filePath);
    }
}

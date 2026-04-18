<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderMaterialRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\OrderBusinessRulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    /**
     * Inject OrderBusinessRulesService via constructor.
     */
    public function __construct(
        private readonly OrderBusinessRulesService $orderBusinessRules
    ) {}

    // ──────────────────────────────────────────────────────────
    // 1. index — Daftar semua pesanan dengan filter status
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan semua pesanan dengan kemampuan filter berdasarkan status.
     *
     * Query parameter:
     * - status (optional): filter berdasarkan status pesanan
     */
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'service'])->latest();

        // Filter berdasarkan status jika parameter diberikan
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(15);

        // Daftar status yang tersedia untuk dropdown filter
        $statuses = [
            'menunggu_appointment',
            'menunggu_bahan',
            'diproses',
            'dijahit',
            'finishing',
            'selesai',
        ];

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. show — Detail pesanan lengkap
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan detail pesanan lengkap beserta semua relasi.
     */
    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'service',
            'statusLogs.user',
            'designFiles',
            'payments',
            'appointment',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // ──────────────────────────────────────────────────────────
    // 3. updateStatus — Update status pesanan
    // ──────────────────────────────────────────────────────────

    /**
     * Mengupdate status pesanan dengan validasi bisnis.
     *
     * Alur:
     * 1. Validasi input via UpdateOrderStatusRequest
     * 2. Jika status baru = "diproses", cek via canMoveToProcessing
     * 3. Jika status baru = "selesai", cek via canMarkAsComplete
     * 4. Update status order
     * 5. Simpan ke order_status_logs
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();
        $newStatus = $validated['status'];

        // Validasi aturan bisnis berdasarkan status tujuan
        if ($newStatus === 'diproses') {
            $check = $this->orderBusinessRules->canMoveToProcessing($order);

            if (! $check['can_proceed']) {
                return redirect()
                    ->back()
                    ->withErrors(['status' => $check['blocking_reasons']])
                    ->withInput();
            }
        }

        if ($newStatus === 'selesai') {
            $check = $this->orderBusinessRules->canMarkAsComplete($order);

            if (! $check['can_proceed']) {
                return redirect()
                    ->back()
                    ->withErrors(['status' => $check['blocking_reasons']])
                    ->withInput();
            }
        }

        // Update status pesanan
        $order->update(['status' => $newStatus]);

        // Simpan ke order_status_logs
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'changed_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui menjadi "' . $newStatus . '".');
    }

    // ──────────────────────────────────────────────────────────
    // 4. updateMaterial — Update sumber dan status bahan
    // ──────────────────────────────────────────────────────────

    /**
     * Mengupdate sumber dan status bahan pesanan, lalu menghitung ulang estimasi.
     *
     * Alur:
     * 1. Validasi input via UpdateOrderMaterialRequest
     * 2. Update material_source dan material_status
     * 3. Recalculate estimasi harga & tanggal selesai
     * 4. Update order dengan data estimasi baru
     */
    public function updateMaterial(UpdateOrderMaterialRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        // Update data material
        $order->update([
            'material_source' => $validated['material_source'],
            'material_status' => $validated['material_status'],
        ]);

        // Recalculate estimasi setelah perubahan material
        $estimation = $this->orderBusinessRules->calculateEstimation($order);

        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Data bahan pesanan berhasil diperbarui dan estimasi telah dihitung ulang.');
    }
}

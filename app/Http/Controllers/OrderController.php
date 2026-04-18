<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UploadDesignRequest;
use App\Models\DesignFile;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Service;
use App\Services\OrderBusinessRulesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Inject OrderBusinessRulesService via constructor.
     */
    public function __construct(
        private readonly OrderBusinessRulesService $orderBusinessRules
    ) {}

    // ──────────────────────────────────────────────────────────
    // 1. index — Daftar pesanan milik customer yang login
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan daftar pesanan milik customer yang sedang login.
     *
     * Memuat relasi service dan mengurutkan dari pesanan terbaru.
     */
    public function index(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with('service')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // ──────────────────────────────────────────────────────────
    // 2. create — Form pemesanan baru
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan form pemesanan baru beserta daftar layanan yang tersedia.
     */
    public function create(): View
    {
        $services = Service::all();

        return view('orders.create', compact('services'));
    }

    // ──────────────────────────────────────────────────────────
    // 3. store — Simpan pesanan baru
    // ──────────────────────────────────────────────────────────

    /**
     * Menyimpan pesanan baru ke database.
     *
     * Alur:
     * 1. Validasi input via StoreOrderRequest
     * 2. Generate order number unik via OrderBusinessRulesService
     * 3. Tentukan status awal berdasarkan tipe layanan:
     *    - seragam/custom → menunggu_appointment
     *    - vermak → menunggu_bahan
     * 4. Buat record order
     * 5. Hitung estimasi harga & tanggal selesai via OrderBusinessRulesService
     * 6. Update order dengan data estimasi
     * 7. Simpan order_status_log pertama
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Ambil data layanan untuk menentukan status awal
        $service = Service::findOrFail($validated['service_id']);

        // Generate order number unik
        $orderNumber = $this->orderBusinessRules->generateOrderNumber();

        // Tentukan status awal berdasarkan tipe layanan
        $initialStatus = in_array($service->type, ['seragam', 'custom'], true)
            ? 'menunggu_appointment'
            : 'menunggu_bahan';

        // Buat record order
        $order = Order::create([
            'customer_id' => auth()->id(),
            'service_id' => $validated['service_id'],
            'order_number' => $orderNumber,
            'status' => $initialStatus,
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'material_source' => $validated['material_source'] ?? null,
            'material_status' => $validated['material_status'] ?? null,
        ]);

        // Hitung estimasi harga dan tanggal selesai
        $estimation = $this->orderBusinessRules->calculateEstimation($order);

        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        // Simpan order_status_log pertama
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $initialStatus,
            'changed_by' => auth()->id(),
            'notes' => 'Pesanan baru dibuat.',
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat dengan nomor ' . $orderNumber . '.');
    }

    // ──────────────────────────────────────────────────────────
    // 4. show — Detail pesanan dengan tracking status
    // ──────────────────────────────────────────────────────────

    /**
     * Menampilkan detail pesanan beserta status logs untuk tracking.
     *
     * Memastikan customer hanya bisa melihat pesanan miliknya sendiri.
     */
    public function show(Order $order): View
    {
        // Pastikan customer hanya bisa melihat pesanan miliknya
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load(['service', 'statusLogs.user', 'designFiles', 'payments']);

        return view('orders.show', compact('order'));
    }

    // ──────────────────────────────────────────────────────────
    // 5. uploadDesign — Upload file desain
    // ──────────────────────────────────────────────────────────

    /**
     * Handle upload file desain untuk pesanan tertentu.
     *
     * File disimpan ke storage/app/private/designs/ dengan nama unik.
     * Validasi: format JPG/PNG, max 5MB.
     */
    public function uploadDesign(UploadDesignRequest $request, Order $order): RedirectResponse
    {
        // Pastikan customer hanya bisa upload ke pesanan miliknya
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $file = $request->file('design_file');

        // Simpan file ke storage/app/private/designs/
        $filePath = $file->store('designs', 'private');

        // Simpan record design file
        DesignFile::create([
            'order_id' => $order->id,
            'file_path' => $filePath,
            'original_filename' => $file->getClientOriginalName(),
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'File desain berhasil diunggah.');
    }
}

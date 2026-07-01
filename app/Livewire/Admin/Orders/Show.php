<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Fabric;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;
use App\Services\OrderBusinessRulesService;
use App\Services\OrderStatusTransitionService;
use App\Services\OrderRejectionService;
use App\Services\OrderPricingService;
use App\Services\OrderMaterialService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * AdminOrdersShow Component - Halaman detail pesanan untuk admin
 * 
 * Komponen ini menampilkan informasi lengkap pesanan dan menyediakan
 * berbagai action yang dapat dilakukan admin:
 * - Terima/tolak pesanan baru
 * - Atur DP dan harga
 * - Manage status bahan (ready/po)
 * - Track produksi
 * - Lihat riwayat status
 * 
 * Catatan: Komponen ini mengelola 5 berbagai concern (rejection, DP, material, price, production).
 * Untuk peningkatan maintainability, pertimbangkan memisahkan menjadi sub-components.
 */
class Show extends Component
{
    public Order $order;

    // ── Rejection ────────────────────────────────────────────
    public string $rejectionReason = '';
    public bool $showRejectForm = false;

    // ── DP Amount ────────────────────────────────────────────
    public string $dpAmount = '';
    public bool $showDpForm = false;

    // ── Material Management ──────────────────────────────────
    public string $material_source = '';
    public string $material_status = '';
    public ?int $fabric_id = null;
    public string $poDays = '';
    public bool $showMaterialForm = false;

    // ── Price editing ────────────────────────────────────────
    public string $editEstimatedPrice = '';
    public bool $showPriceForm = false;

    // ── Production ───────────────────────────────────────────
    public string $productionDays = '';
    public bool $showProductionForm = false;

    // ── Status notes ─────────────────────────────────────────
    public ?string $notes = null;

    // ── Design Preview ───────────────────────────────────────
    public bool $showDesignModal = false;
    public ?string $designPreviewUrl = null;
    public ?string $designPreviewName = null;

    public function mount(Order $order): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->order = $order;
        $this->refreshOrder();
    }

    // ──────────────────────────────────────────────────────────
    // Action: Terima Pesanan
    // ──────────────────────────────────────────────────────────

    public function acceptOrder(): void
    {
        if ($this->order->status !== 'menunggu_konfirmasi') {
            session()->flash('error', 'Pesanan ini tidak sedang menunggu konfirmasi.');
            return;
        }

        $service = app(OrderStatusTransitionService::class);
        $result = $service->acceptOrder($this->order, auth()->id());

        if ($result['success']) {
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Action: Tolak Pesanan
    // ──────────────────────────────────────────────────────────

    public function openRejectForm(): void
    {
        $this->showRejectForm = true;
    }

    public function closeRejectForm(): void
    {
        $this->showRejectForm = false;
        $this->rejectionReason = '';
        $this->resetValidation('rejectionReason');
    }

    public function rejectOrder(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'rejectionReason.required' => 'Alasan penolakan harus diisi.',
            'rejectionReason.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $service = app(OrderRejectionService::class);
        $result = $service->rejectOrder($this->order, $this->rejectionReason, auth()->id());

        if ($result['success']) {
            $this->closeRejectForm();
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Action: Set DP Amount
    // ──────────────────────────────────────────────────────────

    public function openDpForm(): void
    {
        $this->dpAmount = (string) (int) ($this->order->dp_amount ?? 0);
        $this->showDpForm = true;
    }

    public function closeDpForm(): void
    {
        $this->showDpForm = false;
        $this->dpAmount = '';
        $this->resetValidation('dpAmount');
    }

    public function setDpAmount(): void
    {
        $this->validate([
            'dpAmount' => ['required', 'numeric', 'min:1000'],
        ], [
            'dpAmount.required' => 'Nominal DP harus diisi.',
            'dpAmount.numeric' => 'Nominal DP harus berupa angka.',
            'dpAmount.min' => 'Nominal DP minimal Rp 1.000.',
        ]);

        $service = app(OrderPricingService::class);
        $result = $service->setDpAmount($this->order, (float) $this->dpAmount, auth()->id());

        if ($result['success']) {
            $this->closeDpForm();
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Action: Material Management
    // ──────────────────────────────────────────────────────────

    public function openMaterialForm(): void
    {
        $this->material_source = $this->order->material_source ?? '';
        $this->material_status = $this->order->material_status ?? '';
        $this->fabric_id = $this->order->fabric_id;
        $this->poDays = (string) ($this->order->po_days ?? '');
        $this->showMaterialForm = true;
    }

    public function closeMaterialForm(): void
    {
        $this->showMaterialForm = false;
        $this->resetValidation();
    }

    public function updatedMaterialSource($value): void
    {
        if ($value === 'jasa' && $this->fabric_id) {
            $this->updatedFabricId($this->fabric_id);
        } else {
            $this->material_status = '';
        }
    }

    public function updatedFabricId($value): void
    {
        if ($this->material_source === 'jasa' && $value) {
            $fabric = Fabric::find($value);
            if ($fabric) {
                if ($fabric->stock_status === 'tersedia') {
                    $this->material_status = 'ready';
                } elseif ($fabric->stock_status === 'po') {
                    $this->material_status = 'po';
                    $this->poDays = (string) $fabric->po_days;
                }
            }
        }
    }

    public function updateMaterial(): void
    {
        $rules = [
            'material_source' => ['required', 'in:customer,jasa'],
            'material_status' => ['required', 'in:ready,po'],
        ];

        if ($this->material_source === 'jasa') {
            $rules['fabric_id'] = ['required', 'exists:fabrics,id'];
        }

        if ($this->material_status === 'po') {
            $rules['poDays'] = ['required', 'integer', 'min:3', 'max:30'];
        }

        $this->validate($rules, [
            'material_source.required' => 'Sumber bahan harus dipilih.',
            'material_status.required' => 'Status bahan harus dipilih.',
            'fabric_id.required' => 'Pilih bahan kain.',
            'poDays.required' => 'Durasi PO harus diisi.',
            'poDays.min' => 'PO minimal 3 hari.',
            'poDays.max' => 'PO maksimal 30 hari.',
        ]);

        $service = app(OrderMaterialService::class);
        $result = $service->setMaterialSource(
            $this->order,
            $this->material_source,
            $this->material_source === 'jasa' ? $this->fabric_id : null,
            $this->material_status === 'po' ? (int) $this->poDays : null,
            auth()->id()
        );

        if ($result['success']) {
            $this->closeMaterialForm();
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message'] ?? 'Gagal update material.');
        }
    }

    public function markMaterialReady(): void
    {
        $service = app(OrderMaterialService::class);
        $result = $service->markMaterialReady($this->order, auth()->id());

        if ($result['success']) {
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function forceMoveToQueue(): void
    {
        if ($this->order->status !== 'menunggu_bahan') {
            session()->flash('error', 'Pesanan tidak sedang menunggu bahan.');
            return;
        }

        $service = app(OrderStatusTransitionService::class);
        $result = $service->moveToQueue($this->order, auth()->id());

        if ($result['success']) {
            $this->refreshOrder();
            session()->flash('success', 'Pesanan berhasil dimasukkan ke antrian produksi.');
        } else {
            session()->flash('error', implode(', ', $result['errors'] ?? [$result['message']]));
        }
    }

    // ──────────────────────────────────────────────────────────
    // Action: Price Editing
    // ──────────────────────────────────────────────────────────

    public function openPriceForm(): void
    {
        $this->editEstimatedPrice = (string) (int) $this->order->estimated_price;
        $this->showPriceForm = true;
    }

    public function closePriceForm(): void
    {
        $this->showPriceForm = false;
        $this->editEstimatedPrice = '';
        $this->resetValidation('editEstimatedPrice');
    }

    public function updatePrice(): void
    {
        $this->validate([
            'editEstimatedPrice' => ['required', 'numeric', 'min:1000'],
        ], [
            'editEstimatedPrice.required' => 'Harga harus diisi.',
            'editEstimatedPrice.numeric' => 'Harga harus berupa angka.',
            'editEstimatedPrice.min' => 'Harga minimal Rp 1.000.',
        ]);

        $service = app(OrderPricingService::class);
        $result = $service->setEstimatedPrice($this->order, (float) $this->editEstimatedPrice, auth()->id());

        if ($result['success']) {
            $this->closePriceForm();
            $this->refreshOrder();
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Action: Start Production (dalam_antrian → dijahit)
    // ──────────────────────────────────────────────────────────

    public function openProductionForm(): void
    {
        $this->productionDays = (string) ($this->order->production_days ?? $this->order->service->base_duration_days ?? 7);
        $this->showProductionForm = true;
    }

    public function closeProductionForm(): void
    {
        $this->showProductionForm = false;
        $this->productionDays = '';
        $this->resetValidation('productionDays');
    }

    public function startProduction(): void
    {
        $this->validate([
            'productionDays' => ['required', 'integer', 'min:1', 'max:90'],
        ], [
            'productionDays.required' => 'Estimasi hari pengerjaan harus diisi.',
            'productionDays.min' => 'Minimal 1 hari.',
            'productionDays.max' => 'Maksimal 90 hari.',
        ]);

        if ($this->order->status !== 'dalam_antrian') {
            session()->flash('error', 'Pesanan tidak dalam antrian.');
            return;
        }

        $this->order->update([
            'status' => 'dijahit',
            'production_days' => (int) $this->productionDays,
            'production_started_at' => now(),
            'estimated_finish_date' => now()->addDays((int) $this->productionDays),
        ]);

        // Deduct fabric stock
        app(OrderBusinessRulesService::class)->deductFabricStock($this->order);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'dijahit',
            'changed_by' => auth()->id(),
            'notes' => 'Produksi dimulai. Estimasi selesai: ' . $this->productionDays . ' hari.',
        ]);

        if ($this->order->customer) {
            $this->order->customer->notify(new OrderStatusUpdated(
                $this->order,
                'Pesanan #' . $this->order->order_number . ' mulai dijahit. Estimasi selesai dalam ' . $this->productionDays . ' hari.'
            ));
        }

        $this->closeProductionForm();
        $this->refreshOrder();
        session()->flash('success', 'Produksi dimulai.');
    }

    // ──────────────────────────────────────────────────────────
    // Action: Finish Production (dijahit → selesai_produksi)
    // ──────────────────────────────────────────────────────────

    public function finishProduction(): void
    {
        if ($this->order->status !== 'dijahit') {
            session()->flash('error', 'Pesanan tidak sedang dalam proses jahit.');
            return;
        }

        $this->order->update([
            'status' => 'selesai_produksi',
            'production_finished_at' => now(),
        ]);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'selesai_produksi',
            'changed_by' => auth()->id(),
            'notes' => 'Produksi selesai. Menunggu pelunasan pembayaran.',
        ]);

        if ($this->order->customer) {
            $totalPaid = $this->order->payments->where('status', 'terverifikasi')->sum('amount');
            $remaining = max(0, (float) $this->order->estimated_price - $totalPaid);
            $msg = 'Pesanan #' . $this->order->order_number . ' selesai diproduksi.';
            if ($remaining > 0) {
                $msg .= ' Silakan lakukan pelunasan sebesar Rp ' . number_format($remaining, 0, ',', '.') . ' untuk pengambilan.';
            }
            $this->order->customer->notify(new OrderStatusUpdated($this->order, $msg));
        }

        $this->refreshOrder();
        session()->flash('success', 'Produksi ditandai selesai. Notifikasi pelunasan dikirim ke customer.');
    }

    // ──────────────────────────────────────────────────────────
    // Action: Mark Ready for Pickup (selesai_produksi → siap_diambil)
    // ──────────────────────────────────────────────────────────

    public function markReadyForPickup(): void
    {
        if ($this->order->status !== 'selesai_produksi') {
            session()->flash('error', 'Pesanan belum selesai produksi.');
            return;
        }

        $check = app(OrderBusinessRulesService::class)->canMarkReadyForPickup($this->order->fresh('payments'));

        if (!$check['can_proceed']) {
            session()->flash('error', implode(' ', $check['blocking_reasons']));
            return;
        }

        $this->order->update(['status' => 'siap_diambil']);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'siap_diambil',
            'changed_by' => auth()->id(),
            'notes' => 'Pembayaran lunas. Pesanan siap diambil.',
        ]);

        if ($this->order->customer) {
            $this->order->customer->notify(new OrderStatusUpdated(
                $this->order,
                'Pesanan #' . $this->order->order_number . ' siap untuk diambil! Silakan datang ke workshop kami.'
            ));
        }

        $this->refreshOrder();
        session()->flash('success', 'Pesanan ditandai siap diambil.');
    }

    // ──────────────────────────────────────────────────────────
    // Action: Mark Complete (siap_diambil → selesai)
    // ──────────────────────────────────────────────────────────

    public function markComplete(): void
    {
        if ($this->order->status !== 'siap_diambil') {
            session()->flash('error', 'Pesanan belum siap diambil.');
            return;
        }

        $this->order->update(['status' => 'selesai']);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'selesai',
            'changed_by' => auth()->id(),
            'notes' => 'Pesanan telah diambil oleh customer.',
        ]);

        if ($this->order->customer) {
            $this->order->customer->notify(new OrderStatusUpdated(
                $this->order,
                'Pesanan #' . $this->order->order_number . ' telah selesai. Terima kasih telah menggunakan jasa Jahitly!'
            ));
        }

        $this->refreshOrder();
        session()->flash('success', 'Pesanan ditandai selesai.');
    }

    // ──────────────────────────────────────────────────────────
    // Design Preview
    // ──────────────────────────────────────────────────────────

    public function previewDesign(int $designId): void
    {
        $designFile = \App\Models\DesignFile::where('order_id', $this->order->id)->findOrFail($designId);
        $filePath = storage_path('app/private/' . $designFile->file_path);

        if (!file_exists($filePath)) {
            session()->flash('error', 'File desain tidak ditemukan.');
            return;
        }

        $mime = mime_content_type($filePath) ?: 'image/jpeg';
        $contents = file_get_contents($filePath);

        if ($contents === false) {
            session()->flash('error', 'File desain tidak dapat dibuka.');
            return;
        }

        $this->designPreviewUrl = 'data:' . $mime . ';base64,' . base64_encode($contents);
        $this->designPreviewName = $designFile->original_filename;
        $this->showDesignModal = true;
    }

    public function closeDesignPreview(): void
    {
        $this->showDesignModal = false;
        $this->designPreviewUrl = null;
        $this->designPreviewName = null;
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    // ──────────────────────────────────────────────────────────
    // Action: Penerimaan Pakaian (Vermak)
    // ──────────────────────────────────────────────────────────

    public function markClothesReceived(): void
    {
        if ($this->order->status !== 'pakaian_dikirim') {
            return;
        }

        $this->order->update(['status' => 'dalam_antrian']);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => 'dalam_antrian',
            'changed_by' => auth()->id(),
            'notes' => 'Pakaian telah diterima oleh admin dan dimasukkan ke antrian produksi.',
        ]);

        $this->refreshOrder();
        session()->flash('success', 'Pakaian berhasil dikonfirmasi dan pesanan masuk antrian produksi.');
    }

    private function refreshOrder(): void
    {
        $this->order = $this->order->fresh([
            'customer',
            'service',
            'fabric',
            'statusLogs.user',
            'designFiles',
            'payments',
            'appointment',
        ]);
    }

    public function render(): View
    {
        $fabrics = Fabric::query()->orderBy('name')->orderBy('color')->get();

        return view('livewire.admin.orders.show', [
            'statusLogs' => $this->order->statusLogs->sortByDesc('created_at'),
            'fabrics' => $fabrics,
        ])->layout('layouts.admin');
    }
}

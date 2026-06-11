<?php

namespace App\Livewire\Admin\Orders;

use App\Models\DesignFile;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Notifications\OrderStatusUpdated;
use App\Services\OrderBusinessRulesService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public Order $order;
    public string $material_source = '';
    public string $material_status = '';
    public string $status = '';
    public ?string $notes = null;
    /**
     * @var list<string>
     */
    public array $blockingReasons = [];

    public bool $showDesignModal = false;
    public ?string $designPreviewUrl = null;
    public ?string $designPreviewName = null;

    // ── Price editing ────────────────────────────────────────
    public string $editEstimatedPrice = '';
    public bool $showPriceForm = false;

    public function mount(Order $order): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->order = $order;
        $this->refreshOrder();
        $this->syncBlockingReasons();
    }

    /**
     * @return array<string, mixed>
     */
    protected function materialRules(): array
    {
        return [
            'material_source' => ['required', 'in:customer,jasa'],
            'material_status' => ['required', 'in:ready,po'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function statusRules(): array
    {
        return [
            'status' => [
                'required',
                'in:menunggu_appointment,menunggu_bahan,diproses,dijahit,finishing,selesai',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'material_source.required' => 'Sumber bahan harus diisi.',
            'material_source.in' => 'Sumber bahan harus "customer" atau "jasa".',
            'material_status.required' => 'Status bahan harus diisi.',
            'material_status.in' => 'Status bahan harus "ready" atau "po".',
            'status.required' => 'Status pesanan harus diisi.',
            'status.in' => 'Status pesanan tidak valid.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    public function updatedStatus(): void
    {
        $this->syncBlockingReasons();
    }

    public function updateMaterial(): void
    {
        $validated = $this->validate($this->materialRules());

        $this->order->update([
            'material_source' => $validated['material_source'],
            'material_status' => $validated['material_status'],
        ]);

        $estimation = app(OrderBusinessRulesService::class)->calculateEstimation($this->order);

        $this->order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        $this->refreshOrder();
        $this->syncBlockingReasons();

        session()->flash('success', 'Data bahan pesanan berhasil diperbarui.');
    }

    public function updateStatus(): void
    {
        $validated = $this->validate($this->statusRules());

        $this->syncBlockingReasons();

        if (! empty($this->blockingReasons)) {
            return;
        }

        $oldStatus = $this->order->status;
        $newStatus = $validated['status'];

        $this->order->update([
            'status' => $newStatus,
        ]);

        OrderStatusLog::create([
            'order_id' => $this->order->id,
            'status' => $newStatus,
            'changed_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Deduct fabric stock when order starts processing
        if ($oldStatus !== 'diproses' && $newStatus === 'diproses') {
            app(OrderBusinessRulesService::class)->deductFabricStock($this->order);
        }

        $this->notes = null;
        $this->refreshOrder();
        $this->syncBlockingReasons();

        $customer = $this->order->customer;
        if ($customer) {
            $message = 'Pesanan #' . $this->order->order_number
                . ' status diperbarui menjadi ' . $this->order->status . '.';
            $customer->notify(new OrderStatusUpdated($this->order, $message));
        }

        session()->flash('success', 'Status pesanan berhasil diperbarui.');
    }

    // ── Price Editing ─────────────────────────────────────────

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

        $oldPrice = (float) $this->order->estimated_price;
        $newPrice = (float) $this->editEstimatedPrice;

        $this->order->update([
            'estimated_price' => $newPrice,
        ]);

        $this->closePriceForm();
        $this->refreshOrder();

        // Notifikasi ke customer tentang update harga
        $customer = $this->order->customer;
        if ($customer) {
            $message = 'Harga pesanan #' . $this->order->order_number
                . ' telah diperbarui dari Rp ' . number_format($oldPrice, 0, ',', '.')
                . ' menjadi Rp ' . number_format($newPrice, 0, ',', '.')
                . '. Silakan lakukan pembayaran.';
            $customer->notify(new OrderStatusUpdated($this->order, $message));
        }

        session()->flash('success', 'Harga pesanan berhasil diperbarui dan notifikasi telah dikirim ke customer.');
    }

    // ── Design Preview ────────────────────────────────────────

    public function previewDesign(int $designId): void
    {
        $designFile = DesignFile::where('order_id', $this->order->id)->findOrFail($designId);
        $filePath = storage_path('app/private/' . $designFile->file_path);

        if (! file_exists($filePath)) {
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

    public function getCanUpdateStatusProperty(): bool
    {
        return empty($this->blockingReasons);
    }

    /**
     * Cek apakah admin boleh mengedit harga.
     * Hanya bisa edit setelah appointment selesai (untuk custom/seragam).
     */
    public function getCanEditPriceProperty(): bool
    {
        $serviceType = $this->order->service->type ?? '';

        // Vermak: selalu bisa edit harga
        if ($serviceType === 'vermak') {
            return true;
        }

        // Custom & Seragam: hanya bisa edit setelah appointment selesai
        return $this->order->appointment
            && $this->order->appointment->status === 'selesai';
    }

    /**
     * @return list<string>
     */
    private function getBlockingReasonsForStatus(string $status): array
    {
        if ($status === 'diproses') {
            $order = $this->order->fresh(['service', 'appointment', 'payments']);
            $check = app(OrderBusinessRulesService::class)->canMoveToProcessing($order);
            return $check['blocking_reasons'];
        }

        if ($status === 'selesai') {
            $order = $this->order->fresh(['payments']);
            $check = app(OrderBusinessRulesService::class)->canMarkAsComplete($order);
            return $check['blocking_reasons'];
        }

        return [];
    }

    private function syncBlockingReasons(): void
    {
        $this->blockingReasons = $this->getBlockingReasonsForStatus($this->status);
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

        $this->material_source = $this->order->material_source ?? '';
        $this->material_status = $this->order->material_status ?? '';
        $this->status = $this->order->status;
    }

    public function render(): View
    {
        return view('livewire.admin.orders.show', [
            'statusLogs' => $this->order->statusLogs->sortByDesc('created_at'),
        ])->layout('layouts.admin');
    }
}

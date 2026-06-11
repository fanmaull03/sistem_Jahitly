<?php

namespace App\Livewire\Customer\Orders;

use App\Models\DesignFile;
use App\Models\Fabric;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Service;
use App\Services\OrderBusinessRulesService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $services;
    public ?int $service_id = null;
    public ?int $quantity = 1;
    public ?string $material_source = null;
    public ?string $material_status = null;
    public ?int $fabric_id = null;
    public ?string $notes = null;
    public $design_file;
    public float $estimated_price = 0.0;

    /** @var Collection<int, Fabric> */
    public $fabrics;

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->services = Service::query()->orderBy('name')->get();
        $this->fabrics = collect();
        $this->syncEstimatedPrice();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'material_source' => ['nullable', 'in:customer,jasa'],
            'material_status' => ['nullable', 'in:ready,po'],
            'design_file' => $this->requiresDesignFile()
                ? ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];

        // Jika sumber bahan dari penjahit, fabric_id wajib dipilih
        if ($this->material_source === 'jasa') {
            $rules['fabric_id'] = ['required', 'exists:fabrics,id'];
        } else {
            $rules['fabric_id'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'service_id.required' => 'Layanan harus dipilih.',
            'service_id.exists' => 'Layanan yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
            'notes.max' => 'Catatan maksimal 2000 karakter.',
            'material_source.in' => 'Sumber bahan harus "customer" atau "jasa".',
            'material_status.in' => 'Status bahan harus "ready" atau "po".',
            'fabric_id.required' => 'Pilih bahan yang diinginkan dari daftar.',
            'fabric_id.exists' => 'Bahan yang dipilih tidak valid.',
            'design_file.required' => 'File desain harus diunggah untuk layanan custom.',
            'design_file.file' => 'File desain harus berupa file yang valid.',
            'design_file.mimes' => 'File desain harus berformat JPG atau PNG.',
            'design_file.max' => 'Ukuran file desain maksimal 5MB.',
        ];
    }

    public function updatedServiceId(): void
    {
        if (! $this->requiresDesignFile()) {
            $this->design_file = null;
            $this->resetValidation('design_file');
        }

        // Reset material source saat ganti layanan
        if ($this->selectedServiceType() === 'vermak') {
            $this->material_source = null;
            $this->fabric_id = null;
            $this->material_status = null;
            $this->fabrics = collect();
        }

        $this->syncEstimatedPrice();
    }

    public function updatedQuantity(): void
    {
        $this->syncEstimatedPrice();
    }

    public function updatedMaterialSource(): void
    {
        $this->fabric_id = null;
        $this->material_status = null;

        if ($this->material_source === 'jasa') {
            // Load semua bahan yang tersedia
            $this->fabrics = Fabric::query()->orderBy('name')->orderBy('color')->get();
        } elseif ($this->material_source === 'customer') {
            // Jika bawa sendiri, otomatis ready (tidak ada pilihan PO)
            $this->material_status = 'ready';
            $this->fabrics = collect();
        } else {
            $this->fabrics = collect();
        }

        $this->syncEstimatedPrice();
    }

    public function updatedFabricId(): void
    {
        if ($this->fabric_id && $this->material_source === 'jasa') {
            $fabric = $this->fabrics->firstWhere('id', $this->fabric_id);
            if ($fabric) {
                // Auto-set material_status berdasarkan stock_status bahan
                $this->material_status = $fabric->stock_status === 'tersedia' ? 'ready' : 'po';
            }
        } else {
            $this->material_status = null;
        }

        $this->syncEstimatedPrice();
    }

    public function updatedDesignFile(): void
    {
        $this->validateOnly('design_file');
    }

    public function submit()
    {
        $validated = $this->validate();

        $service = Service::findOrFail($validated['service_id']);

        $orderRules = app(OrderBusinessRulesService::class);
        $orderNumber = $orderRules->generateOrderNumber();

        $initialStatus = in_array($service->type, ['seragam', 'custom'], true)
            ? 'menunggu_appointment'
            : 'menunggu_bahan';

        $order = Order::create([
            'customer_id' => auth()->id(),
            'service_id' => $validated['service_id'],
            'fabric_id' => ($this->material_source === 'jasa') ? $validated['fabric_id'] : null,
            'order_number' => $orderNumber,
            'status' => $initialStatus,
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'material_source' => $validated['material_source'] ?? null,
            'material_status' => $validated['material_status'] ?? null,
        ]);

        $estimation = $orderRules->calculateEstimation($order);

        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $initialStatus,
            'changed_by' => auth()->id(),
            'notes' => 'Pesanan baru dibuat.',
        ]);

        if ($this->design_file) {
            $filePath = $this->design_file->store('designs', 'local');

            DesignFile::create([
                'order_id' => $order->id,
                'file_path' => $filePath,
                'original_filename' => $this->design_file->getClientOriginalName(),
            ]);
        }

        session()->flash('success', 'Pesanan berhasil dibuat dengan nomor ' . $orderNumber . '.');

        return $this->redirectRoute('orders.show', $order, navigate: true);
    }

    private function requiresDesignFile(): bool
    {
        $service = $this->services?->firstWhere('id', $this->service_id);

        return $service && $service->type === 'custom';
    }

    private function selectedServiceType(): ?string
    {
        $service = $this->services?->firstWhere('id', $this->service_id);
        return $service?->type;
    }

    private function syncEstimatedPrice(): void
    {
        $service = $this->services?->firstWhere('id', $this->service_id);

        if (! $service) {
            $this->estimated_price = 0.0;
            return;
        }

        $quantity = max(1, (int) $this->quantity);
        $baseTotal = (float) $service->base_price * $quantity;

        // Tambahkan harga bahan jika memilih bahan dari penjahit
        $fabricCost = 0.0;
        if ($this->material_source === 'jasa' && $this->fabric_id && $this->fabrics->isNotEmpty()) {
            $fabric = $this->fabrics->firstWhere('id', $this->fabric_id);
            if ($fabric) {
                $fabricCost = (float) $fabric->price_per_meter * $quantity;
            }
        }

        $this->estimated_price = $baseTotal + $fabricCost;
    }

    public function render(): View
    {
        $selectedFabric = null;
        if ($this->fabric_id && $this->fabrics->isNotEmpty()) {
            $selectedFabric = $this->fabrics->firstWhere('id', $this->fabric_id);
        }

        return view('livewire.customer.orders.create', [
            'services' => $this->services,
            'requiresDesignFile' => $this->requiresDesignFile(),
            'selectedService' => $this->services?->firstWhere('id', $this->service_id),
            'selectedFabric' => $selectedFabric,
        ])->layout('layouts.app');
    }
}

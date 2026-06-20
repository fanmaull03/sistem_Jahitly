<?php

namespace App\Livewire\Customer\Orders;

use App\Models\DesignFile;
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

    public Collection $services;
    public ?int $service_id = null;
    public ?int $quantity = 1;
    public ?string $notes = null;
    public $design_file;

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isCustomer()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $this->services = Service::query()->orderBy('name')->get();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'design_file' => $this->requiresDesignFile()
                ? ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120']
                : ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
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
            'design_file.required' => 'File desain harus diunggah untuk layanan custom.',
            'design_file.file' => 'File desain harus berupa file yang valid.',
            'design_file.mimes' => 'File desain harus berformat JPG atau PNG.',
            'design_file.max' => 'Ukuran file desain maksimal 5MB.',
        ];
    }

    public function updatedServiceId(): void
    {
        if (!$this->requiresDesignFile()) {
            $this->design_file = null;
            $this->resetValidation('design_file');
        }
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

        // Semua pesanan baru masuk ke menunggu_konfirmasi
        $order = Order::create([
            'customer_id' => auth()->id(),
            'service_id' => $validated['service_id'],
            'order_number' => $orderNumber,
            'status' => 'menunggu_konfirmasi',
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Estimasi awal berdasarkan layanan
        $estimation = $orderRules->calculateEstimation($order);
        $order->update([
            'estimated_price' => $estimation['estimated_price'],
            'estimated_finish_date' => $estimation['estimated_finish_date'],
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => 'menunggu_konfirmasi',
            'changed_by' => auth()->id(),
            'notes' => 'Pesanan baru dibuat. Menunggu konfirmasi admin.',
        ]);

        if ($this->design_file) {
            $filePath = $this->design_file->store('designs', 'public');

            DesignFile::create([
                'order_id' => $order->id,
                'file_path' => $filePath,
                'original_filename' => $this->design_file->getClientOriginalName(),
            ]);
        }

        session()->flash('success', 'Pesanan berhasil dibuat dengan nomor ' . $orderNumber . '. Menunggu konfirmasi admin.');

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

    public function render(): View
    {
        return view('livewire.customer.orders.create', [
            'services' => $this->services,
            'requiresDesignFile' => $this->requiresDesignFile(),
            'selectedService' => $this->services?->firstWhere('id', $this->service_id),
        ])->layout('layouts.app');
    }
}

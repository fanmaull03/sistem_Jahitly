<?php

namespace App\Livewire\Admin\Fabrics;

use App\Models\Fabric;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ── Filters ──────────────────────────────────────
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $search = '';

    // ── Modal state ──────────────────────────────────
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingFabricId = null;
    public ?int $deletingFabricId = null;

    // ── Form fields ──────────────────────────────────
    public string $name = '';
    public string $category = '';
    public string $color = '';
    public string $price_per_meter = '';
    public string $stock_status = 'tersedia';
    public ?int $po_days = null;
    public string $description = '';
    public string $stock_meters = '0';
    public $image; // Untuk file upload gambar bahan

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:katun,polyester,linen,sutra,denim,sifon,wol,lainnya'],
            'color' => ['required', 'string', 'max:100'],
            'price_per_meter' => ['required', 'numeric', 'min:0'],
            'stock_meters' => ['required', 'numeric', 'min:0'],
            'stock_status' => ['required', 'in:tersedia,po'],
            'po_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2048'], // Maks 2MB
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama bahan harus diisi.',
            'category.required' => 'Kategori harus dipilih.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'color.required' => 'Warna harus diisi.',
            'price_per_meter.required' => 'Harga per meter harus diisi.',
            'price_per_meter.numeric' => 'Harga harus berupa angka.',
            'price_per_meter.min' => 'Harga tidak boleh negatif.',
            'stock_meters.required' => 'Stok meter harus diisi.',
            'stock_meters.numeric' => 'Stok meter harus berupa angka.',
            'stock_meters.min' => 'Stok meter tidak boleh negatif.',
            'stock_status.required' => 'Status stok harus dipilih.',
            'stock_status.in' => 'Status stok tidak valid.',
            'po_days.integer' => 'Hari PO harus berupa angka.',
            'po_days.min' => 'Hari PO minimal 1.',
            'po_days.max' => 'Hari PO maksimal 90.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
        ];
    }

    // ── Filter updaters ──────────────────────────────

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ── CRUD actions ─────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetFormFields();
        $this->editingFabricId = null;
        $this->showFormModal = true;
    }

    public function openEditModal(int $fabricId): void
    {
        $fabric = Fabric::findOrFail($fabricId);

        $this->editingFabricId = $fabric->id;
        $this->name = $fabric->name;
        $this->category = $fabric->category;
        $this->color = $fabric->color;
        $this->price_per_meter = (string) $fabric->price_per_meter;
        $this->stock_meters = (string) $fabric->stock_meters;
        $this->stock_status = $fabric->stock_status;
        $this->po_days = $fabric->po_days;
        $this->description = $fabric->description ?? '';
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetFormFields();
        $this->resetValidation();
    }

    public function saveFabric(): void
    {
        $validated = $this->validate();

        // Jika status tersedia, hapus po_days
        if ($validated['stock_status'] === 'tersedia') {
            $validated['po_days'] = null;
        }

        // Handle image upload
        if ($this->image) {
            $validated['image_path'] = $this->image->store('fabrics', 'public');
            // Hapus image dari array agar tidak error jika tidak ada di fillable (jika belum diupdate fillable-nya, tapi sudah kita update)
        } else {
            // Remove image from validated array so it doesn't overwrite existing with null unintentionally
            unset($validated['image']);
        }

        if ($this->editingFabricId) {
            $fabric = Fabric::findOrFail($this->editingFabricId);
            
            // Delete old image if a new one is uploaded
            if ($this->image && $fabric->image_path) {
                Storage::disk('public')->delete($fabric->image_path);
            }

            $fabric->update($validated);
            session()->flash('success', 'Bahan "' . $fabric->name . ' - ' . $fabric->color . '" berhasil diperbarui.');
        } else {
            $fabric = Fabric::create($validated);
            session()->flash('success', 'Bahan "' . $fabric->name . ' - ' . $fabric->color . '" berhasil ditambahkan.');
        }

        $this->closeFormModal();
    }

    public function confirmDelete(int $fabricId): void
    {
        $this->deletingFabricId = $fabricId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingFabricId = null;
    }

    public function deleteFabric(): void
    {
        if (! $this->deletingFabricId) {
            return;
        }

        $fabric = Fabric::findOrFail($this->deletingFabricId);

        // Cek apakah ada pesanan yang terkait
        if ($fabric->orders()->exists()) {
            session()->flash('error', 'Bahan tidak bisa dihapus karena masih digunakan oleh pesanan.');
            $this->cancelDelete();
            return;
        }

        $fabricName = $fabric->name . ' - ' . $fabric->color;
        
        // Hapus gambar jika ada
        if ($fabric->image_path) {
            Storage::disk('public')->delete($fabric->image_path);
        }

        $fabric->delete();

        session()->flash('success', 'Bahan "' . $fabricName . '" berhasil dihapus.');
        $this->cancelDelete();
    }

    /**
     * Konfirmasi bahan PO sudah siap → ubah status ke 'tersedia'.
     */
    public function confirmReady(int $fabricId): void
    {
        $fabric = Fabric::findOrFail($fabricId);

        if ($fabric->stock_status !== 'po') {
            session()->flash('error', 'Bahan ini sudah berstatus tersedia.');
            return;
        }

        $fabric->update([
            'stock_status' => 'tersedia',
            'po_days' => null,
        ]);

        session()->flash('success', 'Bahan "' . $fabric->name . ' - ' . $fabric->color . '" dikonfirmasi tersedia. Pesanan terkait bisa dilanjutkan.');
    }

    // ── Helpers ──────────────────────────────────────

    private function resetFormFields(): void
    {
        $this->name = '';
        $this->category = '';
        $this->color = '';
        $this->price_per_meter = '';
        $this->stock_meters = '0';
        $this->stock_status = 'tersedia';
        $this->po_days = null;
        $this->description = '';
        $this->image = null;
        $this->editingFabricId = null;
    }

    // ── Render ───────────────────────────────────────

    public function render(): View
    {
        $query = Fabric::query()->orderBy('name');

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterStatus) {
            $query->where('stock_status', $this->filterStatus);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('color', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.fabrics.index', [
            'fabrics' => $query->paginate(12),
            'categories' => [
                'katun' => 'Katun',
                'polyester' => 'Polyester',
                'linen' => 'Linen',
                'sutra' => 'Sutra',
                'denim' => 'Denim',
                'sifon' => 'Sifon',
                'wol' => 'Wol',
                'lainnya' => 'Lainnya',
            ],
        ])->layout('layouts.admin');
    }
}

<div class="page-enter space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Kelola Bahan</h1>
            <p class="text-sm text-slate-500">Kelola stok bahan kain yang tersedia di workshop.</p>
        </div>
        <button
            type="button"
            wire:click="openCreateModal"
            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Bahan
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari nama atau warna bahan..."
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none sm:max-w-xs"
        />
        <select
            wire:model.live="filterCategory"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
        >
            <option value="">Semua Kategori</option>
            @foreach ($categories as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <select
            wire:model.live="filterStatus"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none"
        >
            <option value="">Semua Status</option>
            <option value="tersedia">Tersedia</option>
            <option value="po">Pre-Order (PO)</option>
        </select>
    </div>

    {{-- Fabric Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($fabrics as $fabric)
            <div class="hover-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        @if ($fabric->image_path)
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-slate-200">
                                <img src="{{ Storage::url($fabric->image_path) }}" class="h-full w-full object-cover" alt="{{ $fabric->name }}">
                            </div>
                        @else
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="text-base font-semibold text-slate-900">{{ $fabric->name }}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 text-sm text-slate-500">
                                    <span class="h-3 w-3 rounded-full border border-slate-300" style="background-color: {{ $fabric->color === 'Putih' ? '#fff' : ($fabric->color === 'Hitam' ? '#1a1a1a' : ($fabric->color === 'Navy' ? '#1e3a5f' : ($fabric->color === 'Biru Muda' ? '#87CEEB' : ($fabric->color === 'Khaki' ? '#C3B091' : ($fabric->color === 'Cream' ? '#FFFDD0' : ($fabric->color === 'Gold' ? '#FFD700' : ($fabric->color === 'Biru Tua' ? '#00008B' : ($fabric->color === 'Dusty Pink' ? '#DCAE96' : ($fabric->color === 'Abu-abu' ? '#808080' : ($fabric->color === 'Olive' ? '#808000' : '#ccc')))))))))) }};"></span>
                                    {{ $fabric->color }}
                                </span>
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $fabric->category_label }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        @if ($fabric->stock_status === 'tersedia')
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Tersedia</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">PO ~{{ $fabric->po_days }} hari</span>
                        @endif
                    </div>
                </div>

                @if ($fabric->description)
                    <p class="mt-3 text-sm text-slate-500 line-clamp-2">{{ $fabric->description }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                    <div>
                        <div class="text-lg font-bold text-slate-900">
                            Rp {{ number_format((float) $fabric->price_per_meter, 0, ',', '.') }}
                            <span class="text-xs font-normal text-slate-500">/meter</span>
                        </div>
                        <div class="mt-1 text-xs font-semibold {{ (float)$fabric->stock_meters > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            Stok: {{ number_format((float) $fabric->stock_meters, 1) }} meter
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($fabric->stock_status === 'po')
                            <button
                                type="button"
                                wire:click="confirmReady({{ $fabric->id }})"
                                wire:confirm="Konfirmasi bahan '{{ $fabric->name }} - {{ $fabric->color }}' sudah tersedia?"
                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-500"
                                title="Konfirmasi bahan sudah siap"
                            >
                                Siap
                            </button>
                        @endif
                        <button
                            type="button"
                            wire:click="openEditModal({{ $fabric->id }})"
                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            wire:click="confirmDelete({{ $fabric->id }})"
                            class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-slate-200 bg-white px-4 py-10 text-center text-sm text-slate-500">
                Belum ada data bahan. Klik "Tambah Bahan" untuk memulai.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="border-t border-slate-200 pt-4">
        {{ $fabrics->links() }}
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showFormModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="modal-content-enter w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $editingFabricId ? 'Edit Bahan' : 'Tambah Bahan Baru' }}
                    </h3>
                    <button
                        type="button"
                        wire:click="closeFormModal"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-sm font-semibold text-slate-600 hover:border-slate-300"
                    >
                        Tutup
                    </button>
                </div>

                <form wire:submit.prevent="saveFabric" class="mt-5 space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nama Bahan</label>
                            <input
                                type="text"
                                wire:model="name"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                placeholder="Contoh: Katun Combed 30s"
                            />
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Warna</label>
                            <input
                                type="text"
                                wire:model="color"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                placeholder="Contoh: Navy, Putih"
                            />
                            @error('color')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Kategori</label>
                            <select
                                wire:model="category"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                            >
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Harga per Meter (Rp)</label>
                            <input
                                type="number"
                                step="1000"
                                min="0"
                                wire:model="price_per_meter"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                placeholder="45000"
                            />
                            @error('price_per_meter')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Stok (Meter)</label>
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                wire:model="stock_meters"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                placeholder="50"
                            />
                            @error('stock_meters')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Status Stok</label>
                            <select
                                wire:model.live="stock_status"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                            >
                                <option value="tersedia">Tersedia</option>
                                <option value="po">Pre-Order (PO)</option>
                            </select>
                            @error('stock_status')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($stock_status === 'po')
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Estimasi Hari PO</label>
                                <input
                                    type="number"
                                    min="1"
                                    max="90"
                                    wire:model="po_days"
                                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                    placeholder="7"
                                />
                                @error('po_days')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Gambar Bahan (Opsional)</label>
                            <input
                                type="file"
                                wire:model="image"
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200"
                            />
                            <div wire:loading wire:target="image" class="mt-1 text-xs text-blue-600">Mengunggah...</div>
                            @error('image')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                            
                            {{-- Image Preview --}}
                            <div class="mt-3">
                                @if ($image)
                                    <div class="relative h-24 w-24 overflow-hidden rounded-xl border border-slate-200">
                                        <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                                    </div>
                                @elseif ($editingFabricId)
                                    @php
                                        $currentFabric = \App\Models\Fabric::find($editingFabricId);
                                    @endphp
                                    @if ($currentFabric && $currentFabric->image_path)
                                        <div class="relative h-24 w-24 overflow-hidden rounded-xl border border-slate-200">
                                            <img src="{{ Storage::url($currentFabric->image_path) }}" class="h-full w-full object-cover" alt="Current Image">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Deskripsi (Opsional)</label>
                            <textarea
                                rows="3"
                                wire:model="description"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-slate-400 focus:outline-none"
                                placeholder="Deskripsi singkat bahan..."
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            wire:click="closeFormModal"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="saveFabric">
                                {{ $editingFabricId ? 'Simpan Perubahan' : 'Tambah Bahan' }}
                            </span>
                            <span wire:loading wire:target="saveFabric">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal && $deletingFabricId)
        @php
            $deletingFabric = \App\Models\Fabric::find($deletingFabricId);
        @endphp
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="modal-content-enter w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900">Konfirmasi Hapus</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Apakah Anda yakin ingin menghapus bahan
                    <strong>{{ $deletingFabric?->name }} - {{ $deletingFabric?->color }}</strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        wire:click="cancelDelete"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="deleteFabric"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

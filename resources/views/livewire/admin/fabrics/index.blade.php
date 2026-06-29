<div class="page-enter space-y-6">
    <x-slot name="header">Kelola Bahan</x-slot>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Inventaris</p>
            <h1 class="mt-0.5 text-2xl font-bold text-ink dark:text-stone-100">Kelola Bahan</h1>
            <p class="text-sm text-muted dark:text-stone-400">Kelola stok bahan kain yang tersedia di workshop.</p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Bahan
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative w-full sm:max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau warna bahan..."
                   class="w-full rounded-xl border border-border bg-white pl-9 pr-4 py-2.5 text-sm text-ink shadow-sm 
                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100" />
        </div>
        <select wire:model.live="filterCategory"
                class="rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus"
                class="rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
            <option value="">Semua Status</option>
            <option value="tersedia">Tersedia</option>
            <option value="po">Pre-Order (PO)</option>
        </select>
    </div>

    {{-- Fabric Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($fabrics as $fabric)
            <div class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-border bg-white shadow-sm transition hover:shadow-md hover:-translate-y-0.5 dark:border-stone-700 dark:bg-stone-800">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            @if ($fabric->image_path)
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-border">
                                    <img src="{{ Storage::url($fabric->image_path) }}" class="h-full w-full object-cover" alt="{{ $fabric->name }}">
                                </div>
                            @else
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-dashed border-border bg-surface text-muted">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="text-base font-bold text-ink dark:text-stone-100">{{ $fabric->name }}</div>
                                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-muted">
                                        <span class="h-3 w-3 rounded-full border border-border shadow-sm" style="background-color: {{ $fabric->color === 'Putih' ? '#fff' : ($fabric->color === 'Hitam' ? '#1a1a1a' : ($fabric->color === 'Navy' ? '#1e3a5f' : ($fabric->color === 'Biru Muda' ? '#87CEEB' : ($fabric->color === 'Khaki' ? '#C3B091' : ($fabric->color === 'Cream' ? '#FFFDD0' : ($fabric->color === 'Gold' ? '#FFD700' : ($fabric->color === 'Biru Tua' ? '#00008B' : ($fabric->color === 'Dusty Pink' ? '#DCAE96' : ($fabric->color === 'Abu-abu' ? '#808080' : ($fabric->color === 'Olive' ? '#808000' : '#ccc')))))))))) }};"></span>
                                        {{ $fabric->color }}
                                    </span>
                                    <span class="rounded bg-surface px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-muted border border-border dark:bg-stone-700 dark:text-stone-300">{{ $fabric->category_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 ml-2">
                            @if ($fabric->stock_status === 'tersedia')
                                <span class="rounded bg-emerald-500/10 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Tersedia</span>
                            @else
                                <span class="rounded bg-accent/10 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-accent dark:text-accent">PO ~{{ $fabric->po_days }} hr</span>
                            @endif
                        </div>
                    </div>

                    @if ($fabric->description)
                        <p class="mt-4 text-sm text-muted dark:text-stone-400 line-clamp-2">{{ $fabric->description }}</p>
                    @endif
                </div>

                <div class="mt-auto border-t border-border bg-surface/50 p-4 dark:border-stone-700 dark:bg-stone-800/50">
                    <div class="flex items-end justify-between">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-widest text-muted mb-0.5">Harga / Meter</div>
                            <div class="text-lg font-extrabold text-ink dark:text-stone-100">
                                Rp {{ number_format((float) $fabric->price_per_meter, 0, ',', '.') }}
                            </div>
                            <div class="mt-1 text-xs font-bold {{ (float)$fabric->stock_meters > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                Stok: {{ number_format((float) $fabric->stock_meters, 1) }} m
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if ($fabric->stock_status === 'po')
                                <button type="button" wire:click="confirmReady({{ $fabric->id }})" wire:confirm="Konfirmasi bahan '{{ $fabric->name }} - {{ $fabric->color }}' sudah tersedia?"
                                        class="rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-600 shadow-sm" title="Konfirmasi bahan sudah siap">
                                    Siap
                                </button>
                            @endif
                            <button type="button" wire:click="openEditModal({{ $fabric->id }})"
                                    class="rounded-lg border border-border bg-white px-3 py-1.5 text-xs font-bold text-ink transition hover:border-primary hover:text-primary dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300">
                                Edit
                            </button>
                            <button type="button" wire:click="confirmDelete({{ $fabric->id }})"
                                    class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-400">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-border bg-white p-12 text-center dark:border-stone-700 dark:bg-stone-800">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-surface mb-4">
                    <svg class="h-8 w-8 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-ink dark:text-stone-300">Belum ada data bahan.</p>
                <p class="mt-1 text-xs text-muted dark:text-stone-400">Klik "Tambah Bahan" untuk mulai mengelola inventaris kain.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="border-t border-border pt-4 dark:border-stone-700">
        {{ $fabrics->links() }}
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showFormModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-ink/60 px-4 backdrop-blur-sm">
            <div class="modal-content-enter w-full max-w-2xl rounded-3xl bg-white shadow-2xl dark:bg-stone-800 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-border p-6 dark:border-stone-700 shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-ink dark:text-stone-100">
                            {{ $editingFabricId ? 'Edit Bahan' : 'Tambah Bahan Baru' }}
                        </h3>
                        <p class="text-sm text-muted mt-0.5">Isi informasi detail bahan dengan lengkap.</p>
                    </div>
                    <button type="button" wire:click="closeFormModal"
                            class="rounded-full p-2 text-muted transition hover:bg-surface hover:text-ink dark:hover:bg-stone-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto p-6">
                    <form wire:submit.prevent="saveFabric" id="fabricForm" class="space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Nama Bahan</label>
                                <input type="text" wire:model="name"
                                       class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                       placeholder="Contoh: Katun Combed 30s" />
                                @error('name') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Warna</label>
                                <input type="text" wire:model="color"
                                       class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                       placeholder="Contoh: Navy, Putih" />
                                @error('color') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Kategori</label>
                                <select wire:model="category"
                                        class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Harga per Meter (Rp)</label>
                                <input type="number" step="1000" min="0" wire:model="price_per_meter"
                                       class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                       placeholder="45000" />
                                @error('price_per_meter') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Stok (Meter)</label>
                                <input type="number" step="0.1" min="0" wire:model="stock_meters"
                                       class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                       placeholder="50" />
                                @error('stock_meters') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Status Stok</label>
                                <select wire:model.live="stock_status"
                                        class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                    <option value="tersedia">Tersedia</option>
                                    <option value="po">Pre-Order (PO)</option>
                                </select>
                                @error('stock_status') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            @if ($stock_status === 'po')
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Estimasi Hari PO</label>
                                    <input type="number" min="1" max="90" wire:model="po_days"
                                           class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm 
                                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                           placeholder="7" />
                                    @error('po_days') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Gambar Bahan (Opsional)</label>
                                <div class="mt-1 flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-border border-dashed rounded-xl cursor-pointer bg-surface hover:bg-surface/80 dark:hover:bg-stone-800 dark:bg-stone-700 dark:border-stone-600 transition">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-3 text-muted" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-1 text-sm text-muted dark:text-stone-400"><span class="font-semibold text-primary">Klik untuk upload</span></p>
                                            <p class="text-xs text-muted/70 dark:text-stone-500">PNG, JPG up to 2MB</p>
                                        </div>
                                        <input type="file" wire:model="image" accept="image/*" class="hidden" />
                                    </label>
                                </div>
                                <div wire:loading wire:target="image" class="mt-2 text-xs font-bold text-primary animate-pulse">Mengunggah file...</div>
                                @error('image') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                                
                                {{-- Image Preview --}}
                                <div class="mt-4">
                                    @if ($image)
                                        <div class="relative h-24 w-24 overflow-hidden rounded-xl border border-border shadow-sm">
                                            <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                                        </div>
                                    @elseif ($editingFabricId)
                                        @php
                                            $currentFabric = \App\Models\Fabric::find($editingFabricId);
                                        @endphp
                                        @if ($currentFabric && $currentFabric->image_path)
                                            <div class="relative h-24 w-24 overflow-hidden rounded-xl border border-border shadow-sm">
                                                <img src="{{ Storage::url($currentFabric->image_path) }}" class="h-full w-full object-cover" alt="Current Image">
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Deskripsi (Opsional)</label>
                                <textarea rows="5" wire:model="description"
                                          class="block w-full rounded-xl border border-border bg-white px-4 py-3 text-sm text-ink shadow-sm 
                                                 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                          placeholder="Deskripsi singkat bahan..."></textarea>
                                @error('description') <p class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="flex items-center justify-end gap-3 border-t border-border bg-surface p-6 dark:border-stone-700 dark:bg-stone-800 shrink-0">
                    <button type="button" wire:click="closeFormModal"
                            class="rounded-xl border border-border bg-white px-5 py-2.5 text-sm font-bold text-ink transition hover:border-primary hover:text-primary dark:bg-stone-800 dark:border-stone-600 dark:text-stone-300">
                        Batal
                    </button>
                    <button type="submit" form="fabricForm"
                            class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveFabric">
                            {{ $editingFabricId ? 'Simpan Perubahan' : 'Tambah Bahan' }}
                        </span>
                        <span wire:loading wire:target="saveFabric" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal && $deletingFabricId)
        @php
            $deletingFabric = \App\Models\Fabric::find($deletingFabricId);
        @endphp
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-ink/60 px-4 backdrop-blur-sm">
            <div class="modal-content-enter w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl dark:bg-stone-800 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-500/10 mb-5">
                    <svg class="h-8 w-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-ink dark:text-stone-100">Hapus Bahan</h3>
                <p class="mt-2 text-sm text-muted">
                    Apakah Anda yakin ingin menghapus bahan <br>
                    <strong class="text-ink dark:text-stone-200">{{ $deletingFabric?->name }} - {{ $deletingFabric?->color }}</strong>? <br>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="mt-8 flex flex-col-reverse sm:flex-row items-center justify-center gap-3">
                    <button type="button" wire:click="cancelDelete"
                            class="w-full sm:w-auto rounded-xl border border-border bg-white px-5 py-2.5 text-sm font-bold text-ink transition hover:bg-surface dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300">
                        Batal
                    </button>
                    <button type="button" wire:click="deleteFabric"
                            class="w-full sm:w-auto rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 shadow-sm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-32 sm:px-6 lg:pb-10">
    <div data-reveal class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">Buat Pesanan Baru</h1>
            <p class="mt-1 text-sm text-stone-600">Pilih layanan, isi detail pesanan, dan lihat estimasi harga secara langsung.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 transition hover:text-stone-900" wire:navigate>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-8 lg:grid-cols-3">
        <!-- Form Area (Matching Mockup) -->
        <div class="space-y-0 lg:col-span-2 rounded-xl border border-stone-200 bg-white p-6 sm:p-8 shadow-sm">
            
            <!-- STEP 1: Pilih Layanan -->
            <section>
                <div class="mb-2">
                    <h2 class="text-lg font-bold text-stone-900">1. Pilih Layanan</h2>
                    <p class="text-sm text-stone-500 mt-1">Pilih jenis layanan jahit yang Anda butuhkan.</p>
                </div>
                <hr class="my-5 border-stone-200">
                
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($services as $service)
                        <button
                            type="button"
                            wire:click="$set('service_id', {{ $service->id }})"
                            @class([
                                'relative flex flex-col items-center rounded-xl p-5 text-center transition focus:outline-none',
                                'border-2 border-[#003399] bg-blue-50/10' => $service_id === $service->id,
                                'border border-stone-200 bg-white hover:border-stone-300' => $service_id !== $service->id,
                            ])
                        >
                            @if ($service_id === $service->id)
                                <div class="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#003399] text-white">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div @class([
                                'mb-3 flex h-12 w-12 items-center justify-center rounded-full',
                                'bg-[#003399] text-white' => $service_id === $service->id,
                                'bg-stone-200 text-stone-500' => $service_id !== $service->id,
                            ])>
                                @if (stripos($service->name, 'baru') !== false || stripos($service->type, 'baru') !== false)
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                    </svg>
                                @elseif (stripos($service->name, 'vermak') !== false || stripos($service->type, 'vermak') !== false)
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 14.25l-2.25 2.25m-4.5 0L5.25 14.25M12 12l2.25-2.25m0-4.5l-4.5 4.5M12 12V3" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                @endif
                            </div>
                            
                            <div class="text-sm font-bold text-stone-900">{{ $service->name }}</div>
                            <p class="mt-1 text-xs text-stone-500">{{ $service->description }}</p>
                        </button>
                    @endforeach
                </div>
                @error('service_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            <!-- STEP 2: Detail Pesanan -->
            <section class="mt-8">
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-stone-800 mb-1.5">Jumlah Item (Pcs)</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.live="quantity"
                            class="w-full rounded-lg border border-stone-300 px-4 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 transition"
                            placeholder="Masukkan jumlah item"
                        />
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    @if (! $selectedService || $selectedService->type !== 'vermak')
                    <div>
                        <label class="block text-xs font-bold text-stone-800 mb-1.5">Sumber Bahan</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label @class([
                                'flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition hover:bg-stone-50',
                                'border-[#003399] bg-blue-50 text-[#003399]' => $material_source === 'customer',
                                'border-stone-300 bg-white text-stone-700' => $material_source !== 'customer',
                            ])>
                                <div @class([
                                    'flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border',
                                    'border-[#003399]' => $material_source === 'customer',
                                    'border-stone-400' => $material_source !== 'customer',
                                ])>
                                    @if($material_source === 'customer')
                                        <div class="h-2.5 w-2.5 rounded-full bg-[#003399]"></div>
                                    @endif
                                </div>
                                <input type="radio" name="material_source" wire:model.live="material_source" value="customer" class="sr-only">
                                <span class="text-sm">Bawa Sendiri</span>
                            </label>

                            <label @class([
                                'flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition hover:bg-stone-50',
                                'border-[#003399] bg-blue-50 text-[#003399]' => $material_source === 'jasa',
                                'border-stone-300 bg-white text-stone-700' => $material_source !== 'jasa',
                            ])>
                                <div @class([
                                    'flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border',
                                    'border-[#003399]' => $material_source === 'jasa',
                                    'border-stone-400' => $material_source !== 'jasa',
                                ])>
                                    @if($material_source === 'jasa')
                                        <div class="h-2.5 w-2.5 rounded-full bg-[#003399]"></div>
                                    @endif
                                </div>
                                <input type="radio" name="material_source" wire:model.live="material_source" value="jasa" class="sr-only">
                                <span class="text-sm">Beli di Penjahit</span>
                            </label>
                        </div>
                        @error('material_source')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    {{-- Pilihan bahan dari customer (status bahan manual) --}}
                    @if ($material_source === 'customer')
                        <div>
                            <label class="block text-xs font-bold text-stone-800 mb-1.5">Status Bahan (Jika dari Anda)</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label @class([
                                    'flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition hover:bg-stone-50',
                                    'border-[#003399] bg-blue-50 text-[#003399]' => $material_status === 'ready',
                                    'border-stone-300 bg-white text-stone-700' => $material_status !== 'ready',
                                ])>
                                    <div @class([
                                        'flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border',
                                        'border-[#003399]' => $material_status === 'ready',
                                        'border-stone-400' => $material_status !== 'ready',
                                    ])>
                                        @if($material_status === 'ready')
                                            <div class="h-2.5 w-2.5 rounded-full bg-[#003399]"></div>
                                        @endif
                                    </div>
                                    <input type="radio" name="material_status" wire:model="material_status" value="ready" class="sr-only">
                                    <span class="text-sm">Sudah Ready</span>
                                </label>

                                <label @class([
                                    'flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition hover:bg-stone-50',
                                    'border-[#003399] bg-blue-50 text-[#003399]' => $material_status === 'po',
                                    'border-stone-300 bg-white text-stone-700' => $material_status !== 'po',
                                ])>
                                    <div @class([
                                        'flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border',
                                        'border-[#003399]' => $material_status === 'po',
                                        'border-stone-400' => $material_status !== 'po',
                                    ])>
                                        @if($material_status === 'po')
                                            <div class="h-2.5 w-2.5 rounded-full bg-[#003399]"></div>
                                        @endif
                                    </div>
                                    <input type="radio" name="material_status" wire:model="material_status" value="po" class="sr-only">
                                    <span class="text-sm">Menyusul / PO</span>
                                </label>
                            </div>
                            @error('material_status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Pilihan bahan dari penjahit --}}
                    @if ($material_source === 'jasa' && $fabrics->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-stone-800 mb-1.5">Pilih Bahan</label>
                            <p class="text-xs text-stone-500 mb-3">Pilih bahan kain yang tersedia di workshop kami.</p>
                            
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($fabrics as $fabric)
                                    <button
                                        type="button"
                                        wire:click="$set('fabric_id', {{ $fabric->id }})"
                                        @class([
                                            'relative flex flex-col rounded-lg border p-4 text-left transition focus:outline-none',
                                            'border-2 border-[#003399] bg-blue-50/30' => $fabric_id === $fabric->id,
                                            'border-stone-200 bg-white hover:border-stone-300' => $fabric_id !== $fabric->id,
                                        ])
                                    >
                                        @if ($fabric_id === $fabric->id)
                                            <div class="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#003399] text-white">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-stone-900">{{ $fabric->name }}</span>
                                            <span class="rounded-md bg-stone-100 px-1.5 py-0.5 text-[10px] font-medium text-stone-600">{{ $fabric->category_label }}</span>
                                        </div>

                                        <div class="mt-1.5 flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 text-xs text-stone-500">
                                                <span class="h-2.5 w-2.5 rounded-full border border-stone-300" style="background-color: {{ $fabric->color === 'Putih' ? '#fff' : ($fabric->color === 'Hitam' ? '#1a1a1a' : ($fabric->color === 'Navy' ? '#1e3a5f' : ($fabric->color === 'Biru Muda' ? '#87CEEB' : ($fabric->color === 'Khaki' ? '#C3B091' : ($fabric->color === 'Cream' ? '#FFFDD0' : ($fabric->color === 'Gold' ? '#FFD700' : ($fabric->color === 'Biru Tua' ? '#00008B' : ($fabric->color === 'Dusty Pink' ? '#DCAE96' : ($fabric->color === 'Abu-abu' ? '#808080' : ($fabric->color === 'Olive' ? '#808000' : '#ccc')))))))))) }};"></span>
                                                {{ $fabric->color }}
                                            </span>
                                            <span class="text-xs text-stone-400">·</span>
                                            <span class="text-xs font-medium text-stone-700">Rp {{ number_format((float) $fabric->price_per_meter, 0, ',', '.') }}/m</span>
                                        </div>

                                        <div class="mt-2">
                                            @if ($fabric->stock_status === 'tersedia')
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">
                                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Stok Ready
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                    <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    PO ~{{ $fabric->po_days }} hari
                                                </span>
                                            @endif
                                        </div>

                                        @if ($fabric->description)
                                            <p class="mt-2 text-[11px] text-stone-400 line-clamp-2">{{ $fabric->description }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>

                            @error('fabric_id')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            {{-- Info bahan terpilih --}}
                            @if ($selectedFabric)
                                <div class="mt-3 rounded-lg border p-3 {{ $selectedFabric->stock_status === 'po' ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }}">
                                    <div class="flex items-center gap-2">
                                        @if ($selectedFabric->stock_status === 'po')
                                            <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                            <span class="text-xs font-semibold text-amber-800">
                                                Bahan ini perlu Pre-Order (PO) ~{{ $selectedFabric->po_days }} hari. Admin akan mengkonfirmasi saat bahan sudah siap.
                                            </span>
                                        @else
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-xs font-semibold text-emerald-800">
                                                Bahan tersedia dan siap digunakan langsung.
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-xs font-bold text-stone-800 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <textarea
                            rows="4"
                            wire:model="notes"
                            class="w-full rounded-lg border border-stone-300 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 transition"
                            placeholder="Contoh: Tolong buatkan kantong di sebelah kiri, atau model kerah shanghai..."
                        ></textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- STEP 3: Upload Desain (Optional/Custom) -->
            @if ($requiresDesignFile)
                <section class="mt-8">
                    <div class="mb-2">
                        <h2 class="text-lg font-bold text-stone-900">Upload Desain Custom</h2>
                    </div>
                    <hr class="my-5 border-stone-200">
                    
                    <div class="rounded-xl border border-stone-200 bg-white p-6">
                        <div class="flex items-center gap-6">
                            @if ($design_file)
                                <div class="flex-shrink-0">
                                    <img src="{{ $design_file->temporaryUrl() }}" class="h-24 w-24 rounded-lg object-cover shadow-sm border border-stone-200" alt="Preview">
                                </div>
                            @else
                                <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 border border-stone-200">
                                    <svg class="h-8 w-8 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-grow">
                                <label for="design_file" class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-stone-700 border border-stone-300 shadow-sm transition hover:bg-stone-50">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    {{ $design_file ? 'Ganti File' : 'Pilih File Desain' }}
                                </label>
                                <input
                                    id="design_file"
                                    type="file"
                                    class="sr-only"
                                    accept="image/png,image/jpeg"
                                    wire:model="design_file"
                                />
                                @if ($design_file)
                                    <p class="mt-2 text-sm font-medium text-stone-900 truncate max-w-[200px] sm:max-w-xs">{{ $design_file->getClientOriginalName() }}</p>
                                @else
                                    <p class="mt-2 text-xs text-stone-500">Format yang didukung: JPG, PNG (Max 5MB)</p>
                                @endif
                                
                                @error('design_file')
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                                <div wire:loading wire:target="design_file" class="mt-1 text-xs font-semibold text-blue-600">
                                    Sedang mengunggah...
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Form Actions -->
            <hr class="my-8 border-stone-200">
            <div class="flex items-center justify-end gap-6 pb-2">
                <a href="{{ route('orders.index') }}" class="text-sm font-bold text-[#003399] hover:text-blue-800 transition" wire:navigate>Batal</a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#003399] px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Lanjut ke Tahap Pembayaran</span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                    <svg wire:loading.remove wire:target="submit" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Sticky Footer / Side Panel (Price Summary) -->
        <aside class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white p-4 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] lg:sticky lg:top-0 lg:z-auto lg:rounded-xl lg:border lg:p-6 lg:shadow-sm h-fit">
            <h3 class="hidden text-xl font-bold text-stone-900 lg:block">Ringkasan Pesanan</h3>
            
            @php
                $basePrice = $selectedService ? (float) $selectedService->base_price : 0;
            @endphp
            
            <div class="hidden lg:block lg:mt-6 lg:space-y-3 lg:text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Harga Dasar</span>
                    <span class="font-medium text-stone-900">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Jumlah</span>
                    <span class="font-medium text-stone-900">{{ $quantity }} pcs</span>
                </div>
                @if ($selectedFabric)
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Bahan</span>
                        <span class="font-medium text-stone-900">{{ $selectedFabric->name }} ({{ $selectedFabric->color }})</span>
                    </div>
                    @if ($selectedFabric->stock_status === 'po')
                        <div class="flex items-center justify-between">
                            <span class="text-stone-500">Status Bahan</span>
                            <span class="font-medium text-amber-600">PO ~{{ $selectedFabric->po_days }} hari</span>
                        </div>
                    @endif
                @endif
                <div class="my-4 h-px bg-stone-200"></div>
            </div>

            <div class="flex items-center justify-between lg:flex-col lg:items-stretch lg:justify-start lg:gap-2">
                <div>
                    <p class="text-xs font-semibold text-stone-500 lg:text-sm">Total Estimasi Harga</p>
                    <p class="text-xl font-black text-[#003399] lg:mt-1 lg:text-2xl">
                        Rp {{ number_format($estimated_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            
            <p class="hidden mt-4 text-center text-xs text-stone-400 lg:block">
                Estimasi harga ini bisa berubah setelah proses konsultasi/fitting.
            </p>
        </aside>
    </form>
</div>

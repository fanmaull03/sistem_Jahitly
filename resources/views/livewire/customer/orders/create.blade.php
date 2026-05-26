<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-32 sm:px-6 lg:pb-10">
    <div data-reveal class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Buat Pesanan Baru</h1>
            <p class="mt-1 text-sm text-stone-600">Pilih layanan, isi detail pesanan, dan lihat estimasi harga secara langsung.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-stone-500 hover:text-stone-900" wire:navigate>
            &larr; Kembali
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            
            <!-- STEP 1: Pilih Layanan -->
            <section data-reveal data-reveal-delay="1">
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-stone-900 text-xs font-bold text-white">1</span>
                    <h2 class="text-lg font-bold text-stone-900">Pilih Layanan</h2>
                </div>
                
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($services as $service)
                        <button
                            type="button"
                            wire:click="$set('service_id', {{ $service->id }})"
                            @class([
                                'relative flex flex-col rounded-2xl border-2 p-5 text-left transition focus:outline-none',
                                'border-blue-600 bg-blue-50/50 shadow-sm' => $service_id === $service->id,
                                'border-stone-200 bg-white hover:border-stone-300 hover:bg-stone-50' => $service_id !== $service->id,
                            ])
                        >
                            @if ($service_id === $service->id)
                                <div class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white shadow">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-between text-xs text-stone-500">
                                <span class="uppercase tracking-wider font-bold">{{ strtoupper($service->type) }}</span>
                            </div>
                            <div class="mt-2 text-base font-bold text-stone-900">{{ $service->name }}</div>
                            <p class="mt-2 flex-grow line-clamp-3 text-sm text-stone-600">{{ $service->description }}</p>
                            <div class="mt-4 pt-4 border-t border-stone-200/60 flex items-center justify-between">
                                <span class="text-xs font-medium text-stone-500">Est. {{ $service->base_duration_days }} hari</span>
                                <span class="text-sm font-bold text-stone-900">Rp {{ number_format($service->base_price, 0, ',', '.') }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
                @error('service_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            <!-- STEP 2: Detail Pesanan -->
            <section data-reveal data-reveal-delay="2">
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-stone-900 text-xs font-bold text-white">2</span>
                    <h2 class="text-lg font-bold text-stone-900">Detail Pesanan</h2>
                </div>
                
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-stone-700">Jumlah Item (Pcs)</label>
                            <input
                                type="number"
                                min="1"
                                wire:model.live="quantity"
                                class="mt-2 w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            />
                            @error('quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label class="text-sm font-semibold text-stone-700">Sumber Bahan</label>
                            <div class="mt-2 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 hover:bg-stone-50">
                                    <span class="font-medium">Bahan dari Customer</span>
                                    <input type="radio" wire:model.live="material_source" value="customer" class="sr-only">
                                </label>
                                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 hover:bg-stone-50">
                                    <span class="font-medium">Beli dari Jasa</span>
                                    <input type="radio" wire:model.live="material_source" value="jasa" class="sr-only">
                                </label>
                            </div>
                            @error('material_source')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($material_source === 'customer')
                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-stone-700">Status Bahan (Jika dari Anda)</label>
                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 hover:bg-stone-50">
                                        <input type="radio" wire:model="material_status" value="ready" class="sr-only">
                                        <span class="font-medium">Sudah Ready</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-stone-300 bg-white px-4 py-3 text-sm transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-700 hover:bg-stone-50">
                                        <input type="radio" wire:model="material_status" value="po" class="sr-only">
                                        <span class="font-medium">Menyusul / PO</span>
                                    </label>
                                </div>
                                @error('material_status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <label class="text-sm font-semibold text-stone-700">Catatan Khusus</label>
                        <textarea
                            rows="3"
                            wire:model="notes"
                            class="mt-2 w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                            placeholder="Contoh: Pinggang dikecilkan 2cm, model kerah V-neck..."
                        ></textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- STEP 3: Upload Desain (Optional/Custom) -->
            @if ($requiresDesignFile)
                <section data-reveal data-reveal-delay="3">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-stone-900 text-xs font-bold text-white">3</span>
                        <h2 class="text-lg font-bold text-stone-900">Upload Desain Custom</h2>
                    </div>
                    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                        
                        <div x-data="{ isDropping: false }" 
                             @dragover.prevent="isDropping = true" 
                             @dragleave.prevent="isDropping = false" 
                             @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-stone-300 bg-stone-50'"
                             class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-12 text-center transition hover:border-blue-400 hover:bg-stone-100">
                            
                            <input
                                x-ref="fileInput"
                                id="design_file"
                                type="file"
                                class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
                                accept="image/png,image/jpeg"
                                wire:model="design_file"
                            />
                            
                            @if ($design_file)
                                <div class="flex flex-col items-center">
                                    <img src="{{ $design_file->temporaryUrl() }}" class="h-32 w-auto rounded-lg object-cover shadow-sm" alt="Preview">
                                    <p class="mt-3 text-sm font-semibold text-stone-700">{{ $design_file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-stone-500">Klik atau drag untuk mengganti gambar</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center text-stone-500">
                                    <svg class="mb-3 h-10 w-10 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                    <span class="text-sm font-bold text-stone-700">Drag & drop gambar referensi desain</span>
                                    <span class="mt-1 text-xs">Atau klik untuk memilih file (JPG/PNG, max 5MB)</span>
                                </div>
                            @endif
                        </div>
                        
                        @error('design_file')
                            <p class="mt-2 text-xs font-medium text-red-600 text-center">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="design_file" class="mt-2 text-center text-xs font-semibold text-blue-600">
                            Sedang memproses gambar...
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <!-- Sticky Footer / Side Panel -->
        <aside class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white p-4 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] lg:sticky lg:top-6 lg:z-auto lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm">
            <h3 class="hidden text-lg font-bold text-stone-900 lg:block">Ringkasan Pesanan</h3>
            
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
                <div class="my-4 h-px bg-stone-200"></div>
            </div>

            <div class="flex items-center justify-between lg:flex-col lg:items-stretch lg:justify-start lg:gap-6">
                <div>
                    <p class="text-xs font-medium text-stone-500 lg:text-sm">Total Estimasi Harga</p>
                    <p class="text-xl font-black text-blue-600 lg:mt-1 lg:text-2xl">
                        Rp {{ number_format($estimated_price, 0, ',', '.') }}
                    </p>
                </div>
                
                <button
                    type="submit"
                    class="rounded-xl bg-stone-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-stone-800 focus:ring-2 focus:ring-stone-900 focus:ring-offset-2 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 lg:w-full"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Buat Pesanan</span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                </button>
            </div>
            
            <p class="hidden mt-4 text-center text-xs text-stone-400 lg:block">
                Estimasi harga ini bisa berubah setelah proses konsultasi/fitting.
            </p>
        </aside>
    </form>
</div>

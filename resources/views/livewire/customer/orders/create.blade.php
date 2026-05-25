<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Buat Pesanan Baru</h1>
            <p class="text-sm text-slate-600">Pilih layanan, isi detail pesanan, dan lihat estimasi harga secara langsung.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900" wire:navigate>
            Kembali ke daftar
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Pilih Layanan</h2>
                    <span class="text-xs font-semibold text-slate-400">Wajib</span>
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($services as $service)
                        <button
                            type="button"
                            wire:click="$set('service_id', {{ $service->id }})"
                            @class([
                                'rounded-xl border p-4 text-left transition',
                                'border-blue-500 bg-blue-50 shadow-sm' => $service_id === $service->id,
                                'border-slate-200 bg-white hover:border-slate-300' => $service_id !== $service->id,
                            ])
                        >
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="uppercase tracking-wide">{{ strtoupper($service->type) }}</span>
                                <span>{{ $service->base_duration_days }} hari</span>
                            </div>
                            <div class="mt-2 text-base font-semibold text-slate-900">{{ $service->name }}</div>
                            <p class="mt-2 line-clamp-3 text-sm text-slate-600">{{ $service->description }}</p>
                            <div class="mt-4 text-sm font-semibold text-slate-900">Rp {{ number_format($service->base_price, 0, ',', '.') }}</div>
                        </button>
                    @endforeach
                </div>
                @error('service_id')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Detail Pesanan</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Jumlah Item</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.live="quantity"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-200"
                        />
                        @error('quantity')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Sumber Bahan</label>
                        <select
                            wire:model="material_source"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-200"
                        >
                            <option value="">Pilih sumber</option>
                            <option value="customer">Bahan dari customer</option>
                            <option value="jasa">Bahan dari jasa</option>
                        </select>
                        @error('material_source')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Status Bahan</label>
                        <select
                            wire:model="material_status"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-200"
                        >
                            <option value="">Pilih status</option>
                            <option value="ready">Ready</option>
                            <option value="po">PO</option>
                        </select>
                        @error('material_status')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-semibold text-slate-700">Catatan</label>
                    <textarea
                        rows="4"
                        wire:model="notes"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-200"
                        placeholder="Tambahkan catatan ukuran, model, atau preferensi khusus."
                    ></textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            @if ($requiresDesignFile)
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Upload Desain</h2>
                    <label
                        for="design_file"
                        class="mt-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center transition hover:border-blue-300"
                    >
                        <input
                            id="design_file"
                            type="file"
                            class="sr-only"
                            accept="image/png,image/jpeg"
                            wire:model="design_file"
                        />
                        <span class="text-sm font-semibold text-slate-700">Drag and drop file desain</span>
                        <span class="mt-1 text-xs text-slate-500">JPG/PNG, maksimal 5MB.</span>
                    </label>
                    @if ($design_file)
                        <p class="mt-3 text-sm text-slate-700">File terpilih: {{ $design_file->getClientOriginalName() }}</p>
                    @endif
                    @error('design_file')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                    <div wire:loading wire:target="design_file" class="mt-2 text-sm text-slate-500">
                        Mengunggah file desain...
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-4 lg:sticky lg:top-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Estimasi Harga</h3>
                @php
                    $basePrice = $selectedService ? (float) $selectedService->base_price : 0;
                @endphp
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Harga dasar</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($basePrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Jumlah item</span>
                        <span class="font-semibold text-slate-900">{{ $quantity }}</span>
                    </div>
                    <div class="h-px bg-slate-200"></div>
                    <div class="flex items-center justify-between text-base">
                        <span class="font-semibold text-slate-900">Total estimasi</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($estimated_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="mt-4 text-xs text-slate-500">Estimasi dihitung dari harga dasar layanan dan jumlah item.</p>
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                wire:loading.attr="disabled"
            >
                Kirim Pesanan
            </button>
        </aside>
    </form>
</div>

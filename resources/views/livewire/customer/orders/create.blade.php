<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-32 sm:px-6 lg:pb-10">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-ink dark:text-stone-100">Buat Pesanan Baru</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">Pilih layanan dan isi detail pesanan Anda. Admin akan mengkonfirmasi pesanan ini.</p>
        </div>
        <a href="{{ route('orders.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-xs font-semibold text-muted hover:text-primary transition">
            ← Kembali
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-8 lg:grid-cols-3">
        <!-- Form Area (Matching Mockup) -->
        <div class="space-y-0 lg:col-span-2 rounded-2xl border border-border bg-white p-6 sm:p-8 shadow-sm dark:border-stone-700 dark:bg-stone-800">
            
            <!-- STEP 1: Pilih Layanan -->
            <section>
                <div class="mb-2">
                    <h2 class="text-lg font-bold text-ink dark:text-stone-100">1. Pilih Layanan</h2>
                    <p class="text-sm text-muted mt-1 dark:text-stone-400">Pilih jenis layanan jahit yang Anda butuhkan.</p>
                </div>
                <hr class="my-5 border-border dark:border-stone-700">
                
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($services as $service)
                        <button
                            type="button"
                            wire:click="selectService({{ $service->id }})"
                            @class([
                                'relative flex flex-col items-center rounded-2xl p-5 text-center transition focus:outline-none',
                                'border-2 border-primary bg-primary/5' => $service_id === $service->id,
                                'border border-border bg-white hover:border-primary/50 dark:border-stone-600 dark:bg-stone-700 dark:hover:border-stone-500' => $service_id !== $service->id,
                            ])
                        >
                            @if ($service_id === $service->id)
                                <div class="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-white">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div @class([
                                'mb-3 flex h-12 w-12 items-center justify-center rounded-full',
                                'bg-primary text-white' => $service_id === $service->id,
                                'bg-surface text-muted dark:bg-stone-600 dark:text-stone-300' => $service_id !== $service->id,
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
                            
                            <div class="text-sm font-bold text-ink dark:text-stone-100">{{ $service->name }}</div>
                            <p class="mt-1 text-xs text-muted">{{ $service->description }}</p>
                        </button>
                    @endforeach
                </div>
                @error('service_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            <!-- STEP 2: Detail Pesanan -->
            <section class="mt-12">
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-ink dark:text-stone-300 mb-1.5">Jumlah Item (Pcs)</label>
                        <input
                            type="number"
                            min="1"
                            wire:model.live="quantity"
                            class="w-full rounded-xl border border-border px-4 py-3 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition dark:bg-stone-800 dark:text-stone-100"
                            placeholder="Masukkan jumlah item"
                        />
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-ink dark:text-stone-300 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <textarea
                            rows="4"
                            wire:model="notes"
                            class="w-full rounded-xl border border-border px-4 py-3 text-sm text-ink focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary transition dark:bg-stone-800 dark:text-stone-100"
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
                        <h2 class="text-lg font-bold text-ink dark:text-stone-100">Upload Desain Custom</h2>
                    </div>
                    <hr class="my-5 border-border dark:border-stone-700">
                    
                    <div class="rounded-2xl border border-border bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
                        <div class="flex items-center gap-6">
                            @if ($design_file)
                                <div class="flex-shrink-0">
                                    <img src="{{ $design_file->temporaryUrl() }}" class="h-24 w-24 rounded-xl object-cover shadow-sm border border-border" alt="Preview">
                                </div>
                            @else
                                <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-xl bg-surface border border-border dark:bg-stone-700">
                                    <svg class="h-8 w-8 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-grow">
                                <label for="design_file" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-ink border border-border shadow-sm transition hover:bg-surface dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300 dark:hover:bg-stone-600">
                                    <svg class="h-4 w-4 text-muted dark:text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                                    <p class="mt-2 text-sm font-medium text-ink dark:text-stone-100 truncate max-w-[200px] sm:max-w-xs">{{ $design_file->getClientOriginalName() }}</p>
                                @else
                                    <p class="mt-2 text-xs text-muted dark:text-stone-400">Format yang didukung: JPG, PNG (Max 5MB)</p>
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

            <!-- STEP 3: Rincian Vermak -->
            @if ($this->selectedServiceType() === 'vermak')
                <section class="mt-8">
                    <div class="mb-2">
                        <h2 class="text-lg font-bold text-ink dark:text-stone-100">Rincian Vermak</h2>
                        <p class="text-sm text-muted dark:text-stone-400">Pilih opsi perbaikan pakaian yang Anda butuhkan (bisa lebih dari satu).</p>
                    </div>
                    <hr class="my-5 border-border dark:border-stone-700">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse ($alterationOptions as $option)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-white p-4 shadow-sm hover:border-primary/50 hover:bg-primary/5 transition dark:border-stone-700 dark:bg-stone-800 dark:hover:border-primary/50 dark:hover:bg-stone-700">
                                <div class="flex h-5 items-center mt-0.5">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selected_alterations" 
                                        value="{{ $option->id }}" 
                                        class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                    >
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-bold text-ink dark:text-stone-100 text-sm">{{ $option->name }}</span>
                                        <span class="font-semibold text-primary dark:text-primary text-xs bg-primary/10 px-2 py-0.5 rounded-md">+Rp {{ number_format($option->price, 0, ',', '.') }}</span>
                                    </div>
                                    @if($option->description)
                                        <p class="text-xs text-muted dark:text-stone-400">{{ $option->description }}</p>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-sm text-muted col-span-2">Belum ada opsi vermak tersedia. Silakan hubungi admin.</p>
                        @endforelse
                    </div>
                    @error('selected_alterations')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </section>
            @endif

            <!-- Form Actions -->
            <hr class="mt-12 mb-8 border-border dark:border-stone-700">
            <div class="flex items-center justify-end gap-6 pb-2">
                <a href="{{ route('orders.index') }}" class="text-sm font-bold text-muted hover:text-ink transition dark:hover:text-stone-300" wire:navigate>Batal</a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-bold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">
                        Buat Pesanan Baru
                    </span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                    <svg wire:loading.remove wire:target="submit" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Sticky Footer / Side Panel (Info) -->
        <aside class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-white p-4 shadow-[0_-4px_16px_rgb(0,0,0,0.06)] lg:sticky lg:top-6 lg:z-auto lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm h-fit dark:border-stone-700 dark:bg-stone-800">
            <h2 class="text-xs font-bold text-muted uppercase tracking-wider dark:text-stone-400">Ringkasan Pesanan</h2>
            
            <p class="mt-4 text-sm text-ink dark:text-stone-300 leading-relaxed">
                Pemilihan bahan dan detail lainnya akan ditentukan saat admin menerima pesanan Anda dan/atau setelah sesi fitting (pengukuran).
            </p>
        </aside>
    </form>
</div>

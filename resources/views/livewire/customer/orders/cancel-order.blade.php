<div class="page-enter space-y-6 pb-32 lg:pb-10">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-stone-900 dark:text-stone-100">Batalkan Pesanan</h1>
            <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">Pesanan #{{ $order->order_number }} - {{ $order->service->name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-200" wire:navigate>
            &larr; Kembali
        </a>
    </div>

    <!-- Warning Alert -->
    <div class="rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-6 shadow-sm dark:bg-amber-900/30 dark:border-amber-700">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-amber-900 dark:text-amber-200">Perhatian</h3>
                <p class="mt-1 text-sm text-amber-800 dark:text-amber-300/90">
                    Pembatalan pesanan bersifat permanen dan tidak dapat dibatalkan. Pastikan Anda benar-benar ingin membatalkan pesanan ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="mb-4 text-lg font-bold text-stone-900 dark:text-stone-100">Informasi Pesanan</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-4 dark:border-stone-700">
                        <span class="text-stone-600 dark:text-stone-400">Nomor Pesanan</span>
                        <span class="font-bold text-stone-900 dark:text-stone-100">#{{ $order->order_number }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-stone-100 pb-4 dark:border-stone-700">
                        <span class="text-stone-600 dark:text-stone-400">Layanan</span>
                        <span class="font-bold text-stone-900 dark:text-stone-100">{{ $order->service->name }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-stone-100 pb-4 dark:border-stone-700">
                        <span class="text-stone-600 dark:text-stone-400">Harga Estimasi</span>
                        <span class="font-bold text-stone-900 dark:text-stone-100">Rp{{ number_format($order->estimated_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                        <span class="text-stone-600">Status Saat Ini</span>
                        <span class="inline-flex rounded-lg bg-stone-100 px-3 py-1 text-xs font-bold uppercase text-stone-700">
                            {{ match($order->status) {
                                'menunggu_appointment' => 'Menunggu Appointment',
                                'menunggu_bahan' => 'Menunggu Bahan',
                                'diproses' => 'Diproses',
                                'dijahit' => 'Dijahit',
                                'finishing' => 'Finishing',
                                'selesai' => 'Selesai',
                                default => ucfirst(str_replace('_', ' ', $order->status)),
                            } }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-stone-600 dark:text-stone-400">Tanggal Pemesanan</span>
                        <span class="font-bold text-stone-900 dark:text-stone-100">{{ $order->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cancellation Form -->
            <form wire:submit.prevent="submitCancellation" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800" x-data="{ showCancelModal: false }">
                <h2 class="mb-4 text-lg font-bold text-stone-900 dark:text-stone-100">Alasan Pembatalan</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-700 dark:text-stone-300">
                            Jelaskan alasan Anda membatalkan pesanan ini *
                        </label>
                        <textarea 
                            wire:model="cancellationReason" 
                            rows="4" 
                            class="block w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-stone-900 placeholder-stone-500 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition dark:bg-stone-900 dark:text-stone-100 dark:border-stone-600 dark:placeholder-stone-500 dark:focus:bg-stone-800"
                            placeholder="Ceritakan secara singkat alasan Anda..."
                        ></textarea>
                        @error('cancellationReason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">Minimum 10 karakter, maksimal 500 karakter</p>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="flex gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Catatan:</strong> Pembayaran yang telah terverifikasi akan dipertahankan. Hubungi admin jika ingin mengajukan refund.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="flex-1 rounded-xl border-2 border-stone-300 px-6 py-3 text-center font-bold text-stone-700 hover:bg-stone-50 transition dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="showCancelModal = true"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-center font-bold text-white hover:bg-red-700 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Batalkan Pesanan
                    </button>
                </div>

                {{-- Modal Konfirmasi --}}
                <div 
                    x-show="showCancelModal" 
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm transition-opacity"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <div 
                        @click.outside="showCancelModal = false"
                        class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 shadow-2xl transition-all dark:bg-stone-800 dark:border dark:border-stone-700"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div class="flex items-center justify-center mb-4 h-16 w-16 rounded-full bg-red-50 mx-auto dark:bg-red-900/20">
                            <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-center text-xl font-extrabold text-slate-900 dark:text-stone-100">Konfirmasi Pembatalan</h3>
                        <p class="mt-2 text-center text-sm text-slate-500 dark:text-stone-400">
                            Apakah Anda yakin ingin membatalkan pesanan ini? Tindakan ini tidak dapat dikembalikan.
                        </p>
                        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" @click="showCancelModal = false" class="w-full rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:w-auto transition-colors dark:bg-stone-700 dark:text-stone-300 dark:ring-stone-600 dark:hover:bg-stone-600">Batal</button>
                            <button type="submit" class="w-full rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 sm:w-auto transition-colors">Ya, Batalkan Pesanan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column: Summary -->
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm h-fit dark:border-stone-700 dark:bg-stone-800">
            <h2 class="mb-4 text-lg font-bold text-stone-900 dark:text-stone-100">Ringkasan Pembayaran</h2>
            
            <div class="space-y-4 text-sm">
                @php
                    $verifiedPayments = $order->payments->where('status', 'terverifikasi');
                    $totalPaid = $verifiedPayments->sum('amount');
                @endphp

                <div>
                    <p class="text-stone-600">Total Harga</p>
                    <p class="font-bold text-stone-900">Rp{{ number_format($order->estimated_price, 0, ',', '.') }}</p>
                </div>

                <div class="border-t border-stone-200 pt-4">
                    <p class="text-stone-600">Sudah Dibayar</p>
                    <p class="font-bold text-stone-900">Rp{{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>

                @if ($totalPaid > 0)
                    <div class="rounded-lg bg-blue-50 p-3 border border-blue-100">
                        <p class="text-xs text-blue-800">
                            <strong>Penting:</strong> Pembayaran yang sudah diverifikasi akan tetap tersimpan. Hubungi admin untuk proses refund.
                        </p>
                    </div>
                @else
                    <div class="rounded-lg bg-green-50 p-3 border border-green-100">
                        <p class="text-xs text-green-800">
                            Pesanan ini belum memiliki pembayaran terverifikasi.
                        </p>
                    </div>
                @endif

                <div class="border-t border-stone-200 pt-4">
                    <a 
                        href="{{ route('orders.show', $order) }}" 
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-stone-300 px-4 py-3 text-center font-bold text-stone-700 hover:bg-stone-50 transition"
                        wire:navigate
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

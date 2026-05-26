<div class="page-enter space-y-6 pb-32 lg:pb-10">
    <!-- Header -->
    <div data-reveal class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Batalkan Pesanan</h1>
            <p class="mt-1 text-sm text-stone-600">Pesanan #{{ $order->order_number }} - {{ $order->service->name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-stone-500 hover:text-stone-900" wire:navigate>
            &larr; Kembali
        </a>
    </div>

    <!-- Warning Alert -->
    <div data-reveal data-reveal-delay="1" class="rounded-2xl border-l-4 border-amber-500 bg-amber-50 p-6 shadow-sm">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-amber-900">Perhatian</h3>
                <p class="mt-1 text-sm text-amber-800">
                    Pembatalan pesanan bersifat permanen dan tidak dapat dibatalkan. Pastikan Anda benar-benar ingin membatalkan pesanan ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div data-reveal data-reveal-delay="2" class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-stone-900">Informasi Pesanan</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                        <span class="text-stone-600">Nomor Pesanan</span>
                        <span class="font-bold text-stone-900">#{{ $order->order_number }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                        <span class="text-stone-600">Layanan</span>
                        <span class="font-bold text-stone-900">{{ $order->service->name }}</span>
                    </div>

                    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                        <span class="text-stone-600">Harga Estimasi</span>
                        <span class="font-bold text-stone-900">Rp{{ number_format($order->estimated_price, 0, ',', '.') }}</span>
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
                        <span class="text-stone-600">Tanggal Pemesanan</span>
                        <span class="font-bold text-stone-900">{{ $order->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cancellation Reason Form -->
            <form wire:submit.prevent="submitCancellation" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-stone-900">Alasan Pembatalan</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-stone-700">
                            Jelaskan alasan Anda membatalkan pesanan ini *
                        </label>
                        <textarea
                            wire:model="cancellationReason"
                            rows="5"
                            placeholder="Contoh: Saya sudah tidak membutuhkan layanan ini, alasan pribadi, dll."
                            class="block w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-stone-900 placeholder-stone-500 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                        ></textarea>
                        @error('cancellationReason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-stone-500">Minimum 10 karakter, maksimal 500 karakter</p>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div class="flex gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-blue-800">
                            <strong>Catatan:</strong> Pembayaran yang telah terverifikasi akan dipertahankan. Hubungi admin jika ingin mengajukan refund.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex gap-3">
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="flex-1 rounded-xl border-2 border-stone-300 px-6 py-3 text-center font-bold text-stone-700 hover:bg-stone-50 transition"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-red-600 px-6 py-3 text-center font-bold text-white hover:bg-red-700 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column: Summary -->
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm h-fit">
            <h2 class="mb-4 text-lg font-bold text-stone-900">Ringkasan Pembayaran</h2>
            
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

<div class="page-enter space-y-6 pb-32 lg:pb-10">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-ink dark:text-stone-100">Pembayaran Ditolak</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">Pesanan #{{ $payment->order->order_number }}</p>
        </div>
        <a href="{{ route('payments.history', $payment->order) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-muted transition hover:text-primary mb-3 dark:text-stone-400 dark:hover:text-stone-300" wire:navigate>
            ← Kembali
        </a>
    </div>

    <!-- Alert Card -->
    <div class="rounded-2xl border-l-4 border-red-500 bg-red-50 p-6 shadow-sm dark:border-red-900 dark:bg-red-900/20">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2m-6-4v2m6-12v2m0 4v2m0 4v2m6-4v2m-6-12v2m0 4v2m0 4v2" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-red-900 dark:text-red-300">Pembayaran Anda Ditolak</h3>
                <p class="mt-1 text-sm text-red-800 dark:text-red-400">
                    Pembayaran Anda tidak dapat diverifikasi oleh admin. Silakan periksa alasan penolakan di bawah dan coba lagi.
                </p>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Payment Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Rejection Reason -->
            <div class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm dark:border-red-900/50 dark:bg-stone-800">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-ink dark:text-stone-100">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Alasan Penolakan
                </h2>
                <div class="rounded-lg bg-red-50 p-4 border border-red-100 dark:bg-red-900/30 dark:border-red-900/50">
                    <p class="text-red-900 font-medium leading-relaxed dark:text-red-300">
                        {{ $payment->rejection_note ?? 'Tidak ada penjelasan yang diberikan' }}
                    </p>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="mb-4 text-lg font-bold text-ink dark:text-stone-100">Informasi Pembayaran</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-border pb-4 dark:border-stone-700">
                        <span class="text-muted dark:text-stone-400">Tipe Pembayaran</span>
                        <span class="font-bold text-ink dark:text-stone-100">
                            @if ($payment->payment_type === 'dp')
                                Down Payment (DP)
                            @else
                                Pelunasan
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-border pb-4 dark:border-stone-700">
                        <span class="text-muted dark:text-stone-400">Metode Pembayaran</span>
                        <span class="font-bold text-ink dark:text-stone-100">
                            @switch($payment->payment_method)
                                @case('transfer')
                                    Transfer Bank
                                    @break
                                @case('qris')
                                    QRIS
                                    @break
                                @case('cash')
                                    Tunai
                                    @break
                                @default
                                    Tidak Diketahui
                            @endswitch
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-border pb-4 dark:border-stone-700">
                        <span class="text-muted dark:text-stone-400">Jumlah Pembayaran</span>
                        <span class="font-bold text-lg text-ink dark:text-stone-100">
                            Rp{{ number_format($payment->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-muted dark:text-stone-400">Tanggal Pengajuan</span>
                        <span class="font-bold text-ink dark:text-stone-100">
                            {{ $payment->created_at->format('d F Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- What to do next -->
            <div class="rounded-2xl border border-primary/20 bg-primary/5 p-6 shadow-sm dark:border-blue-900/50 dark:bg-blue-900/10">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-bold text-primary dark:text-blue-300">
                    <svg class="h-5 w-5 text-primary dark:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Langkah Selanjutnya
                </h2>
                
                <ol class="space-y-3 text-sm text-primary dark:text-blue-300">
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">1</span>
                        <span>Periksa kembali alasan penolakan di atas</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">2</span>
                        <span>Pastikan bukti pembayaran Anda jelas dan sesuai dengan aturan yang berlaku</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">3</span>
                        <span>Klik tombol "Bayar Ulang" untuk membuat pembayaran baru</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">4</span>
                        <span>Tunggu admin untuk memverifikasi pembayaran Anda</span>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="rounded-2xl border border-border bg-white p-6 shadow-sm h-fit dark:border-stone-700 dark:bg-stone-800">
            <h2 class="mb-4 text-lg font-bold text-ink dark:text-stone-100">Ringkasan Pesanan</h2>
            
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-muted dark:text-stone-400">Nomor Pesanan</p>
                    <p class="font-bold text-ink dark:text-stone-100">#{{ $payment->order->order_number }}</p>
                </div>

                <div>
                    <p class="text-muted dark:text-stone-400">Layanan</p>
                    <p class="font-bold text-ink dark:text-stone-100">{{ $payment->order->service->name }}</p>
                </div>

                <div>
                    <p class="text-muted dark:text-stone-400">Harga Pesanan</p>
                    <p class="font-bold text-ink dark:text-stone-100">Rp{{ number_format($payment->order->estimated_price, 0, ',', '.') }}</p>
                </div>

                <div class="border-t border-border pt-4 dark:border-stone-700">
                    <a 
                        href="{{ route('orders.show', $payment->order) }}" 
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-ink px-4 py-3 text-center font-bold text-white hover:bg-ink/90 transition dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-stone-200"
                        wire:navigate
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Retry Button -->
    <div class="flex gap-3">
        <a
            href="{{ route('payments.create', $payment->order) }}"
            class="flex-1 flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-4 text-center font-bold text-white hover:bg-primary-hover transition shadow-md shadow-primary/25"
            wire:navigate
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Bayar Ulang Sekarang
        </a>
        <a
            href="{{ route('payments.history', $payment->order) }}"
            class="flex-1 flex items-center justify-center gap-2 rounded-full border-2 border-border px-6 py-4 text-center font-bold text-ink hover:bg-surface transition dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-800"
            wire:navigate
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>
</div>

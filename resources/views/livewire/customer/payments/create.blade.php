<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-32 sm:px-6 lg:pb-10">
    <div class="flex flex-col gap-4 rounded-3xl border border-border bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between dark:border-stone-700 dark:bg-stone-800">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-accent">
                Konfirmasi Pembayaran
            </p>
            <h1 class="mt-3 font-display text-3xl font-bold text-ink dark:text-stone-100">Pembayaran Tagihan</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">Pesanan #{{ $order->order_number }} - {{ $order->service->name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" wire:navigate
           class="inline-flex items-center gap-2 text-xs font-semibold text-muted hover:text-primary transition sm:pr-4">
            ← Kembali
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            
            <!-- STEP 1: Metode Pembayaran -->
            <section>
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">1</span>
                    <h2 class="text-xl font-bold text-ink dark:text-stone-100">Metode Pembayaran</h2>
                </div>
                
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach (['transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'cash' => 'Cash (Di Tempat)'] as $value => $label)
                        <label
                            @class([
                                'relative flex cursor-pointer flex-col rounded-2xl border-2 p-5 text-center transition focus:outline-none',
                                'border-primary bg-primary/5 shadow-sm dark:bg-blue-900/20' => $payment_method === $value,
                                'border-border bg-white hover:border-primary/50 hover:bg-primary/5 dark:border-stone-600 dark:bg-stone-700 dark:hover:bg-stone-600 dark:hover:border-stone-500' => $payment_method !== $value,
                            ])
                        >
                            <input type="radio" class="sr-only" value="{{ $value }}" wire:model.live="payment_method" />
                            @if ($payment_method === $value)
                                <div class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-white shadow-sm">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                            <span class="font-bold text-ink dark:text-stone-100">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_method')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Info Rekening / QRIS -->
                @if ($payment_method === 'transfer')
                    <div class="mt-6 rounded-2xl border border-primary/20 bg-primary/5 p-6 shadow-sm dark:border-blue-800 dark:bg-blue-900/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-primary/80">Transfer ke Bank BCA</p>
                                <p class="text-2xl font-black tracking-widest text-primary">1234 567 890</p>
                                <p class="text-sm font-medium text-ink dark:text-stone-300">A.n. Jahitly Tailor</p>
                            </div>
                        </div>
                    </div>
                @elseif ($payment_method === 'qris')
                    <div class="mt-6 flex flex-col items-center rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <p class="mb-4 text-sm font-bold text-ink dark:text-stone-100">Scan QR Code di bawah ini</p>
                        <div class="rounded-2xl border-4 border-border p-2">
                            <img src="{{ asset('images/qris-sample.svg') }}" alt="QRIS Code" class="w-48 rounded-xl object-contain" />
                        </div>
                    </div>
                @elseif ($payment_method === 'cash')
                    <div class="mt-6 rounded-2xl border border-border bg-surface p-6 text-center text-sm font-medium text-muted shadow-sm">
                        Silakan datang langsung ke lokasi kami untuk melakukan pembayaran tunai. Admin akan memverifikasi pembayaran Anda secara manual.
                    </div>
                @endif
            </section>

            <!-- STEP 2: Nominal Pembayaran -->
            <section>
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">2</span>
                    <h2 class="text-xl font-bold text-ink dark:text-stone-100">Nominal Transfer</h2>
                </div>
                
                <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <label class="text-sm font-semibold text-ink dark:text-stone-300">Masukkan jumlah yang ditransfer (Rp)</label>
                    <div class="relative mt-2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-muted font-bold dark:text-stone-400">Rp</span>
                        </div>
                        <input
                            type="text"
                            wire:model="amount"
                            readonly
                            class="block w-full rounded-xl border border-border bg-surface py-3 pl-12 pr-4 text-lg font-bold text-ink focus:outline-none transition dark:bg-stone-800 dark:text-stone-300 dark:border-stone-700 cursor-not-allowed"
                        />
                    </div>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <!-- STEP 3: Bukti Transfer -->
            @if(in_array($payment_method, ['transfer', 'qris']))
                <section>
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">3</span>
                        <h2 class="text-xl font-bold text-ink dark:text-stone-100">Upload Bukti</h2>
                    </div>
                    
                    <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <div x-data="{ isDropping: false }" 
                             @dragover.prevent="isDropping = true" 
                             @dragleave.prevent="isDropping = false" 
                             @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="isDropping ? 'border-primary bg-primary/5' : 'border-border bg-surface dark:border-stone-600 dark:bg-stone-700'"
                             class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-12 text-center transition hover:border-primary/50 hover:bg-primary/5 dark:hover:bg-stone-600">
                            
                            <input
                                x-ref="fileInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.pdf"
                                wire:model="proof_file"
                                class="absolute inset-0 z-50 h-full w-full cursor-pointer opacity-0"
                            />
                            
                            @if ($proof_file)
                                @php
                                    $proofExtension = strtolower($proof_file->getClientOriginalExtension());
                                    $proofIsImage = in_array($proofExtension, ['jpg', 'jpeg', 'png'], true);
                                @endphp
                                <div class="flex flex-col items-center">
                                    @if ($proofIsImage)
                                        <img src="{{ $proof_file->temporaryUrl() }}" class="h-48 w-auto rounded-lg object-contain shadow-sm" alt="Preview">
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <p class="mt-3 text-sm font-semibold text-ink dark:text-stone-300">{{ $proof_file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-muted dark:text-stone-400">Klik atau drag untuk mengganti file</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center text-muted dark:text-stone-400">
                                    <svg class="mb-3 h-10 w-10 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                    <span class="text-sm font-bold text-ink dark:text-stone-100">Drag & drop struk transfer</span>
                                    <span class="mt-1 text-xs">Atau klik untuk memilih file (JPG/PNG/PDF)</span>
                                </div>
                            @endif
                        </div>
                        
                        @error('proof_file')
                            <p class="mt-2 text-xs font-medium text-red-600 text-center">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="proof_file" class="mt-2 text-center text-xs font-semibold text-blue-600">
                            Sedang memproses file...
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <!-- Sticky Footer / Side Panel -->
        <aside class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-white p-4 shadow-[0_-4px_16px_rgb(0,0,0,0.06)] lg:sticky lg:top-6 lg:z-auto lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm dark:border-stone-700 dark:bg-stone-800">
            <h3 class="hidden text-xl font-bold text-ink lg:block dark:text-stone-100">Rincian Tagihan</h3>

            @php
                $billAmount = $paymentType === 'dp' ? $dpAmount : $remainingAmount;
            @endphp

            <div class="mt-4 rounded-xl bg-primary/5 p-4 border border-primary/20 dark:bg-blue-900/20 dark:border-blue-800">
                <p class="text-xs font-semibold text-primary/80 dark:text-blue-400">Total Tagihan</p>
                <p class="mt-1 text-2xl font-black text-primary dark:text-blue-400">Rp {{ number_format($billAmount, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-primary/80 dark:text-blue-400">{{ $paymentType === 'dp' ? 'Pembayaran DP' : 'Pelunasan' }}</p>
            </div>
            
            <div class="hidden lg:block lg:mt-6 lg:space-y-3 lg:text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-muted">Tipe Pembayaran</span>
                    <span class="font-bold text-ink">{{ strtoupper($paymentType) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-muted">Total Harga Pesanan</span>
                    <span class="font-medium text-ink">Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-muted">Sudah Dibayar</span>
                    <span class="font-medium text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                </div>
                @if ($paymentType === 'dp')
                    <div class="flex items-center justify-between">
                        <span class="text-muted">Tagihan DP</span>
                        <span class="font-bold text-ink">Rp {{ number_format($dpAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-muted text-xs mt-2 pt-2 border-t border-border">
                    <span>Sisa Keseluruhan</span>
                    <span>Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                </div>
                <div class="my-4 h-px bg-border"></div>
            </div>

            <div class="flex items-center justify-between lg:flex-col lg:items-stretch lg:justify-start lg:gap-6">
                <div>
                    <p class="text-xs font-semibold text-muted lg:text-sm">Nominal Yang Harus Dibayar</p>
                    <p class="text-xl font-black text-primary lg:mt-1 lg:text-2xl">
                        Rp {{ number_format($billAmount, 0, ',', '.') }}
                    </p>
                </div>
                
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-primary px-6 py-3 text-base font-semibold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 lg:w-full"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Kirim Konfirmasi</span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                </button>
            </div>
            
            @if ($hasPendingPayment)
                <div class="mt-4 rounded-xl bg-accent-soft p-3 text-xs font-medium text-accent border border-accent/20">
                    <span class="font-bold">Info:</span> Ada pembayaran sebelumnya yang sedang menunggu verifikasi admin.
                </div>
            @endif
        </aside>
    </form>
</div>

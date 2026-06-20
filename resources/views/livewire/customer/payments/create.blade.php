<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-32 sm:px-6 lg:pb-10">
    <div class="flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white/90 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-600">
                Konfirmasi Pembayaran
            </p>
            <h1 class="mt-3 text-3xl font-bold text-stone-900">Pembayaran Tagihan</h1>
            <p class="mt-1 text-sm text-stone-600">Pesanan #{{ $order->order_number }} - {{ $order->service->name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 transition hover:text-stone-900 sm:pr-4" wire:navigate>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="submit" class="grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            
            <!-- STEP 1: Metode Pembayaran -->
            <section>
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-sm font-semibold text-white shadow-sm">1</span>
                    <h2 class="text-xl font-bold text-stone-900">Metode Pembayaran</h2>
                </div>
                
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach (['transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'cash' => 'Cash (Di Tempat)'] as $value => $label)
                        <label
                            @class([
                                'relative flex cursor-pointer flex-col rounded-2xl border-2 p-5 text-center transition focus:outline-none',
                                'border-blue-600 bg-blue-50/50 shadow-sm' => $payment_method === $value,
                                'border-stone-200 bg-white hover:border-stone-300 hover:bg-stone-50' => $payment_method !== $value,
                            ])
                        >
                            <input type="radio" class="sr-only" value="{{ $value }}" wire:model.live="payment_method" />
                            @if ($payment_method === $value)
                                <div class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-white shadow">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            @endif
                            <span class="font-bold text-stone-900">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('payment_method')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Info Rekening / QRIS -->
                @if ($payment_method === 'transfer')
                    <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-blue-900">Transfer ke Bank BCA</p>
                                <p class="text-2xl font-black tracking-widest text-blue-700">1234 567 890</p>
                                <p class="text-sm font-medium text-blue-800">A.n. Jahitly Tailor</p>
                            </div>
                        </div>
                    </div>
                @elseif ($payment_method === 'qris')
                    <div class="mt-6 flex flex-col items-center rounded-2xl border border-stone-200 bg-white/90 p-6 shadow-sm">
                        <p class="mb-4 text-sm font-bold text-stone-900">Scan QR Code di bawah ini</p>
                        <div class="rounded-2xl border-4 border-stone-100 p-2">
                            <img src="{{ asset('images/qris-sample.svg') }}" alt="QRIS Code" class="w-48 rounded-xl object-contain" />
                        </div>
                    </div>
                @elseif ($payment_method === 'cash')
                    <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50 p-6 text-center text-sm font-medium text-stone-600 shadow-sm">
                        Silakan datang langsung ke lokasi kami untuk melakukan pembayaran tunai. Admin akan memverifikasi pembayaran Anda secara manual.
                    </div>
                @endif
            </section>

            <!-- STEP 2: Nominal Pembayaran -->
            <section>
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-sm font-semibold text-white shadow-sm">2</span>
                    <h2 class="text-xl font-bold text-stone-900">Nominal Transfer</h2>
                </div>
                
                <div class="rounded-2xl border border-stone-200 bg-white/90 p-6 shadow-sm">
                    <label class="text-sm font-semibold text-stone-700">Masukkan jumlah yang ditransfer (Rp)</label>
                    <div class="relative mt-2">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-stone-500 font-bold">Rp</span>
                        </div>
                        <input
                            type="number"
                            min="1000"
                            wire:model.live="amount"
                            class="block w-full rounded-xl border border-stone-300 bg-stone-50 py-3 pl-12 pr-4 text-lg font-bold text-stone-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
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
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-sm font-semibold text-white shadow-sm">3</span>
                        <h2 class="text-xl font-bold text-stone-900">Upload Bukti</h2>
                    </div>
                    
                    <div class="rounded-2xl border border-stone-200 bg-white/90 p-6 shadow-sm">
                        <div x-data="{ isDropping: false }" 
                             @dragover.prevent="isDropping = true" 
                             @dragleave.prevent="isDropping = false" 
                             @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-stone-300 bg-stone-50'"
                             class="relative flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-12 text-center transition hover:border-blue-400 hover:bg-stone-100">
                            
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
                                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <p class="mt-3 text-sm font-semibold text-stone-700">{{ $proof_file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-stone-500">Klik atau drag untuk mengganti file</p>
                                </div>
                            @else
                                <div class="flex flex-col items-center text-stone-500">
                                    <svg class="mb-3 h-10 w-10 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                    <span class="text-sm font-bold text-stone-700">Drag & drop struk transfer</span>
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
        <aside class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white p-4 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] lg:sticky lg:top-6 lg:z-auto lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm">
            <h3 class="hidden text-xl font-bold text-stone-900 lg:block">Rincian Tagihan</h3>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-center">
                <p class="text-xs font-semibold text-blue-700">Total Tagihan</p>
                <p class="mt-1 text-2xl font-black text-blue-700">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-blue-600">{{ $paymentType === 'dp' ? 'Pembayaran DP' : 'Pelunasan' }}</p>
            </div>
            
            <div class="hidden lg:block lg:mt-6 lg:space-y-3 lg:text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Tipe Pembayaran</span>
                    <span class="font-bold text-stone-900">{{ strtoupper($paymentType) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Total Harga Pesanan</span>
                    <span class="font-medium text-stone-900">Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-stone-500">Sudah Dibayar</span>
                    <span class="font-medium text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                </div>
                <div class="my-4 h-px bg-stone-200"></div>
            </div>

            <div class="flex items-center justify-between lg:flex-col lg:items-stretch lg:justify-start lg:gap-6">
                <div>
                    <p class="text-xs font-semibold text-stone-500 lg:text-sm">Sisa Tagihan</p>
                    <p class="text-xl font-black text-blue-600 lg:mt-1 lg:text-2xl">
                        Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                    </p>
                </div>
                
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 lg:w-full"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Kirim Konfirmasi</span>
                    <span wire:loading wire:target="submit">Memproses...</span>
                </button>
            </div>
            
            @if ($hasPendingPayment)
                <div class="mt-4 rounded-lg bg-amber-50 p-3 text-xs font-medium text-amber-700">
                    <span class="font-bold">Info:</span> Ada pembayaran sebelumnya yang sedang menunggu verifikasi admin.
                </div>
            @endif
        </aside>
    </form>
</div>

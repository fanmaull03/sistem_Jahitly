<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Konfirmasi Pembayaran</h1>
            <p class="text-sm text-slate-600">Pesanan {{ $order->order_number }} - {{ $order->service->name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900" wire:navigate>
            Kembali ke detail
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <form wire:submit.prevent="submit" class="space-y-6">
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Metode Pembayaran</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        @foreach (['transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'cash' => 'Cash'] as $value => $label)
                            <label
                                @class([
                                    'rounded-lg border p-4 text-sm font-semibold transition',
                                    'border-slate-200 text-slate-700 hover:border-slate-300' => $payment_method !== $value,
                                    'border-emerald-400 bg-emerald-50 text-emerald-900' => $payment_method === $value,
                                ])
                            >
                                <input type="radio" class="sr-only" value="{{ $value }}" wire:model.live="payment_method" />
                                <div>{{ $label }}</div>
                                <div class="mt-1 text-xs font-normal text-slate-500">Pilih metode {{ strtolower($label) }}.</div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror

                    @if ($payment_method === 'transfer')
                        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            <div class="font-semibold text-slate-700">Bank BCA</div>
                            <div>No. Rekening: 1234567890</div>
                            <div>A.n. Jahitly</div>
                        </div>
                    @elseif ($payment_method === 'qris')
                        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            <div class="font-semibold text-slate-700">Scan QRIS</div>
                            <img
                                src="{{ asset('images/qris-sample.svg') }}"
                                alt="QRIS"
                                class="mt-3 w-40"
                            />
                        </div>
                    @else
                        <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            Pembayaran cash dilakukan langsung di lokasi. Admin tetap akan memverifikasi.
                        </div>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Nominal Pembayaran</h2>
                    <div class="mt-4">
                        <label class="text-sm font-semibold text-slate-700">Masukkan nominal</label>
                        <input
                            type="number"
                            min="1000"
                            wire:model.live="amount"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-200"
                        />
                        @error('amount')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Upload Bukti Pembayaran</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $requiresProofFile ? 'Wajib untuk transfer dan QRIS.' : 'Opsional untuk pembayaran cash.' }}
                    </p>
                    <div class="mt-4">
                        <input
                            type="file"
                            accept=".jpg,.jpeg,.png,.pdf"
                            wire:model="proof_file"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                        />
                        @error('proof_file')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="proof_file" class="mt-2 text-sm text-slate-500">
                            Mengunggah bukti pembayaran...
                        </div>
                        @if ($proof_file)
                            @php
                                $proofExtension = strtolower($proof_file->getClientOriginalExtension());
                                $proofIsImage = in_array($proofExtension, ['jpg', 'jpeg', 'png'], true);
                            @endphp
                            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <div class="text-xs font-semibold text-slate-500">Pratinjau</div>
                                @if ($proofIsImage)
                                    <img src="{{ $proof_file->temporaryUrl() }}" alt="Preview" class="mt-3 w-full rounded-lg" />
                                @else
                                    <p class="mt-2 text-sm text-slate-700">File terpilih: {{ $proof_file->getClientOriginalName() }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500 disabled:cursor-not-allowed disabled:opacity-60"
                    wire:loading.attr="disabled"
                >
                    Kirim Pembayaran
                </button>
            </form>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Ringkasan Tagihan</h3>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tipe pembayaran</span>
                        <span class="font-semibold text-slate-900">{{ strtoupper($paymentType) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Total estimasi</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Total terbayar</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-px bg-slate-200"></div>
                    <div class="flex items-center justify-between text-base">
                        <span class="font-semibold text-slate-900">Sisa bayar</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @if ($hasPendingPayment)
                    <p class="mt-4 text-sm text-amber-600">
                        Ada pembayaran sebelumnya yang masih menunggu verifikasi.
                    </p>
                @endif
            </div>
        </aside>
    </div>
</div>

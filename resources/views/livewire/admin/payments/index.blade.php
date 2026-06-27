<div class="page-enter space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Verifikasi Pembayaran</h1>
        <p class="text-sm text-slate-500 dark:text-stone-400">Periksa bukti pembayaran customer.</p>
    </div>

    <div class="space-y-4">
        @forelse ($payments as $payment)
            <div class="hover-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-base font-semibold text-slate-900 dark:text-stone-100">
                            {{ $payment->order->order_number }} - {{ $payment->customer->name }}
                        </div>
                        <div class="text-sm text-slate-500 dark:text-stone-400">
                            {{ strtoupper($payment->payment_type) }} · {{ strtoupper($payment->payment_method) }}
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900 dark:text-stone-100">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="openProof({{ $payment->id }})"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-base font-semibold text-slate-700 hover:border-slate-300 dark:border-stone-600 dark:text-stone-300 dark:hover:border-stone-500"
                    >
                        Cek Bukti
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-6 text-sm text-slate-500 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-400">
                Tidak ada pembayaran yang menunggu verifikasi.
            </div>
        @endforelse
    </div>

    <div class="border-t border-slate-200 pt-4 dark:border-stone-700">
        {{ $payments->links() }}
    </div>

    @if ($activePaymentId && $activePayment)
        @php
            $proofPath = $activePayment->proof_file_path;
            $extension = $proofPath ? strtolower(pathinfo($proofPath, PATHINFO_EXTENSION)) : '';
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png'], true);
        @endphp

        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 sm:p-6">
            <div class="modal-content-enter flex w-full max-w-3xl flex-col max-h-full overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-stone-800">
                <div class="flex flex-shrink-0 items-center justify-between border-b border-slate-100 p-5 dark:border-stone-700">
                    <div>
                        <div class="text-lg font-semibold">Bukti Pembayaran</div>
                        <div class="text-sm text-slate-500 dark:text-stone-400">
                            {{ $activePayment->order->order_number }} · {{ $activePayment->customer->name }}
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeProof"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-700"
                    >
                        Tutup
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5">
                    @if ($proofPath)
                        @if ($isImage)
                            <div class="flex items-center justify-center rounded-xl bg-slate-50 p-2 sm:p-4 border border-slate-100 dark:bg-stone-700/30 dark:border-stone-700">
                                <img
                                    src="{{ route('payments.proof', $activePayment) }}"
                                    alt="Bukti"
                                    class="max-h-[50vh] max-w-full rounded-lg object-contain shadow-sm"
                                />
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center">
                                <a
                                    href="{{ route('payments.proof', $activePayment) }}"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                                    target="_blank"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Buka File Bukti di Tab Baru
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500 dark:border-stone-600 dark:text-stone-400">
                            Tidak ada bukti pembayaran yang diunggah.
                        </div>
                    @endif

                    @if ($showRejectForm)
                        <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-800 dark:bg-rose-900/20">
                            <label class="text-sm font-semibold text-rose-800">Alasan Penolakan</label>
                            <textarea
                                rows="3"
                                wire:model="rejectionNote"
                                class="mt-2 w-full rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400"
                                placeholder="Tuliskan alasan mengapa pembayaran ini ditolak..."
                            ></textarea>
                            @error('rejectionNote')
                                <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    wire:click="rejectPayment({{ $activePayment->id }})"
                                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500"
                                >
                                    Kirim Penolakan
                                </button>
                                <button
                                    type="button"
                                    wire:click="cancelReject"
                                    class="rounded-xl border border-rose-200 bg-white px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50"
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-shrink-0 flex-col gap-3 border-t border-slate-100 bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between rounded-b-2xl dark:border-stone-700 dark:bg-stone-700/50">
                    <div class="text-sm font-medium text-slate-500 dark:text-stone-400">
                        Total Pembayaran: <span class="font-bold text-slate-900 dark:text-stone-100 text-lg">Rp {{ number_format($activePayment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="startReject"
                            class="flex-1 sm:flex-none rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-rose-500"
                        >
                            Tolak
                        </button>
                        <button
                            type="button"
                            wire:click="approvePayment({{ $activePayment->id }})"
                            class="flex-1 sm:flex-none rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-500 shadow-sm"
                        >
                            Setujui
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

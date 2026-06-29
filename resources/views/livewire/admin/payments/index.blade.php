<div class="page-enter space-y-6">
    <x-slot name="header">Verifikasi Pembayaran</x-slot>
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Verifikasi</p>
        <h1 class="mt-0.5 text-2xl font-bold text-ink dark:text-stone-100">Pembayaran</h1>
        <p class="text-sm text-muted dark:text-stone-400">Periksa bukti pembayaran customer.</p>
    </div>

    <div class="space-y-4">
        @forelse ($payments as $payment)
            <div class="group relative overflow-hidden rounded-2xl border border-border bg-white shadow-sm transition hover:shadow-md hover:-translate-y-0.5 dark:border-stone-700 dark:bg-stone-800">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-accent to-accent/50"></div>
                <div class="p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-muted">
                                {{ $payment->order->order_number }}
                            </p>
                            <p class="mt-1 text-base font-bold text-ink dark:text-stone-100">
                                {{ $payment->customer->name }}
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="rounded bg-surface px-2 py-1 text-xs font-semibold text-muted dark:bg-stone-700 dark:text-stone-300">
                                    {{ strtoupper($payment->payment_type) }}
                                </span>
                                <span class="rounded bg-surface px-2 py-1 text-xs font-semibold text-muted dark:bg-stone-700 dark:text-stone-300">
                                    {{ strtoupper($payment->payment_method) }}
                                </span>
                            </div>
                            <div class="mt-3 text-2xl font-extrabold text-ink dark:text-stone-100">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </div>
                        </div>
                        <button type="button" wire:click="openProof({{ $payment->id }})"
                                class="shrink-0 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                            Cek Bukti
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-border bg-white p-12 text-center dark:border-stone-700 dark:bg-stone-800">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 mb-4">
                    <svg class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-ink dark:text-stone-300">Semua pembayaran sudah terverifikasi.</p>
                <p class="mt-1 text-xs text-muted dark:text-stone-400">Tidak ada bukti baru yang perlu diperiksa saat ini.</p>
            </div>
        @endforelse
    </div>

    <div class="border-t border-border pt-4 dark:border-stone-700">
        {{ $payments->links() }}
    </div>

    @if ($activePaymentId && $activePayment)
        @php
            $proofPath = $activePayment->proof_file_path;
            $extension = $proofPath ? strtolower(pathinfo($proofPath, PATHINFO_EXTENSION)) : '';
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png'], true);
        @endphp

        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-ink/60 p-4 sm:p-6 backdrop-blur-sm">
            <div class="modal-content-enter flex w-full max-w-3xl flex-col max-h-full overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-stone-800">
                <div class="flex flex-shrink-0 items-center justify-between border-b border-border p-6 dark:border-stone-700">
                    <div>
                        <h2 class="text-lg font-bold text-ink dark:text-stone-100">Bukti Pembayaran</h2>
                        <p class="mt-0.5 text-sm font-medium text-muted">
                            {{ $activePayment->order->order_number }} · {{ $activePayment->customer->name }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeProof"
                            class="rounded-full p-2 text-muted transition hover:bg-surface hover:text-ink dark:hover:bg-stone-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    @if ($proofPath)
                        @if ($isImage)
                            <div class="flex items-center justify-center rounded-2xl bg-surface p-4 border border-border dark:bg-stone-700/30 dark:border-stone-700">
                                <img src="{{ route('payments.proof', $activePayment) }}" alt="Bukti"
                                     class="max-h-[50vh] max-w-full rounded-xl object-contain shadow-sm" />
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-border p-12 text-center">
                                <a href="{{ route('payments.proof', $activePayment) }}" target="_blank"
                                   class="inline-flex items-center gap-2 rounded-xl bg-primary/10 px-5 py-2.5 text-sm font-bold text-primary transition hover:bg-primary/20">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Buka File Bukti di Tab Baru
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="rounded-2xl border border-dashed border-border p-12 text-center text-sm font-medium text-muted">
                            Tidak ada bukti pembayaran yang diunggah.
                        </div>
                    @endif

                    @if ($showRejectForm)
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-900/50 dark:bg-rose-900/10">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-rose-700 dark:text-rose-400">Alasan Penolakan</label>
                            <textarea rows="3" wire:model="rejectionNote"
                                      class="mt-2 w-full rounded-xl border border-rose-200 bg-white px-4 py-3 text-sm text-ink 
                                             focus:border-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-400/20
                                             dark:bg-stone-800 dark:border-rose-800 dark:text-stone-100"
                                      placeholder="Tuliskan alasan mengapa pembayaran ini ditolak..."></textarea>
                            @error('rejectionNote') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                            
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" wire:click="rejectPayment({{ $activePayment->id }})"
                                        class="rounded-xl bg-rose-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-rose-700">
                                    Kirim Penolakan
                                </button>
                                <button type="button" wire:click="cancelReject"
                                        class="rounded-xl border border-rose-200 bg-white px-5 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-800 dark:bg-stone-800 dark:text-rose-400">
                                    Batal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-shrink-0 flex-col gap-4 border-t border-border bg-surface p-6 sm:flex-row sm:items-center sm:justify-between dark:border-stone-700 dark:bg-stone-800/80">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted">Total Pembayaran</p>
                        <p class="mt-0.5 text-xl font-extrabold text-ink dark:text-stone-100">
                            Rp {{ number_format($activePayment->amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" wire:click="startReject"
                                class="flex-1 sm:flex-none rounded-xl bg-rose-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-rose-700 shadow-sm">
                            Tolak
                        </button>
                        <button type="button" wire:click="approvePayment({{ $activePayment->id }})"
                                class="flex-1 sm:flex-none rounded-xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-600 shadow-sm shadow-emerald-500/20">
                            Setujui
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

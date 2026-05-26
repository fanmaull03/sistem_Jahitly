<div class="page-enter space-y-6">
    <div data-reveal>
        <h1 class="text-2xl font-bold">Verifikasi Pembayaran</h1>
        <p class="text-sm text-slate-500">Periksa bukti pembayaran customer.</p>
    </div>

    <div data-reveal data-reveal-delay="1" class="space-y-4">
        @forelse ($payments as $payment)
            <div class="hover-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-base font-semibold text-slate-900">
                            {{ $payment->order->order_number }} - {{ $payment->customer->name }}
                        </div>
                        <div class="text-sm text-slate-500">
                            {{ strtoupper($payment->payment_type) }} · {{ strtoupper($payment->payment_method) }}
                        </div>
                        <div class="mt-2 text-lg font-bold text-slate-900">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="openProof({{ $payment->id }})"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-base font-semibold text-slate-700 hover:border-slate-300"
                    >
                        Cek Bukti
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-6 text-sm text-slate-500">
                Tidak ada pembayaran yang menunggu verifikasi.
            </div>
        @endforelse
    </div>

    <div class="border-t border-slate-200 pt-4">
        {{ $payments->links() }}
    </div>

    @if ($showProofModal && $activePayment)
        @php
            $proofPath = $activePayment->proof_file_path;
            $extension = $proofPath ? strtolower(pathinfo($proofPath, PATHINFO_EXTENSION)) : '';
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png'], true);
        @endphp

        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="modal-content-enter w-full max-w-3xl rounded-2xl bg-white p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-semibold">Bukti Pembayaran</div>
                        <div class="text-sm text-slate-500">
                            {{ $activePayment->order->order_number }} · {{ $activePayment->customer->name }}
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="closeProof"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-sm font-semibold text-slate-600 hover:border-slate-300"
                    >
                        Tutup
                    </button>
                </div>

                <div class="mt-4">
                    @if ($proofPath)
                        @if ($isImage)
                            <img
                                src="{{ route('payments.proof', $activePayment) }}"
                                alt="Bukti"
                                class="max-h-[60vh] w-full rounded-lg object-contain"
                            />
                        @else
                            <a
                                href="{{ route('payments.proof', $activePayment) }}"
                                class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300"
                                target="_blank"
                            >
                                Buka File Bukti
                            </a>
                        @endif
                    @else
                        <div class="rounded-xl border border-dashed border-slate-200 px-4 py-6 text-sm text-slate-500">
                            Tidak ada bukti pembayaran yang diunggah.
                        </div>
                    @endif
                </div>

                <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-slate-500">
                        Total: Rp {{ number_format($activePayment->amount, 0, ',', '.') }}
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="approvePayment({{ $activePayment->id }})"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-base font-semibold text-white hover:bg-emerald-500"
                        >
                            Setujui
                        </button>
                        <button
                            type="button"
                            wire:click="startReject"
                            class="rounded-xl bg-rose-600 px-4 py-2 text-base font-semibold text-white hover:bg-rose-500"
                        >
                            Tolak
                        </button>
                    </div>
                </div>

                @if ($showRejectForm)
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4">
                        <label class="text-sm font-semibold text-rose-800">Alasan Penolakan</label>
                        <textarea
                            rows="3"
                            wire:model="rejectionNote"
                            class="mt-2 w-full rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm"
                        ></textarea>
                        @error('rejectionNote')
                            <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                type="button"
                                wire:click="rejectPayment({{ $activePayment->id }})"
                                class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500"
                            >
                                Kirim Penolakan
                            </button>
                            <button
                                type="button"
                                wire:click="cancelReject"
                                class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

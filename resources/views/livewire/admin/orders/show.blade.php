<div class="page-enter space-y-6">
    <div data-reveal>
        <h1 class="text-2xl font-bold">Detail Pesanan</h1>
        <p class="text-sm text-slate-500">Kontrol pesanan untuk customer {{ $order->customer->name }}.</p>
    </div>

    <div data-reveal data-reveal-delay="1" class="flex flex-wrap items-center gap-2">
        <div class="text-lg font-semibold text-slate-900">{{ $order->order_number }}</div>
        <x-status-badge :status="$order->status" />
        <x-status-badge type="payment_status" :status="$order->payment_status" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section data-reveal data-reveal-delay="2" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Informasi Pesanan</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-slate-500">Customer</div>
                        <div class="text-base font-semibold">{{ $order->customer->name }}</div>
                        <div class="text-sm text-slate-500">{{ $order->customer->email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Layanan</div>
                        <div class="text-base font-semibold">{{ $order->service->name }}</div>
                        <div class="text-sm text-slate-500">{{ strtoupper($order->service->type) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Estimasi Selesai</div>
                        <div class="text-base font-semibold">
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Estimasi Harga</div>
                        <div class="text-base font-semibold">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Appointment</div>
                        <div class="text-base font-semibold">
                            {{ $order->appointment?->appointment_date?->format('d M Y H:i') ?? '-' }}
                        </div>
                        @if ($order->appointment)
                            <x-status-badge type="appointment" :status="$order->appointment->status" />
                        @endif
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Jumlah Item</div>
                        <div class="text-base font-semibold">{{ $order->quantity }}</div>
                    </div>
                </div>
                @if ($order->notes)
                    <div class="mt-4 text-sm text-slate-600">
                        <div class="font-semibold text-slate-900">Catatan Customer</div>
                        <p class="mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
                @if ($order->fabric)
                    <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="text-sm font-semibold text-slate-900">Bahan Kain Dipilih</div>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="text-slate-500">Nama:</span>
                                <span class="font-medium text-slate-900">{{ $order->fabric->name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Warna:</span>
                                <span class="font-medium text-slate-900">{{ $order->fabric->color }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Kategori:</span>
                                <span class="font-medium text-slate-900">{{ $order->fabric->category_label }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">Harga:</span>
                                <span class="font-medium text-slate-900">Rp {{ number_format((float) $order->fabric->price_per_meter, 0, ',', '.') }}/m</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            @if ($order->fabric->stock_status === 'tersedia')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Tersedia</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">PO ~{{ $order->fabric->po_days }} hari</span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <section data-reveal data-reveal-delay="3" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Timeline Status</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($statusLogs as $log)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$log->status" />
                                <div class="text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</div>
                                <div class="text-xs text-slate-500">oleh {{ $log->user?->name ?? 'System' }}</div>
                            </div>
                            @if ($log->notes)
                                <div class="mt-2 text-sm text-slate-600">{{ $log->notes }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada riwayat status.</div>
                    @endforelse
                </div>
            </section>

            <section data-reveal data-reveal-delay="4" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Daftar Pembayaran</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($order->payments as $payment)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 p-3">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ strtoupper($payment->payment_type) }} - Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-500">Metode: {{ strtoupper($payment->payment_method) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge type="payment" :status="$payment->status" />
                                @if ($payment->proof_file_path)
                                    <a
                                        href="{{ route('payments.proof', $payment) }}"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-slate-300"
                                        target="_blank"
                                    >
                                        Lihat Bukti
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section data-reveal data-reveal-delay="2" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Update Status Bahan</h2>
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Sumber Bahan</label>
                        <select
                            wire:model="material_source"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-base"
                        >
                            <option value="">Pilih sumber</option>
                            <option value="customer">Customer</option>
                            <option value="jasa">Jasa</option>
                        </select>
                        @error('material_source')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Status Bahan</label>
                        <select
                            wire:model="material_status"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-base"
                        >
                            <option value="">Pilih status</option>
                            <option value="ready">Ready</option>
                            <option value="po">PO</option>
                        </select>
                        @error('material_status')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button
                        type="button"
                        wire:click="updateMaterial"
                        class="w-full rounded-xl bg-slate-900 px-4 py-2 text-base font-semibold text-white hover:bg-slate-800"
                    >
                        Simpan Status Bahan
                    </button>
                </div>
            </section>

            <section data-reveal data-reveal-delay="3" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Update Status Pesanan</h2>
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Status Pesanan</label>
                        <select
                            wire:model="status"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-base"
                        >
                            <option value="menunggu_appointment">Menunggu Appointment</option>
                            <option value="menunggu_bahan">Menunggu Bahan</option>
                            <option value="diproses">Diproses</option>
                            <option value="dijahit">Dijahit</option>
                            <option value="finishing">Finishing</option>
                            <option value="selesai">Selesai</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Catatan Admin</label>
                        <textarea
                            rows="3"
                            wire:model="notes"
                            class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-base"
                        ></textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if (! $this->canUpdateStatus)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <div class="font-semibold">Belum bisa diproses:</div>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($blockingReasons as $reason)
                                    <li>{{ $reason }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button
                        type="button"
                        wire:click="updateStatus"
                        @class([
                            'w-full rounded-xl px-4 py-2 text-base font-semibold',
                            'bg-slate-900 text-white hover:bg-slate-800' => $this->canUpdateStatus,
                            'bg-slate-300 text-slate-600 cursor-not-allowed' => ! $this->canUpdateStatus,
                        ])
                        @disabled(! $this->canUpdateStatus)
                    >
                        Simpan Status Pesanan
                    </button>
                </div>
            </section>

            <section data-reveal data-reveal-delay="4" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Preview Desain</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($order->designFiles as $file)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
                            <div class="text-sm text-slate-700">{{ $file->original_filename }}</div>
                            <button
                                type="button"
                                wire:click="previewDesign({{ $file->id }})"
                                class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-slate-300"
                            >
                                Lihat
                            </button>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada file desain.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    @if ($showDesignModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="modal-content-enter w-full max-w-2xl rounded-2xl bg-white p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $designPreviewName }}</h3>
                    <button
                        type="button"
                        wire:click="closeDesignPreview"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-sm font-semibold text-slate-600 hover:border-slate-300"
                    >
                        Tutup
                    </button>
                </div>
                <div class="mt-4">
                    <img src="{{ $designPreviewUrl }}" alt="Preview" class="max-h-[70vh] w-full rounded-lg object-contain" />
                </div>
            </div>
        </div>
    @endif
</div>

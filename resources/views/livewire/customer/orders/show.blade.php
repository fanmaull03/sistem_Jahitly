<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">No. Pesanan</p>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $order->order_number }}</h1>
            <p class="text-sm text-slate-600">{{ $order->service->name }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <x-status-badge :status="$order->status" />
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->paymentStatusClasses }}">
                {{ $this->paymentStatusLabel }}
            </span>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Progress Pesanan</h2>
            <span class="text-sm text-slate-500">{{ $progressPercent }}%</span>
        </div>
        <div class="mt-4 h-2 w-full rounded-full bg-slate-100">
            <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $progressPercent }}%"></div>
        </div>
        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($statusSteps as $index => $step)
                <div @class([
                    'rounded-lg border p-3',
                    'border-emerald-200 bg-emerald-50' => $index <= $currentStepIndex,
                    'border-slate-200 bg-white' => $index > $currentStepIndex,
                ])>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span class="uppercase tracking-wide">Step {{ $index + 1 }}</span>
                        @if ($index === $currentStepIndex)
                            <span class="font-semibold text-emerald-700">Aktif</span>
                        @elseif ($index < $currentStepIndex)
                            <span class="font-semibold text-emerald-700">Selesai</span>
                        @endif
                    </div>
                    <div class="mt-2 font-semibold text-slate-900">{{ $step['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Timeline Status</h2>
                <ol class="mt-4 space-y-4">
                    @forelse ($statusLogs as $log)
                        <li class="flex gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-slate-300"></div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-status-badge :status="$log->status" />
                                    <span class="text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                                @if ($log->notes)
                                    <p class="mt-1 text-sm text-slate-600">{{ $log->notes }}</p>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Belum ada riwayat status.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Detail Pesanan</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Estimasi selesai</span>
                        <span class="font-semibold text-slate-900">
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Jumlah item</span>
                        <span class="font-semibold text-slate-900">{{ $order->quantity }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Estimasi harga</span>
                        <span class="font-semibold text-slate-900">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Sumber bahan</span>
                        <span class="font-semibold text-slate-900">
                            {{ $order->material_source ? ucfirst($order->material_source) : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Status bahan</span>
                        <span class="font-semibold text-slate-900">
                            {{ $order->material_status ? strtoupper($order->material_status) : '-' }}
                        </span>
                    </div>
                </div>
                @if ($order->notes)
                    <div class="mt-4 text-sm text-slate-600">
                        <div class="font-semibold text-slate-900">Catatan</div>
                        <p class="mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">File Desain</h2>
                <div class="mt-4 space-y-2 text-sm">
                    @forelse ($order->designFiles as $file)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
                            <span class="text-slate-700">{{ $file->original_filename }}</span>
                            <span class="text-xs text-slate-400">Tersimpan</span>
                        </div>
                    @empty
                        <p class="text-slate-500">Belum ada file desain.</p>
                    @endforelse
                </div>
            </div>

            @if ($order->payment_status !== 'lunas')
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Pembayaran</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Status pembayaran saat ini: {{ $this->paymentStatusLabel }}.
                    </p>
                    @if ($hasPendingPayment)
                        <p class="mt-2 text-sm text-amber-600">
                            Ada pembayaran yang menunggu verifikasi admin.
                        </p>
                    @endif
                    <a
                        href="{{ route('orders.payments.create', $order) }}"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 {{ $hasPendingPayment ? 'pointer-events-none opacity-60' : '' }}"
                        wire:navigate
                    >
                        Lanjutkan ke Pembayaran
                    </a>
                </div>
            @endif
        </aside>
    </div>
</div>

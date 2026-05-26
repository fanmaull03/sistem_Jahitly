<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-24 sm:px-6 lg:pb-10">
    <!-- Header -->
    <div data-reveal class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 hover:text-stone-900 transition mb-3" wire:navigate>
                    &larr; Kembali ke Pesanan
                </a>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-900">Pesanan #{{ $order->order_number }}</h1>
                        <p class="mt-1 text-sm font-medium text-stone-600">{{ $order->service->name }} &bull; {{ $order->quantity }} Pcs</p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $paymentLabels = [
                        'belum_bayar' => 'Belum Lunas',
                        'menunggu' => 'Menunggu Verifikasi',
                        'dp' => 'DP Terbayar',
                        'lunas' => 'Lunas',
                    ];
                    $paymentClasses = [
                        'belum_bayar' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
                        'menunggu' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
                        'dp' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
                        'lunas' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                    ];
                @endphp
                
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $paymentClasses[$order->payment_status] ?? 'bg-stone-50 text-stone-700' }}">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ $paymentLabels[$order->payment_status] ?? 'Belum Lunas' }}
                </span>

                @if ($canCancel)
                    <a href="{{ route('orders.cancel', $order) }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 hover:bg-red-100 transition" wire:navigate>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Batalkan
                    </a>
                @endif
            </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Col: Tracking Stepper -->
        <div class="space-y-6 lg:col-span-2">
            <section data-reveal data-reveal-delay="1" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-stone-900">Status Pesanan</h2>
                    <span class="text-xs font-semibold uppercase tracking-wide text-stone-400">Self-Tracking</span>
                </div>
                
                <div class="mt-8 flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach ($statusSteps as $index => $step)
                            @php
                                $isCompleted = $index < $currentStepIndex;
                                $isActive = $index === $currentStepIndex;
                                $isLast = $loop->last;
                            @endphp
                            <li>
                                <div class="relative pb-8">
                                    @if (!$isLast)
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 {{ $isCompleted ? 'bg-emerald-500' : 'bg-stone-200' }}" aria-hidden="true"></span>
                                    @endif
                                    
                                    <div class="relative flex space-x-4">
                                        <div>
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full ring-4 ring-white {{ $isActive ? 'bg-blue-600' : ($isCompleted ? 'bg-emerald-500' : 'bg-stone-200') }}">
                                                @if ($isCompleted)
                                                    <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                    </svg>
                                                @elseif ($isActive)
                                                    <span class="h-2.5 w-2.5 rounded-full bg-white"></span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm font-semibold {{ $isActive || $isCompleted ? 'text-stone-900' : 'text-stone-500' }}">
                                                    {{ $step['label'] }}
                                                </p>
                                                
                                                <!-- Find matching log for this status to show date -->
                                                @php
                                                    $matchedLog = $statusLogs->firstWhere('status', $step['key']);
                                                @endphp
                                                
                                                @if ($matchedLog)
                                                    <p class="mt-1 flex items-center gap-2 text-xs text-stone-500">
                                                        <svg class="h-3.5 w-3.5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $matchedLog->created_at->format('d M Y, H:i') }}
                                                    </p>
                                                    @if ($matchedLog->notes)
                                                        <p class="mt-2 rounded-lg bg-stone-50 p-3 text-sm italic text-stone-600">
                                                            "{{ $matchedLog->notes }}"
                                                        </p>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        <!-- Right Col: Order Details -->
        <aside class="space-y-6">
            <div data-reveal data-reveal-delay="2" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-stone-900">Detail Pesanan</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between border-b border-stone-100 pb-3">
                        <span class="font-medium text-stone-500">Estimasi Selesai</span>
                        <span class="font-bold text-stone-900">
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : 'Menunggu Penjadwalan' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-stone-100 pb-3">
                        <span class="font-medium text-stone-500">Total Harga</span>
                        <span class="font-bold text-stone-900">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-stone-100 pb-3">
                        <span class="font-medium text-stone-500">Sumber Bahan</span>
                        <span class="font-bold text-stone-900">
                            {{ $order->material_source === 'customer' ? 'Dari Pelanggan' : ($order->material_source === 'jasa' ? 'Dari Jahitly' : '-') }}
                        </span>
                    </div>
                    <div class="flex justify-between pb-1">
                        <span class="font-medium text-stone-500">Status Bahan</span>
                        <span class="font-bold text-stone-900">
                            {{ $order->material_status ? strtoupper($order->material_status) : '-' }}
                        </span>
                    </div>
                </div>

                @if ($order->notes)
                    <div class="mt-4 rounded-xl bg-stone-50 p-4 text-sm text-stone-700">
                        <div class="font-bold text-stone-900">Catatan Khusus:</div>
                        <p class="mt-1">{{ $order->notes }}</p>
                    </div>
                @endif

                <!-- Payment History & Actions -->
                <div class="mt-4 space-y-2">
                    <a
                        href="{{ route('payments.history.order', $order) }}"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-stone-300 px-4 py-3 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50 transition"
                        wire:navigate
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat Pembayaran
                    </a>
                </div>
            </div>

            @if ($order->designFiles->isNotEmpty())
                <div data-reveal data-reveal-delay="3" class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-stone-900">File Desain</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($order->designFiles as $file)
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="group flex items-center justify-between rounded-xl border border-stone-200 p-3 transition hover:border-blue-300 hover:bg-blue-50">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-stone-100 text-stone-500 group-hover:bg-blue-100 group-hover:text-blue-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="truncate text-sm font-semibold text-stone-900">{{ $file->original_filename }}</p>
                                        <p class="text-xs font-medium text-stone-500 group-hover:text-blue-600">Lihat Gambar &rarr;</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>

    <!-- Floating Payment Action Button (Only if not fully paid) -->
    @if ($order->payment_status !== 'lunas')
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white p-4 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] lg:sticky lg:bottom-6 lg:mt-6 lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-center sm:text-left">
                    <p class="text-sm font-semibold text-stone-900">Tagihan Pembayaran</p>
                    <p class="mt-1 text-xs font-medium text-stone-500">
                        @if ($hasPendingPayment)
                            Ada pembayaran yang sedang menunggu verifikasi admin.
                        @else
                            Silakan lakukan pembayaran agar pesanan bisa segera diproses.
                        @endif
                    </p>
                </div>
                
                <a
                    href="{{ route('payments.create', $order) }}"
                    class="w-full rounded-xl bg-blue-600 px-6 py-3 text-center text-sm font-bold text-white transition hover:bg-blue-700 sm:w-auto {{ $hasPendingPayment ? 'pointer-events-none opacity-50 grayscale' : 'hover-lift' }}"
                    wire:navigate
                >
                    Bayar Sekarang
                </a>
            </div>
        </div>
    @endif
</div>

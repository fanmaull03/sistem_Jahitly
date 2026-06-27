<div class="page-enter mx-auto max-w-5xl space-y-6 px-4 pb-20 sm:px-6">
    {{-- ── Header ── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900 dark:text-stone-100">Pesanan Saya</h1>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Pantau status jahitan Anda secara real-time.</p>
        </div>
        <a href="{{ route('orders.create') }}"
           class="group inline-flex items-center justify-center gap-2 rounded-lg bg-[#003399] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-blue-800 hover:shadow-lg sm:w-auto hover-lift"
           wire:navigate>
            <svg class="h-4 w-4 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Pesanan Baru
        </a>
    </div>

    {{-- ── Filter Tabs ── --}}
    <div class="flex gap-3">
        @foreach ($statuses as $key => $label)
            <button
                type="button"
                wire:click="$set('statusFilter', '{{ $key }}')"
                @class([
                    'rounded-full px-5 py-1.5 text-sm font-semibold transition-all duration-200',
                    'bg-[#F8A01A] text-stone-900 shadow-sm' => $statusFilter === $key,
                    'bg-stone-200 text-stone-500 hover:bg-stone-300 hover:text-stone-700 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-300' => $statusFilter !== $key,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── Order Cards ── --}}
    <div class="space-y-4">
        @php
            function getOrderStatusBadge($status) {
                return match(true) {
                    in_array($status, ['menunggu_appointment', 'menunggu_bahan']) => [
                        'label' => 'Menunggu',
                        'class' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-500/30 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-500/20',
                        'dot'   => 'bg-amber-500',
                    ],
                    in_array($status, ['diproses', 'dijahit']) => [
                        'label' => 'Sedang Dijahit',
                        'class' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-500/30 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-500/20',
                        'dot'   => 'bg-blue-500',
                    ],
                    $status === 'finishing' => [
                        'label' => 'Finishing',
                        'class' => 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-500/30 dark:bg-purple-900/20 dark:text-purple-400 dark:ring-purple-500/20',
                        'dot'   => 'bg-purple-500',
                    ],
                    $status === 'selesai' => [
                        'label' => 'Selesai & Diambil',
                        'class' => 'bg-stone-100 text-stone-600 ring-1 ring-inset ring-stone-200 dark:bg-stone-800 dark:text-stone-400 dark:ring-stone-700',
                        'dot'   => 'bg-stone-400',
                    ],
                    default => [
                        'label' => ucwords(str_replace('_', ' ', $status)),
                        'class' => 'bg-stone-50 text-stone-600 ring-1 ring-inset ring-stone-300/50 dark:bg-stone-800/50 dark:text-stone-400 dark:ring-stone-700/50',
                        'dot'   => 'bg-stone-400',
                    ],
                };
            }

            function getOrderBarColor($status) {
                return match(true) {
                    $status === 'selesai' => 'bg-stone-200',
                    default => 'bg-[#F8A01A]',
                };
            }
        @endphp

        @forelse ($orders as $order)
            @php
                $badge = getOrderStatusBadge($order->status);
                $barGradient = getOrderBarColor($order->status);
                $isFinished = $order->status === 'selesai';
            @endphp

            <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-stone-200 transition-all duration-300 hover:shadow-md hover:ring-stone-300 dark:bg-stone-800 dark:ring-stone-700 dark:hover:ring-stone-600">
                {{-- Left bar --}}
                <div class="absolute inset-y-0 left-0 w-1.5 {{ $barGradient }}" aria-hidden="true"></div>

                <div class="flex flex-col md:flex-row">
                    {{-- Main content --}}
                    <a href="{{ route('orders.show', $order) }}" wire:navigate class="flex flex-1 gap-4 py-5 pl-6 pr-5">
                        {{-- Icon --}}
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500 transition group-hover:bg-stone-200 dark:bg-stone-700 dark:text-stone-400 dark:group-hover:bg-stone-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>

                        {{-- Text --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="text-[13px] font-bold text-stone-800 dark:text-stone-200">#{{ $order->order_number }}</span>
                                <span class="text-[10px] text-stone-300 dark:text-stone-600">•</span>
                                <span class="text-[13px] font-medium text-stone-600 dark:text-stone-400">{{ $order->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-stone-300 dark:text-stone-600">•</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $badge['class'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                            <p class="mt-2 text-[17px] font-bold text-stone-900 dark:text-stone-100">{{ $order->service->name }}</p>
                            @if ($order->notes)
                                <p class="mt-0.5 truncate text-sm text-stone-500 dark:text-stone-400">{{ \Illuminate\Support\Str::limit($order->notes, 80) }}</p>
                            @endif
                        </div>
                    </a>

                    {{-- Right panel --}}
                    <div class="flex flex-shrink-0 flex-col justify-center border-t border-stone-100 bg-white px-6 py-4 md:w-56 md:border-l md:border-t-0 md:items-end md:text-right dark:border-stone-700 dark:bg-stone-800/50">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-stone-400 dark:text-stone-500">Total Biaya</div>
                        <div class="mt-0.5 text-lg font-bold {{ $isFinished ? 'text-stone-700 dark:text-stone-300' : 'text-[#0044CC] dark:text-blue-400' }}">Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}</div>

                        @if ($isFinished)
                            <div class="mt-3 text-[11px] text-stone-400 dark:text-stone-500">Diambil Pada</div>
                            <div class="text-xs font-semibold text-stone-700 dark:text-stone-300">{{ $order->updated_at->format('d M Y') }}</div>
                            <div class="mt-3 text-right">
                                <a href="{{ route('payments.history.order', $order) }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 transition hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Lihat Nota
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </a>
                            </div>
                        @else
                            <div class="mt-3 text-[11px] text-stone-400 dark:text-stone-500">Estimasi Selesai</div>
                            <div class="text-xs font-semibold text-stone-700 dark:text-stone-300">{{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}</div>
                            <div class="mt-3 text-right">
                                <a href="{{ route('orders.show', $order) }}" wire:navigate class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 transition hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Lihat Detail & Lacak
                                    <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-stone-200 bg-stone-50 p-12 text-center dark:border-stone-700 dark:bg-stone-800/50">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-stone-200 text-stone-500 dark:bg-stone-700 dark:text-stone-400">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-stone-900 dark:text-stone-100">Belum ada pesanan</h3>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Anda belum pernah membuat pesanan jahitan. Mulai pesanan pertama Anda sekarang!</p>
                <a href="{{ route('orders.create') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700" wire:navigate>
                    Buat Pesanan
                </a>
            </div>
        @endforelse
    </div>

    @if ($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<div class="page-enter mx-auto max-w-5xl space-y-6 px-4 pb-20 sm:px-6">
    {{-- ── Header ── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Pesanan Saya</p>
            <h1 class="mt-0.5 font-display text-3xl font-bold text-ink dark:text-stone-100">Jahitan Saya</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">Pantau status jahitan Anda secara real-time.</p>
        </div>
        <a href="{{ route('orders.create') }}" wire:navigate
           class="group inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/30 sm:w-auto">
            <svg class="h-4 w-4 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Pesanan Baru
        </a>
    </div>

    {{-- ── Filter Tabs ── --}}
    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach ($statuses as $key => $label)
            <button
                type="button"
                wire:click="$set('statusFilter', '{{ $key }}')"
                @class([
                    'shrink-0 rounded-full px-4 py-1.5 text-sm font-semibold transition-all duration-200',
                    'bg-primary text-white shadow-sm shadow-primary/25' => $statusFilter === $key,
                    'bg-surface text-muted hover:bg-border hover:text-ink dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700' => $statusFilter !== $key,
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
                    default => 'bg-primary',
                };
            }
        @endphp

        @forelse ($orders as $order)
            @php
                $badge = getOrderStatusBadge($order->status);
                $barGradient = getOrderBarColor($order->status);
                $isFinished = $order->status === 'selesai';
            @endphp

            <div class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-border transition-all duration-300 hover:shadow-md hover:ring-primary/20 dark:bg-stone-800 dark:ring-stone-700">
                {{-- Left bar --}}
                <div class="absolute inset-y-0 left-0 w-1.5 {{ $barGradient }}" aria-hidden="true"></div>

                <div class="flex flex-col md:flex-row">
                    {{-- Main area - link ke detail --}}
                    <a href="{{ route('orders.show', $order) }}" wire:navigate
                       class="flex flex-1 gap-4 py-5 pl-6 pr-5">
                      
                      {{-- Service icon --}}
                      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white dark:bg-primary/20">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>

                        {{-- Text content --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="font-mono text-xs font-bold text-ink dark:text-stone-200">#{{ $order->order_number }}</span>
                                <span class="text-border">·</span>
                                <span class="text-xs text-muted dark:text-stone-400">{{ $order->created_at->format('d M Y') }}</span>
                                <span class="text-border">·</span>
                                {{-- Badge status --}}
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badge['class'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                            <p class="mt-2 text-base font-bold text-ink dark:text-stone-100">{{ $order->service->name }}</p>
                            @if ($order->notes)
                                <p class="mt-0.5 truncate text-sm text-muted dark:text-stone-400">
                                    {{ \Illuminate\Support\Str::limit($order->notes, 80) }}
                                </p>
                            @endif
                        </div>
                    </a>

                    {{-- Right panel --}}
                    <div class="flex shrink-0 flex-col justify-center border-t border-border bg-surface/50 px-6 py-4 md:w-52 md:border-l md:border-t-0 md:items-end md:text-right dark:border-stone-700 dark:bg-stone-800/50">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Total Biaya</p>
                        <p class="mt-0.5 text-lg font-extrabold {{ $isFinished ? 'text-ink dark:text-stone-300' : 'text-primary dark:text-blue-400' }}">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </p>

                        @if ($isFinished)
                            <p class="mt-2 text-[10px] text-muted dark:text-stone-500">Diambil Pada</p>
                            <p class="text-xs font-semibold text-ink dark:text-stone-300">{{ $order->updated_at->format('d M Y') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('payments.history.order', $order) }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-hover transition dark:text-blue-400">
                                    Lihat Nota →
                                </a>
                            </div>
                        @else
                            <p class="mt-2 text-[10px] text-muted dark:text-stone-500">Est. Selesai</p>
                            <p class="text-xs font-semibold text-ink dark:text-stone-300">
                                {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '—' }}
                            </p>
                            <div class="mt-3">
                                <a href="{{ route('orders.show', $order) }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-hover transition dark:text-blue-400">
                                    Detail & Lacak →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-border bg-surface py-16 text-center dark:border-stone-700 dark:bg-stone-800/50">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10">
                    <svg class="h-10 w-10 text-primary/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-ink dark:text-stone-100">Belum ada pesanan</h3>
                <p class="mt-2 max-w-xs text-sm text-muted dark:text-stone-400">
                    Anda belum pernah membuat pesanan jahitan. Mulai pesanan pertama Anda sekarang!
                </p>
                <a href="{{ route('orders.create') }}" wire:navigate
                   class="mt-6 inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover">
                    Buat Pesanan Pertama
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

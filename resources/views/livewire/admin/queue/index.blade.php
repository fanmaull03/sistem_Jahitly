<div class="page-enter space-y-6">
    <x-slot name="header">Antrian Produksi</x-slot>
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Operasional</p>
        <h1 class="mt-0.5 text-2xl font-bold text-ink dark:text-stone-100">Antrian Produksi</h1>
        <p class="text-sm text-muted dark:text-stone-400">Atur prioritas pesanan yang sedang dikerjakan.</p>
    </div>

    @php
        $statusLabels = [
            'diproses' => 'Sedang Diproses',
            'dijahit' => 'Sedang Dijahit',
            'finishing' => 'Finishing',
        ];
    @endphp

    <div class="space-y-8">
        @foreach ($queueGroups as $status => $orders)
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-border pb-2 dark:border-stone-700">
                    <div class="h-2 w-2 rounded-full bg-primary"></div>
                    <h2 class="text-base font-bold text-ink dark:text-stone-100">{{ $statusLabels[$status] ?? ucfirst($status) }}</h2>
                    <span class="rounded-full bg-surface px-2.5 py-0.5 text-xs font-bold text-muted dark:bg-stone-700 dark:text-stone-300">
                        {{ count($orders) }}
                    </span>
                </div>

                <div class="grid gap-3">
                    @forelse ($orders as $order)
                        @php
                            $dueDate = $order->estimated_finish_date;
                            $isOverdue = $dueDate && $dueDate->isPast();
                            $isNear = $dueDate && ! $isOverdue && $dueDate->isBefore(now()->addDays(2));
                            
                            $urgencyColor = $isOverdue ? 'border-l-rose-500 bg-rose-50/50' 
                                          : ($isNear ? 'border-l-accent bg-accent/5' 
                                          : 'border-l-primary bg-white');
                                          
                            $badgeColor = $isOverdue ? 'bg-rose-100 text-rose-700'
                                        : ($isNear ? 'bg-accent/20 text-accent' 
                                        : 'bg-surface text-muted');
                        @endphp

                        <div class="group flex flex-col gap-4 rounded-xl border border-border border-l-4 {{ $urgencyColor }} p-4 shadow-sm transition hover:shadow-md sm:flex-row sm:items-center sm:justify-between dark:border-stone-700 dark:bg-stone-800">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-bold text-ink dark:text-stone-100">{{ $order->order_number }}</span>
                                    <span class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $badgeColor }}">
                                        Estimasi: {{ $dueDate ? $dueDate->format('d M') : '-' }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm font-semibold text-ink dark:text-stone-200">
                                    {{ $order->customer->name }}
                                </div>
                                <div class="text-xs text-muted dark:text-stone-400">
                                    {{ $order->service->name }} · {{ $order->quantity }} item
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="moveUp({{ $order->id }})" title="Naikkan Prioritas"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-muted transition hover:border-primary hover:text-primary hover:shadow-sm dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:border-primary">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="moveDown({{ $order->id }})" title="Turunkan Prioritas"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-border bg-white text-muted transition hover:border-primary hover:text-primary hover:shadow-sm dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:border-primary">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <a href="{{ route('admin.orders.show', $order) }}" wire:navigate
                                   class="flex h-10 items-center justify-center rounded-xl bg-surface px-4 text-xs font-bold text-ink transition hover:bg-border dark:bg-stone-700 dark:text-stone-200">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-border p-8 text-center dark:border-stone-700">
                            <p class="text-sm font-medium text-muted">Antrian kosong untuk tahap ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>

<div class="page-enter space-y-6">
    <div data-reveal>
        <h1 class="text-2xl font-bold">Antrian Produksi</h1>
        <p class="text-sm text-slate-500">Urutkan pesanan yang sedang dikerjakan.</p>
    </div>

    @php
        $statusLabels = [
            'diproses' => 'Sedang Diproses',
            'dijahit' => 'Sedang Dijahit',
            'finishing' => 'Finishing',
        ];
    @endphp

    <div class="space-y-6">
        @foreach ($queueGroups as $status => $orders)
            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-slate-800">{{ $statusLabels[$status] ?? ucfirst($status) }}</h2>

                @forelse ($orders as $order)
                    @php
                        $dueDate = $order->estimated_finish_date;
                        $isOverdue = $dueDate && $dueDate->isPast();
                        $isNear = $dueDate && ! $isOverdue && $dueDate->isBefore(now()->addDays(2));
                        $dueClass = $isOverdue
                            ? 'bg-rose-100 text-rose-700'
                            : ($isNear ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700');
                    @endphp

                    <div class="hover-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-base font-semibold text-slate-900">{{ $order->order_number }}</div>
                                <div class="text-sm text-slate-500">{{ $order->customer->name }} - {{ $order->service->name }}</div>
                                <div class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $dueClass }}">
                                    Estimasi: {{ $dueDate ? $dueDate->format('d M Y') : '-' }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="moveUp({{ $order->id }})"
                                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300"
                                >
                                    ▲ Naikkan
                                </button>
                                <button
                                    type="button"
                                    wire:click="moveDown({{ $order->id }})"
                                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300"
                                >
                                    ▼ Turunkan
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-6 text-sm text-slate-500">
                        Tidak ada pesanan pada status ini.
                    </div>
                @endforelse
            </section>
        @endforeach
    </div>
</div>

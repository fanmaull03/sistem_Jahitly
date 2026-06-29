<div class="page-enter space-y-6">
    <x-slot name="header">Kelola Pesanan</x-slot>
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Manajemen</p>
        <h1 class="mt-0.5 text-2xl font-bold text-ink dark:text-stone-100">Pesanan</h1>
        <p class="text-sm text-muted dark:text-stone-400">Cari dan filter pesanan dengan cepat.</p>
    </div>

    <div class="rounded-2xl border border-border bg-white p-4 shadow-sm dark:border-stone-700 dark:bg-stone-800">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Cari Pesanan</label>
                <div class="relative mt-1.5">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live="search" type="text" placeholder="Nomor pesanan atau nama customer"
                        class="w-full rounded-xl border border-border bg-surface pl-9 pr-4 py-2.5 text-sm text-ink 
                               focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 
                               dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100" />
                </div>
            </div>
            <div>
                <label class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Status</label>
                <select wire:model.live="statusFilter"
                    class="mt-1.5 w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-sm text-ink 
                           focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                           dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100">
                    @foreach ($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Jenis Layanan</label>
                <select wire:model.live="serviceTypeFilter"
                    class="mt-1.5 w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-sm text-ink 
                           focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                           dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100">
                    @foreach ($serviceTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface dark:border-stone-700 dark:bg-stone-800/60">
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">No. Pesanan</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Customer</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Layanan</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Status</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Bahan</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Est. Selesai</th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-400">Bayar</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border dark:divide-stone-700">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-primary/5 dark:hover:bg-primary/10">
                            <td class="px-4 py-4 font-mono text-sm font-bold text-ink dark:text-stone-100">{{ $order->order_number }}</td>
                            <td class="px-4 py-4 text-sm font-medium text-ink dark:text-stone-200">{{ $order->customer->name }}</td>
                            <td class="px-4 py-4 text-sm text-muted dark:text-stone-400">{{ $order->service->name }}</td>
                            <td class="px-4 py-4"><x-status-badge :status="$order->status" /></td>
                            <td class="px-4 py-4">
                                @if ($order->material_status)
                                    <x-status-badge type="material" :status="$order->material_status" />
                                @else
                                    <span class="text-sm text-muted/50">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-ink dark:text-stone-300">
                                {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-4 py-4"><x-status-badge type="payment_status" :status="$order->payment_status" /></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($order->status === 'menunggu_konfirmasi')
                                        <button type="button" wire:click="quickAccept({{ $order->id }})"
                                            class="rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white transition hover:bg-primary-hover shadow-sm">
                                            Terima
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.orders.show', $order) }}" wire:navigate
                                        class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-ink transition hover:border-primary hover:text-primary dark:border-stone-600 dark:text-stone-300">
                                        Detail →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface">
                                        <svg class="h-8 w-8 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-muted">Tidak ada pesanan yang sesuai filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-border px-4 py-3 dark:border-stone-700">
            {{ $orders->links() }}
        </div>
    </div>
</div>

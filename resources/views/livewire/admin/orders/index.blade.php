<div class="page-enter space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Manajemen Pesanan</h1>
        <p class="text-sm text-slate-500 dark:text-stone-400">Cari dan filter pesanan dengan cepat.</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-stone-300">Cari Pesanan</label>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Nomor pesanan atau nama customer"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-base focus:border-slate-400 focus:outline-none"
            />
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-stone-300">Status Pesanan</label>
            <select
                wire:model.live="statusFilter"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-base focus:border-slate-400 focus:outline-none"
            >
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700 dark:text-stone-300">Jenis Layanan</label>
            <select
                wire:model.live="serviceTypeFilter"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-base focus:border-slate-400 focus:outline-none"
            >
                @foreach ($serviceTypes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 dark:bg-stone-700/50 dark:text-stone-400">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. Pesanan</th>
                        <th class="px-4 py-3 text-left font-semibold">Customer</th>
                        <th class="px-4 py-3 text-left font-semibold">Layanan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Bahan</th>
                        <th class="px-4 py-3 text-left font-semibold">Estimasi Selesai</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Bayar</th>
                        <th class="px-4 py-3 text-left font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-stone-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-stone-700/50">
                            <td class="px-4 py-4 font-semibold text-slate-900 dark:text-stone-100">{{ $order->order_number }}</td>
                            <td class="px-4 py-4 text-slate-700 dark:text-stone-300">{{ $order->customer->name }}</td>
                            <td class="px-4 py-4 text-slate-700 dark:text-stone-300">{{ $order->service->name }}</td>
                            <td class="px-4 py-4">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td class="px-4 py-4">
                                @if ($order->material_status)
                                    <x-status-badge type="material" :status="$order->material_status" />
                                @else
                                    <span class="text-sm text-slate-400 dark:text-stone-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-700 dark:text-stone-300">
                                {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <x-status-badge type="payment_status" :status="$order->payment_status" />
                            </td>
                            <td class="px-4 py-4 text-right flex gap-2 justify-end">
                                @if ($order->status === 'menunggu_konfirmasi')
                                    <button
                                        type="button"
                                        wire:click="quickAccept({{ $order->id }})"
                                        class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-stone-600 dark:hover:bg-stone-500"
                                    >
                                        Terima
                                    </button>
                                @endif
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300 dark:border-stone-600 dark:text-stone-300 dark:hover:border-stone-500"
                                    wire:navigate
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-stone-400">
                                Tidak ada pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3 dark:border-stone-700">
            {{ $orders->links() }}
        </div>
    </div>
</div>

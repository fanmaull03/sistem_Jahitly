<div class="page-enter space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Manajemen Pesanan</h1>
        <p class="text-sm text-slate-500">Cari dan filter pesanan dengan cepat.</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label class="text-sm font-semibold text-slate-700">Cari Pesanan</label>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Nomor pesanan atau nama customer"
                class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-base focus:border-slate-400 focus:outline-none"
            />
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Status Pesanan</label>
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
            <label class="text-sm font-semibold text-slate-700">Jenis Layanan</label>
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

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
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
                <tbody class="divide-y divide-slate-200">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $order->customer->name }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $order->service->name }}</td>
                            <td class="px-4 py-4">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td class="px-4 py-4">
                                @if ($order->material_status)
                                    <x-status-badge type="material" :status="$order->material_status" />
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-700">
                                {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <x-status-badge type="payment_status" :status="$order->payment_status" />
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300"
                                    wire:navigate
                                >
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                Tidak ada pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>

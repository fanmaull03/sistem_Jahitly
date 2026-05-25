<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pesanan Saya</h1>
            <p class="text-sm text-slate-600">Pantau status pesanan jahit Anda secara real time.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800" wire:navigate>
            Buat Pesanan Baru
        </a>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach ($statuses as $key => $label)
            <button
                type="button"
                wire:click="$set('statusFilter', '{{ $key }}')"
                @class([
                    'rounded-full px-3 py-1 text-xs font-semibold transition',
                    'bg-slate-900 text-white' => $statusFilter === $key,
                    'bg-white text-slate-700 border border-slate-200 hover:border-slate-300' => $statusFilter !== $key,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">No. Pesanan</th>
                        <th class="px-4 py-3 text-left font-semibold">Layanan</th>
                        <th class="px-4 py-3 text-left font-semibold">Estimasi Selesai</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Pesanan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status Pembayaran</th>
                        <th class="px-4 py-3 text-left font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php
                        $paymentLabels = [
                            'belum_bayar' => 'Belum Bayar',
                            'menunggu' => 'Menunggu Verifikasi',
                            'dp' => 'DP Terverifikasi',
                            'lunas' => 'Lunas',
                        ];
                        $paymentClasses = [
                            'belum_bayar' => 'bg-slate-100 text-slate-700',
                            'menunggu' => 'bg-amber-100 text-amber-800',
                            'dp' => 'bg-sky-100 text-sky-800',
                            'lunas' => 'bg-emerald-100 text-emerald-800',
                        ];
                    @endphp

                    @forelse ($orders as $order)
                        @php
                            $paymentStatus = $order->payment_status;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4 font-semibold text-slate-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-4 text-slate-700">{{ $order->service->name }}</td>
                            <td class="px-4 py-4 text-slate-700">
                                {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-4">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentClasses[$paymentStatus] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $paymentLabels[$paymentStatus] ?? 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900" wire:navigate>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada pesanan. Mulai pesanan pertama Anda sekarang.
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

<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-20 sm:px-6">
    <div data-reveal class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Pesanan Saya</h1>
            <p class="mt-1 text-sm text-stone-600">Pantau status pesanan jahit Anda secara real-time.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto hover-lift" wire:navigate>
            Buat Pesanan Baru
        </a>
    </div>

    <!-- Filter Buttons -->
    <div data-reveal data-reveal-delay="1" class="flex snap-x snap-mandatory overflow-x-auto pb-2 scrollbar-hide sm:flex-wrap">
        <div class="flex gap-2 px-1">
            @foreach ($statuses as $key => $label)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $key }}')"
                    @class([
                        'snap-start whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition',
                        'bg-stone-900 text-white shadow-sm' => $statusFilter === $key,
                        'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => $statusFilter !== $key,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Order List (Cards) -->
    <div data-reveal data-reveal-delay="2" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $paymentLabels = [
                true => 'Lunas',
                false => 'Belum Lunas',
            ];
            $paymentClasses = [
                true => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                false => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
            ];

            // Map status to earthy colors (Yellow, Blue, Green)
            function getStatusColor($status) {
                if (in_array($status, ['menunggu_appointment', 'menunggu_bahan'])) return 'bg-amber-100 text-amber-800';
                if (in_array($status, ['diproses', 'dijahit', 'finishing'])) return 'bg-blue-100 text-blue-800';
                if ($status === 'selesai') return 'bg-emerald-100 text-emerald-800';
                return 'bg-stone-100 text-stone-800';
            }
        @endphp

        @forelse ($orders as $order)
            @php
                $isPaid = $order->payment_status === 'lunas';
            @endphp
            <a href="{{ route('orders.show', $order) }}" wire:navigate class="group relative block overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-stone-200 transition hover:ring-stone-300 hover-lift focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-stone-900">{{ $order->order_number }}</span>
                        </div>
                        <p class="mt-1 text-sm font-medium text-stone-500">{{ $order->service->name }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ getStatusColor($order->status) }}">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 border-t border-stone-100 pt-4">
                    <div>
                        <p class="text-xs font-medium text-stone-500">Estimasi Selesai</p>
                        <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-stone-900">
                            <svg class="h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : 'Menunggu' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-stone-500">Pembayaran</p>
                        <p class="mt-1">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold {{ $paymentClasses[$isPaid] }}">
                                {{ $paymentLabels[$isPaid] }}
                            </span>
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-stone-200 bg-stone-50 p-12 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-stone-200 text-stone-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-stone-900">Belum ada pesanan</h3>
                <p class="mt-2 text-sm text-stone-600">Anda belum pernah membuat pesanan jahitan. Mulai pesanan pertama Anda sekarang!</p>
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

<x-slot name="header">
    Dashboard
</x-slot>

<x-slot name="actions">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="relative text-stone-900">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="text" name="search" placeholder="Cari pesanan..." class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-full border border-stone-200 bg-stone-50 text-sm focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400 transition-colors">
    </form>
</x-slot>

<div class="page-enter space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-stone-900">Halo, Penjahit!</h1>
        <p class="text-sm text-stone-600">Berikut ringkasan pekerjaan Anda hari ini.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-summary-card
            title="Pesanan Aktif"
            :value="$summary['active_orders']"
            helper="Belum selesai"
            accent="blue"
        />
        <x-summary-card
            title="Menunggu Verifikasi"
            :value="$summary['pending_payments']"
            helper="Pembayaran pending"
            accent="amber"
        />
        <x-summary-card
            title="Jadwal Fitting Hari Ini"
            :value="$summary['today_appointments']"
            helper="Jadwal hari ini"
            accent="violet"
        />
        <x-summary-card
            title="Estimasi Pendapatan"
            :value="'Rp ' . number_format($summary['monthly_revenue'], 0, ',', '.')"
            helper="Bulan ini"
            accent="emerald"
        />
    </div>

    <!-- Daftar Fitting Hari Ini -->
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-stone-900">Jadwal Fitting Hari Ini</h2>
                <p class="text-sm text-stone-500">Daftar pelanggan yang memiliki janji temu fitting hari ini.</p>
            </div>
            <a href="{{ route('admin.appointments.index') }}" class="text-sm font-semibold text-[#003399] transition hover:text-blue-800" wire:navigate>
                Kelola Jadwal &rarr;
            </a>
        </div>
        
        @if($todayAppointments->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-stone-300 bg-stone-50 py-10 text-center">
                <svg class="h-12 w-12 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <h3 class="mt-4 text-sm font-bold text-stone-900">Tidak ada jadwal fitting hari ini</h3>
                <p class="mt-1 text-sm text-stone-500">Waktu luang! Belum ada pelanggan yang dijadwalkan untuk fitting hari ini.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-stone-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-stone-600">
                        <thead class="bg-stone-50 text-xs uppercase text-stone-500 border-b border-stone-200">
                            <tr>
                                <th scope="col" class="px-5 py-4 font-semibold">Jam</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Pelanggan & Pesanan</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Layanan</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Status</th>
                                <th scope="col" class="px-5 py-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach($todayAppointments as $appointment)
                                <tr class="transition hover:bg-stone-50">
                                    <td class="whitespace-nowrap px-5 py-4 font-bold text-stone-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-stone-900">{{ $appointment->customer->name ?? '-' }}</div>
                                        <div class="text-xs font-medium text-stone-500">{{ $appointment->order->order_number }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-md bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">
                                            {{ $appointment->order->service->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($appointment->status === 'menunggu')
                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Menunggu</span>
                                        @elseif ($appointment->status === 'selesai')
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Selesai</span>
                                        @elseif ($appointment->status === 'dibatalkan')
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-800">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $appointment->order) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-stone-700 shadow-sm ring-1 ring-inset ring-stone-300 transition hover:bg-stone-50" wire:navigate>
                                            Detail Pesanan
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

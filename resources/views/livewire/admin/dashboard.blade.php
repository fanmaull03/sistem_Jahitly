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
    <div data-reveal>
        <h1 class="text-2xl font-bold text-stone-900">Halo, Penjahit!</h1>
        <p class="text-sm text-stone-600">Berikut ringkasan pekerjaan Anda hari ini.</p>
    </div>

    <div data-reveal class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
</div>

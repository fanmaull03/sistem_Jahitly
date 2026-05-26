<x-slot name="header">
    Dashboard
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

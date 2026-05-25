<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Dashboard Admin</h1>
        <p class="text-sm text-slate-500">Ringkasan pekerjaan harian dan transaksi.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
            title="Appointment Hari Ini"
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

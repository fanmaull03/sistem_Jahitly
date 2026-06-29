<x-slot name="header">Dashboard</x-slot>



<x-slot name="actions">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="text" name="search" placeholder="Cari pesanan..." 
               class="w-full sm:w-64 pl-9 pr-4 py-2.5 rounded-xl border border-border bg-white text-sm text-ink shadow-sm 
                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
    </form>
</x-slot>

<div class="page-enter space-y-6 mt-6">
    <div class="mb-8 mt-2">
        <p class="text-sm font-bold uppercase tracking-widest text-primary/80 mb-1">Ringkasan</p>
        <h1 class="text-3xl font-display font-bold text-ink dark:text-stone-100">Dashboard</h1>
        <p class="mt-2 text-base text-muted dark:text-stone-400">Ikhtisar bisnis Anda hari ini.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-summary-card
            title="Pesanan Aktif"
            :value="$summary['active_orders']"
            helper="Sedang dikerjakan"
            accent="blue"
        />
        <x-summary-card
            title="Menunggu Verifikasi"
            :value="$summary['pending_payments']"
            helper="Pembayaran masuk"
            accent="amber"
        />
        <x-summary-card
            title="Pesanan Selesai"
            :value="$summary['completed_orders']"
            helper="Total pesanan"
            accent="violet"
        />
        <x-summary-card
            title="Pendapatan Bulan Ini"
            :value="'Rp ' . number_format($summary['monthly_revenue'], 0, ',', '.')"
            helper="Dari pembayaran lunas"
            accent="emerald"
        />
    </div>

    <!-- Middle Section: Chart & Appointments -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Revenue Chart -->
        <div class="rounded-2xl border border-border bg-white p-6 shadow-sm lg:col-span-2 flex flex-col dark:border-stone-700 dark:bg-stone-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-ink dark:text-stone-100">Grafik Pendapatan</h2>
                    <p class="text-sm text-muted dark:text-stone-400">Tren pendapatan bisnis Anda.</p>
                </div>
                <div>
                    <select wire:model.live="chartFilter" 
                            class="rounded-xl border border-border bg-white px-4 py-2 text-sm font-semibold text-ink shadow-sm 
                                   focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer dark:bg-stone-800 dark:border-stone-600 dark:text-stone-300">
                        <option value="1_week">7 Hari Terakhir</option>
                        <option value="1_month">1 Bulan Terakhir</option>
                        <option value="6_months">6 Bulan Terakhir</option>
                        <option value="1_year">1 Tahun Terakhir</option>
                    </select>
                </div>
            </div>
            
            <div class="relative h-72 w-full mt-4" wire:ignore>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="rounded-2xl border border-border bg-white p-6 shadow-sm flex flex-col dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-ink dark:text-stone-100">Jadwal Fitting</h2>
                    <p class="text-sm text-muted dark:text-stone-400">Hari ini</p>
                </div>
                <a href="{{ route('admin.appointments.index') }}" class="text-sm font-bold text-primary transition hover:text-primary-hover" wire:navigate>
                    Lihat Semua &rarr;
                </a>
            </div>
            
            @if($todayAppointments->isEmpty())
                <div class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-border bg-surface p-6 text-center dark:border-stone-600 dark:bg-stone-800">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white border border-border mb-3 shadow-sm dark:bg-stone-700 dark:border-stone-600">
                        <svg class="h-6 w-6 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-ink dark:text-stone-300">Kosong</p>
                    <p class="text-xs text-muted dark:text-stone-400">Tidak ada fitting hari ini.</p>
                </div>
            @else
                <div class="flex-1 space-y-3 overflow-y-auto pr-2 custom-scrollbar" style="max-height: 280px;">
                    @foreach($todayAppointments as $appointment)
                        <div class="group flex items-start justify-between gap-4 rounded-xl border border-border bg-white p-4 transition hover:bg-surface hover:border-primary/30 shadow-sm dark:border-stone-700 dark:bg-stone-800 dark:hover:bg-stone-700">
                            <div>
                                <div class="font-extrabold font-mono text-ink text-lg dark:text-stone-100">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}</div>
                                <div class="mt-1 text-sm font-bold text-ink dark:text-stone-300">{{ $appointment->customer->name ?? '-' }}</div>
                                <div class="mt-1 flex items-center gap-1.5">
                                    <span class="rounded bg-surface px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-muted border border-border dark:bg-stone-700 dark:text-stone-400">
                                        {{ $appointment->order->service->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="shrink-0 mt-1">
                                @if ($appointment->status === 'menunggu')
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-accent animate-pulse" title="Menunggu"></span>
                                @elseif ($appointment->status === 'selesai')
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500" title="Selesai"></span>
                                @elseif ($appointment->status === 'dibatalkan')
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-rose-500" title="Dibatalkan"></span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="rounded-2xl border border-border bg-white shadow-sm overflow-hidden dark:border-stone-700 dark:bg-stone-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 border-b border-border bg-surface dark:border-stone-700 dark:bg-stone-800/60 gap-4">
            <div>
                <h2 class="text-lg font-bold text-ink dark:text-stone-100">Pesanan Terbaru</h2>
                <p class="text-sm text-muted dark:text-stone-400">Daftar pesanan yang baru saja masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="rounded-xl border border-border bg-white px-4 py-2 text-sm font-bold text-ink transition hover:border-primary hover:text-primary shadow-sm dark:bg-stone-800 dark:border-stone-600 dark:text-stone-300" wire:navigate>
                Lihat Semua
            </a>
        </div>
        
        @if($recentOrders->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-surface mb-4">
                    <svg class="h-8 w-8 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-ink dark:text-stone-100">Belum ada pesanan terbaru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border bg-surface/50 dark:border-stone-700 dark:bg-stone-800/40">
                            <th scope="col" class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Pesanan & Status</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Pelanggan</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Layanan</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-stone-700">
                        @foreach($recentOrders as $order)
                            <tr class="transition hover:bg-primary/5 dark:hover:bg-primary/10 group">
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="font-extrabold text-ink font-mono text-base dark:text-stone-100">{{ $order->order_number }}</div>
                                    <div class="mt-2">
                                        <x-status-badge type="order" :status="$order->status" />
                                    </div>
                                    <div class="text-[11px] font-bold uppercase tracking-widest text-muted mt-2">
                                        {{ $order->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-ink dark:text-stone-300">
                                        {{ $order->customer->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded bg-surface px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-muted border border-border dark:bg-stone-700 dark:text-stone-300">
                                        {{ $order->service->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-white px-4 py-2 text-xs font-bold text-ink transition hover:border-primary hover:text-primary shadow-sm dark:bg-stone-800 dark:border-stone-600 dark:text-stone-300 opacity-0 group-hover:opacity-100" wire:navigate>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        const initChart = () => {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            
            if (window.myRevenueChart) {
                window.myRevenueChart.destroy();
            }

            const chartData = @json($revenueChartData);

            window.myRevenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: chartData.data,
                        borderColor: '#2B4FFF', // primary
                        backgroundColor: 'rgba(43, 79, 255, 0.08)', // primary w/ opacity
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2B4FFF',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#2B4FFF',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1A1A2E',
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13, weight: 'bold' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 14, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                                color: '#6B7280',
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + ' Rb';
                                    }
                                    return 'Rp ' + value;
                                },
                                maxTicksLimit: 6,
                                padding: 10
                            },
                            border: {
                                display: false,
                            },
                            grid: {
                                color: '#E8E4DF', // border color
                                borderDash: [4, 4],
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                                color: '#6B7280',
                                padding: 10
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            border: {
                                display: false
                            }
                        }
                    }
                }
            });
        };

        initChart();

        window.addEventListener('update-revenue-chart', (event) => {
            if (window.myRevenueChart && event.detail) {
                const chartData = event.detail[0]?.chartData || event.detail[0] || event.detail.chartData;
                
                if (chartData && chartData.labels) {
                    window.myRevenueChart.data.labels = chartData.labels;
                    window.myRevenueChart.data.datasets[0].data = chartData.data;
                    window.myRevenueChart.update();
                }
            }
        });

        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            succeed(({ snapshot, effect }) => {
                setTimeout(() => {
                    const ctx = document.getElementById('revenueChart');
                    if (ctx && !window.myRevenueChart) {
                        initChart();
                    }
                }, 100);
            });
        });
    });
</script>
@endpush

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
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm lg:col-span-2 flex flex-col">
            <div class="flex items-start sm:items-center justify-between mb-6 flex-col sm:flex-row gap-3">
                <div>
                    <h2 class="text-lg font-bold text-stone-900">Grafik Pendapatan</h2>
                    <p class="text-sm text-stone-500">Tren pendapatan bisnis Anda.</p>
                </div>
                <div>
                    <select wire:model.live="chartFilter" class="rounded-xl border-stone-200 bg-stone-50 px-3 py-1.5 text-sm font-semibold text-stone-700 shadow-sm focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400 cursor-pointer">
                        <option value="1_week">7 Hari Terakhir</option>
                        <option value="1_month">1 Bulan Terakhir</option>
                        <option value="6_months">6 Bulan Terakhir</option>
                        <option value="1_year">1 Tahun Terakhir</option>
                    </select>
                </div>
            </div>
            
            <div class="relative h-64 w-full" wire:ignore>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm flex flex-col">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-stone-900">Jadwal Fitting</h2>
                    <p class="text-sm text-stone-500">Hari ini</p>
                </div>
                <a href="{{ route('admin.appointments.index') }}" class="text-sm font-semibold text-[#003399] transition hover:text-blue-800" wire:navigate>
                    Lihat &rarr;
                </a>
            </div>
            
            @if($todayAppointments->isEmpty())
                <div class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-stone-300 bg-stone-50 p-6 text-center">
                    <svg class="h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <p class="mt-3 text-sm text-stone-500">Tidak ada fitting hari ini.</p>
                </div>
            @else
                <div class="flex-1 space-y-4 overflow-y-auto pr-2" style="max-height: 250px;">
                    @foreach($todayAppointments as $appointment)
                        <div class="flex items-start justify-between gap-4 rounded-xl border border-stone-100 bg-stone-50 p-4 transition hover:bg-stone-100">
                            <div>
                                <div class="font-bold text-stone-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }} WIB</div>
                                <div class="mt-1 text-sm text-stone-700">{{ $appointment->customer->name ?? '-' }}</div>
                                <div class="mt-0.5 text-xs text-stone-500">{{ $appointment->order->service->name ?? '-' }}</div>
                            </div>
                            <div>
                                @if ($appointment->status === 'menunggu')
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800">Menunggu</span>
                                @elseif ($appointment->status === 'selesai')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Selesai</span>
                                @elseif ($appointment->status === 'dibatalkan')
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-800">Batal</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-stone-900">Pesanan Terbaru</h2>
                <p class="text-sm text-stone-500">Daftar pesanan yang baru saja masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-[#003399] transition hover:text-blue-800" wire:navigate>
                Lihat Semua &rarr;
            </a>
        </div>
        
        @if($recentOrders->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-stone-300 bg-stone-50 py-10 text-center">
                <svg class="h-12 w-12 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="mt-4 text-sm font-bold text-stone-900">Belum ada pesanan terbaru.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-stone-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-stone-600">
                        <thead class="bg-stone-50 text-xs uppercase text-stone-500 border-b border-stone-200">
                            <tr>
                                <th scope="col" class="px-5 py-4 font-semibold">Pesanan</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Pelanggan</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Layanan</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Waktu Masuk</th>
                                <th scope="col" class="px-5 py-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach($recentOrders as $order)
                                <tr class="transition hover:bg-stone-50">
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="font-bold text-stone-900">{{ $order->order_number }}</div>
                                        <div class="mt-0.5">
                                            @php
                                                $statusClasses = [
                                                    'menunggu_konfirmasi' => 'bg-slate-100 text-slate-800',
                                                    'menunggu_fitting' => 'bg-blue-100 text-blue-800',
                                                    'menunggu_dp' => 'bg-amber-100 text-amber-800',
                                                    'menunggu_bahan' => 'bg-orange-100 text-orange-800',
                                                    'dalam_antrian' => 'bg-indigo-100 text-indigo-800',
                                                    'dijahit' => 'bg-purple-100 text-purple-800',
                                                    'selesai_produksi' => 'bg-teal-100 text-teal-800',
                                                    'siap_diambil' => 'bg-emerald-100 text-emerald-800',
                                                    'selesai' => 'bg-emerald-500 text-white',
                                                    'ditolak' => 'bg-red-100 text-red-800',
                                                    'dibatalkan' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusLabel = [
                                                    'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                                                    'menunggu_fitting' => 'Menunggu Fitting',
                                                    'menunggu_dp' => 'Menunggu DP',
                                                    'menunggu_bahan' => 'Menunggu Bahan',
                                                    'dalam_antrian' => 'Antrian Produksi',
                                                    'dijahit' => 'Proses Jahit',
                                                    'selesai_produksi' => 'Selesai Produksi',
                                                    'siap_diambil' => 'Siap Diambil',
                                                    'selesai' => 'Selesai',
                                                    'ditolak' => 'Ditolak',
                                                    'dibatalkan' => 'Dibatalkan',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusClasses[$order->status] ?? 'bg-stone-100 text-stone-800' }}">
                                                {{ $statusLabel[$order->status] ?? ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 font-medium text-stone-800">
                                        {{ $order->customer->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">
                                            {{ $order->service->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-stone-500">
                                        {{ $order->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-stone-700 shadow-sm ring-1 ring-inset ring-stone-300 transition hover:bg-stone-50" wire:navigate>
                                            Detail
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', () => {
        const initChart = () => {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            
            // Hancurkan chart lama jika ada agar tidak bentrok
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
                        borderColor: '#059669', // Emerald 600
                        backgroundColor: 'rgba(16, 185, 129, 0.1)', // Emerald 500 w/ opacity
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
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
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000) + ' Jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000) + ' Rb';
                                    }
                                    return 'Rp ' + value;
                                },
                                maxTicksLimit: 6
                            },
                            border: {
                                dash: [4, 4]
                            },
                            grid: {
                                color: '#f5f5f4' // stone-100
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        };

        initChart();

        // Listen for filter updates
        window.addEventListener('update-revenue-chart', (event) => {
            if (window.myRevenueChart && event.detail) {
                // Livewire 3 passes arguments as an array inside event.detail
                const chartData = event.detail[0]?.chartData || event.detail[0] || event.detail.chartData;
                
                if (chartData && chartData.labels) {
                    window.myRevenueChart.data.labels = chartData.labels;
                    window.myRevenueChart.data.datasets[0].data = chartData.data;
                    window.myRevenueChart.update();
                }
            }
        });

        // Jika Livewire melakukan navigasi halaman, pastikan chart di-render ulang
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

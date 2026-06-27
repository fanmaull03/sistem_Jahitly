@props(['status', 'label' => null, 'type' => 'order'])

@php
    $labelMaps = [
        'order' => [
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'ditolak' => 'Ditolak',
            'menunggu_fitting' => 'Menunggu Fitting',
            'menunggu_dp' => 'Menunggu DP',
            'menunggu_bahan' => 'Menunggu Bahan',
            'dalam_antrian' => 'Dalam Antrian',
            'dijahit' => 'Dijahit',
            'selesai_produksi' => 'Selesai Produksi',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ],
        'payment' => [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'ditolak' => 'Ditolak',
        ],
        'payment_status' => [
            'belum_bayar' => 'Belum Bayar',
            'menunggu' => 'Menunggu Verifikasi',
            'dp' => 'DP Terverifikasi',
            'lunas' => 'Lunas',
        ],
        'appointment' => [
            'menunggu' => 'Menunggu',
            'terkonfirmasi' => 'Terkonfirmasi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ],
        'material' => [
            'ready' => 'Ready',
            'po' => 'PO',
        ],
    ];

    $classMaps = [
        'order' => [
            'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            'ditolak' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
            'menunggu_fitting' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'menunggu_dp' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            'menunggu_bahan' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
            'dalam_antrian' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'dijahit' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
            'selesai_produksi' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            'siap_diambil' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300',
            'selesai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'dibatalkan' => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
        ],
        'payment' => [
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'terverifikasi' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            'ditolak' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
        ],
        'payment_status' => [
            'belum_bayar' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
            'menunggu' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'dp' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
            'lunas' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
        ],
        'appointment' => [
            'menunggu' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
            'terkonfirmasi' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            'selesai' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            'dibatalkan' => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
        ],
        'material' => [
            'ready' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
            'po' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
        ],
    ];

    $labelMap = $labelMaps[$type] ?? [];
    $classMap = $classMaps[$type] ?? [];

    $label = $label ?? ($labelMap[$status] ?? ucwords(str_replace('_', ' ', (string) $status)));
    $classes = $classMap[$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">
    {{ $label }}
</span>

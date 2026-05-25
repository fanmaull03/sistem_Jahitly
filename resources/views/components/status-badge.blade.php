@props(['status', 'label' => null, 'type' => 'order'])

@php
    $labelMaps = [
        'order' => [
            'menunggu_appointment' => 'Menunggu Appointment',
            'menunggu_bahan' => 'Menunggu Bahan',
            'diproses' => 'Diproses',
            'dijahit' => 'Dijahit',
            'finishing' => 'Finishing',
            'selesai' => 'Selesai',
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
            'menunggu_appointment' => 'bg-amber-100 text-amber-800',
            'menunggu_bahan' => 'bg-sky-100 text-sky-800',
            'diproses' => 'bg-blue-100 text-blue-800',
            'dijahit' => 'bg-indigo-100 text-indigo-800',
            'finishing' => 'bg-purple-100 text-purple-800',
            'selesai' => 'bg-green-100 text-green-800',
        ],
        'payment' => [
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800',
            'terverifikasi' => 'bg-emerald-100 text-emerald-800',
            'ditolak' => 'bg-rose-100 text-rose-800',
        ],
        'payment_status' => [
            'belum_bayar' => 'bg-slate-100 text-slate-700',
            'menunggu' => 'bg-amber-100 text-amber-800',
            'dp' => 'bg-sky-100 text-sky-800',
            'lunas' => 'bg-emerald-100 text-emerald-800',
        ],
        'appointment' => [
            'menunggu' => 'bg-amber-100 text-amber-800',
            'terkonfirmasi' => 'bg-blue-100 text-blue-800',
            'selesai' => 'bg-emerald-100 text-emerald-800',
            'dibatalkan' => 'bg-slate-200 text-slate-700',
        ],
        'material' => [
            'ready' => 'bg-emerald-100 text-emerald-800',
            'po' => 'bg-amber-100 text-amber-800',
        ],
    ];

    $labelMap = $labelMaps[$type] ?? [];
    $classMap = $classMaps[$type] ?? [];

    $label = $label ?? ($labelMap[$status] ?? ucwords(str_replace('_', ' ', (string) $status)));
    $classes = $classMap[$status] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $classes }}">
    {{ $label }}
</span>

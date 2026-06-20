<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #JHT-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body {
                background-color: white !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-[#fcfaf8] font-sans antialiased text-slate-800">

    {{-- Top Action Bar --}}
    <div class="no-print sticky top-0 z-10 flex items-center justify-between border-b border-blue-100 bg-white/80 px-6 py-4 backdrop-blur-md">
        <button onclick="window.close()" class="flex items-center gap-2 text-sm font-semibold text-blue-800 transition hover:text-blue-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </button>
        <div class="text-lg font-bold text-blue-900">Jahitly</div>
        <div class="flex gap-4 text-blue-800">
            {{-- <button onclick="window.print()" class="transition hover:text-blue-600" title="Download">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </button> --}}
            <button onclick="window.print()" class="transition hover:text-blue-600" title="Print">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.728 9.752a2.25 2.25 0 011.834-1.244l6.876-.625a2.25 2.25 0 012.35 1.834l.056.401m-10.97.262l1.624 11.854a2.25 2.25 0 002.35 1.834l6.876-.625a2.25 2.25 0 001.834-2.35l-1.624-11.854m-10.97.262l-1.123-8.2a2.25 2.25 0 011.834-2.35l6.876-.625a2.25 2.25 0 012.35 1.834l1.123 8.2m-12.093 0h14.338" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Invoice Document --}}
    <div class="mx-auto max-w-3xl py-10 px-4 print-card">
        <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm sm:p-12 print-card relative overflow-hidden">
            
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start border-b border-slate-100 pb-8">
                <div>
                    <h1 class="text-3xl font-bold text-blue-800">Jahitly</h1>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                        Jl. Sudirman No. 42, Jakarta Pusat<br>
                        DKI Jakarta, 10220
                    </p>
                </div>
                <div class="mt-6 sm:mt-0 text-left sm:text-right">
                    @if (isset($payment))
                        <h2 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                            INVOICE {{ $payment->payment_type === 'dp' ? 'DP' : 'PELUNASAN' }}
                        </h2>
                    @else
                        <h2 class="text-xl font-bold tracking-widest text-slate-900 uppercase">Invoice</h2>
                    @endif
                    <p class="mt-1 text-sm font-semibold text-slate-700">#{{ $order->order_number }}</p>
                    <p class="text-sm text-slate-500">{{ isset($payment) ? $payment->created_at->translatedFormat('d F Y') : $order->created_at->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            {{-- Ditagihkan Kepada --}}
            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-3">Ditagihkan Kepada:</h3>
                <div class="inline-block rounded-xl bg-slate-50 p-4 min-w-[250px] border border-slate-100">
                    <p class="font-bold text-slate-900">{{ $order->customer->name }}</p>
                    <p class="mt-1 text-sm text-slate-600 leading-relaxed max-w-xs">
                        {{ $order->customer->address ?? 'Alamat tidak diisi' }}
                    </p>
                    <div class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.077-7.077l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        {{ $order->customer->phone ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="mt-10 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="py-3 font-bold text-slate-900">Deskripsi Item</th>
                            <th class="py-3 text-center font-bold text-slate-900 w-24">Qty</th>
                            <th class="py-3 text-right font-bold text-slate-900 w-32">Harga Satuan</th>
                            <th class="py-3 text-right font-bold text-slate-900 w-32">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- Row Layanan --}}
                        @if ($serviceTotal > 0)
                        <tr>
                            <td class="py-4 text-slate-700">Layanan Jahit - {{ $order->service->name }}</td>
                            <td class="py-4 text-center text-slate-700">{{ $order->quantity }}</td>
                            <td class="py-4 text-right text-slate-700">Rp {{ number_format((float) $order->service->base_price, 0, ',', '.') }}</td>
                            <td class="py-4 text-right text-slate-700">Rp {{ number_format($serviceTotal, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        {{-- Row Bahan --}}
                        @if ($order->fabric && $order->service->type !== 'vermak')
                        <tr>
                            <td class="py-4 text-slate-700">Bahan - {{ $order->fabric->name }} ({{ $order->fabric->color }})</td>
                            <td class="py-4 text-center text-slate-700">{{ $order->quantity }}</td>
                            <td class="py-4 text-right text-slate-700">Rp {{ number_format((float) $order->fabric->price_per_meter, 0, ',', '.') }}</td>
                            <td class="py-4 text-right text-slate-700">Rp {{ number_format($fabricTotal, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        {{-- Row Vermak --}}
                        @if ($order->service->type === 'vermak' && $order->alteration_details)
                            @php
                                $details = json_decode($order->alteration_details, true) ?? [];
                            @endphp
                            @foreach ($details as $detail)
                                <tr>
                                    <td class="py-4 text-slate-700">Vermak - {{ $detail['name'] ?? 'Lainnya' }}</td>
                                    <td class="py-4 text-center text-slate-700">{{ $order->quantity }}</td>
                                    <td class="py-4 text-right text-slate-700">Rp {{ number_format((float) ($detail['price'] ?? 0), 0, ',', '.') }}</td>
                                    <td class="py-4 text-right text-slate-700">Rp {{ number_format(((float) ($detail['price'] ?? 0)) * $order->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif
                        {{-- Row Penyesuaian Harga --}}
                        @if ($adjustment != 0)
                        <tr>
                            <td class="py-4 text-slate-700">Penyesuaian Harga (Admin)</td>
                            <td class="py-4 text-center text-slate-700">-</td>
                            <td class="py-4 text-right text-slate-700">-</td>
                            <td class="py-4 text-right text-slate-700">Rp {{ number_format($adjustment, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Total Summary --}}
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-between sm:items-start">
                
                {{-- Left Side: Stamp Lunas --}}
                <div class="w-full sm:w-1/2 pt-4">
                    @if ($isLunas)
                        @php
                            $stampDate = isset($payment) ? $payment->updated_at : ($order->payments->where('status', 'terverifikasi')->sortByDesc('updated_at')->first()?->updated_at);
                            $lunasDate = $stampDate ? $stampDate->translatedFormat('d M Y, H:i') . ' WIB' : 'Selesai';
                            $stampText = isset($payment) ? 'LUNAS ' . strtoupper($payment->payment_type) : 'LUNAS';
                        @endphp
                        <div class="inline-block -rotate-[10deg] transform rounded-lg border-4 border-blue-600 px-6 py-3 text-center opacity-90 shadow-sm mt-4 ml-4">
                            <div class="text-2xl font-black tracking-widest text-blue-600 uppercase">{{ $stampText }}</div>
                            <div class="mt-1 text-xs font-bold text-blue-600">{{ $lunasDate }}</div>
                        </div>
                    @endif
                </div>

                {{-- Right Side: Calculation --}}
                <div class="mt-8 sm:mt-0 w-full sm:w-1/2 max-w-sm">
                    <div class="flex items-center justify-between py-2 text-sm text-slate-700">
                        <span>Subtotal Item</span>
                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($adjustment != 0)
                    <div class="flex items-center justify-between py-2 text-sm text-slate-700">
                        <span>Penyesuaian Harga</span>
                        <span class="font-medium">Rp {{ number_format($adjustment, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-2 text-sm text-slate-900 font-semibold border-b border-slate-200">
                        <span>Total Nilai Pesanan</span>
                        <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    @if (isset($payment))
                        <div class="flex items-center justify-between py-2 text-sm text-slate-700 border-t border-slate-100">
                            <span>Tagihan {{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}</span>
                            <span class="font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between bg-blue-50 px-4 py-3 text-lg font-bold text-blue-900 rounded-lg">
                            <span>Total Dibayar</span>
                            <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between py-2 text-sm text-slate-700 border-b border-slate-200">
                            <span>PPN (0%)</span>
                            <span class="font-medium">Rp 0</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between bg-blue-50 px-4 py-3 text-lg font-bold text-blue-900 rounded-lg">
                            <span>Total Keseluruhan</span>
                            <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <div class="mt-16 border-t border-slate-200 pt-6 text-center">
                <p class="text-sm font-bold text-slate-900">Terima kasih atas kepercayaan Anda menggunakan layanan Jahitly.</p>
                <p class="mt-1 text-xs text-slate-500">Jika ada pertanyaan mengenai invoice ini, silakan hubungi cs@jahitly.com</p>
            </div>
            
        </div>
    </div>

</body>
</html>

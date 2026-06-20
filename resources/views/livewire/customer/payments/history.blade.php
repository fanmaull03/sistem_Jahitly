@php
    $noProofMethods = ['cash'];
@endphp

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="page-enter space-y-6 pb-32 lg:pb-10">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">Riwayat Pembayaran</h1>
            <p class="mt-1 text-sm text-stone-600">
                @if ($order)
                    Pesanan #{{ $order->order_number }} - {{ $order->service->name }}
                @else
                    Semua pembayaran Anda
                @endif
            </p>
        </div>
        <a 
            href="{{ $order ? route('orders.show', $order) : route('orders.index') }}" 
            class="inline-flex items-center gap-2 text-sm font-semibold text-stone-500 transition hover:text-stone-900 sm:pr-4" 
            wire:navigate
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Status Filter -->
    <div class="flex flex-wrap gap-3">
        @foreach ($statuses as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                @class([
                    'rounded-full px-5 py-1.5 text-sm font-semibold transition-all duration-200',
                    'bg-[#F8A01A] text-stone-900 shadow-sm' => $statusFilter === $value,
                    'bg-stone-200 text-stone-500 hover:bg-stone-300 hover:text-stone-700' => $statusFilter !== $value,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Payments Table / List -->
    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        @if ($payments->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-stone-700">Nomor Pesanan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-stone-700">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-stone-700">Metode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-stone-700">Jumlah</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-stone-700">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-stone-700">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-stone-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach ($payments as $payment)
                            <tr class="transition hover:bg-stone-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $payment->order) }}" class="font-semibold text-stone-900 hover:text-blue-600" wire:navigate>
                                        #{{ $payment->order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-lg bg-stone-100 px-3 py-1 text-xs font-bold uppercase text-stone-700">
                                        {{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-stone-600">
                                    {{ $this->getPaymentMethodLabel($payment->payment_method) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-stone-900">
                                    Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold border',
                                        $this->getStatusColor($payment->status),
                                    ])>
                                        {{ $this->getStatusLabel($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-stone-600">
                                    {{ $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($payment->status === 'ditolak' || $payment->status === 'menunggu_verifikasi')
                                        <button
                                            @if ($payment->status === 'ditolak')
                                                onclick="document.getElementById('rejectionModal-{{ $payment->id }}').showModal()"
                                            @else
                                                onclick="Livewire.navigate('{{ route('payments.show', $payment) }}')"
                                            @endif
                                            class="text-sm font-semibold text-blue-600 hover:text-blue-800"
                                        >
                                            {{ $payment->status === 'ditolak' ? 'Lihat Alasan' : 'Lihat Detail' }}
                                        </button>
                                    @elseif ($payment->status === 'terverifikasi' && $payment->proof_file_path)
                                        <a href="{{ Storage::url($payment->proof_file_path) }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                            Unduh Bukti
                                        </a>
                                    @else
                                        <span class="text-sm text-stone-500">—</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Rejection Modal -->
                            @if ($payment->status === 'ditolak')
                                <dialog id="rejectionModal-{{ $payment->id }}" class="modal">
                                    <div class="modal-box max-w-md">
                                        <h3 class="mb-4 text-lg font-bold text-stone-900">Alasan Penolakan</h3>
                                        <p class="mb-6 text-stone-600">{{ $payment->rejection_note }}</p>
                                        <p class="mb-6 text-sm text-stone-500">
                                            Anda dapat membuat pembayaran baru dengan metode atau bukti yang benar.
                                        </p>
                                        <div class="flex gap-3">
                                            <button
                                                onclick="document.getElementById('rejectionModal-{{ $payment->id }}').close()"
                                                class="flex-1 rounded-full border border-stone-300 px-4 py-2 font-semibold text-stone-700 hover:bg-stone-50"
                                            >
                                                Tutup
                                            </button>
                                            <a
                                                href="{{ route('orders.show', $payment->order) }}"
                                                wire:navigate
                                                onclick="document.getElementById('rejectionModal-{{ $payment->id }}').close()"
                                                class="flex-1 rounded-full bg-blue-600 px-4 py-2 text-center font-semibold text-white hover:bg-blue-700"
                                            >
                                                Bayar Ulang
                                            </a>
                                        </div>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-stone-200">
                @foreach ($payments as $payment)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('orders.show', $payment->order) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800" wire:navigate>
                                    #{{ $payment->order->order_number }}
                                </a>
                                <p class="text-xs text-stone-500">{{ $payment->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span @class([
                                'inline-flex rounded-full px-3 py-1 text-xs font-bold border',
                                $this->getStatusColor($payment->status),
                            ])>
                                {{ $this->getStatusLabel($payment->status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg bg-stone-50 p-3">
                            <div>
                                <p class="text-xs text-stone-600">
                                    {{ $this->getPaymentMethodLabel($payment->payment_method) }} · 
                                    <span class="font-semibold">{{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}</span>
                                </p>
                                <p class="text-lg font-bold text-stone-900">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if ($payment->status === 'ditolak')
                            <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                                <p class="text-xs font-semibold text-red-800">Alasan Penolakan:</p>
                                <p class="text-xs text-red-700">{{ $payment->rejection_note }}</p>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            @if ($payment->status === 'ditolak')
                                <a
                                    href="{{ route('orders.show', $payment->order) }}"
                                    wire:navigate
                                    class="flex-1 rounded-full bg-blue-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-blue-700"
                                >
                                    Bayar Ulang
                                </a>
                            @elseif ($payment->status === 'terverifikasi' && $payment->proof_file_path)
                                <a
                                    href="{{ Storage::url($payment->proof_file_path) }}"
                                    target="_blank"
                                    class="flex-1 rounded-full bg-stone-200 px-3 py-2 text-center text-xs font-semibold text-stone-800 hover:bg-stone-300"
                                >
                                    Unduh Bukti
                                </a>
                            @endif
                            <a
                                href="{{ route('orders.show', $payment->order) }}"
                                wire:navigate
                                class="flex-1 rounded-full border border-stone-300 px-3 py-2 text-center text-xs font-semibold text-stone-700 hover:bg-stone-50"
                            >
                                Pesanan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="border-t border-stone-200 bg-stone-50 px-6 py-4">
                {{ $payments->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="mb-4 h-12 w-12 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-semibold text-stone-700">Belum ada riwayat pembayaran</p>
                <p class="mt-1 text-sm text-stone-500">
                    @if ($order)
                        Lakukan pembayaran untuk pesanan ini
                    @else
                        Lakukan pembayaran untuk pesanan Anda
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

@script
<script>
    // Initialize Livewire
    Livewire.on('paymentUpdated', () => {
        $wire.refresh();
    });
</script>
@endscript

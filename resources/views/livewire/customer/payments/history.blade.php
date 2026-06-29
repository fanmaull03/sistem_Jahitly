@php
    $noProofMethods = ['cash'];
@endphp

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="page-enter space-y-6 pb-32 lg:pb-10">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-ink dark:text-stone-100">Riwayat Pembayaran</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">
                @if ($order)
                    Pesanan #{{ $order->order_number }} - {{ $order->service->name }}
                @else
                    Semua pembayaran Anda
                @endif
            </p>
        </div>
        <a 
            href="{{ $order ? route('orders.show', $order) : route('orders.index') }}" 
            class="inline-flex items-center gap-2 text-xs font-semibold text-muted hover:text-primary transition sm:pr-4" 
            wire:navigate
        >
            ← Kembali
        </a>
    </div>

    <!-- Status Filter -->
    <div class="flex flex-wrap gap-3">
        @foreach ($statuses as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                @class([
                    'rounded-full px-5 py-1.5 text-sm font-semibold transition-all duration-200 border',
                    'bg-primary border-primary text-white shadow-sm' => $statusFilter === $value,
                    'bg-surface border-transparent text-muted hover:bg-border hover:text-ink dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-300' => $statusFilter !== $value,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <!-- Payments Table / List -->
    <div class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
        @if ($payments->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-surface dark:border-stone-700 dark:bg-stone-800/50">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Nomor Pesanan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Metode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Jumlah</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Tanggal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-ink dark:text-stone-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border dark:divide-stone-700">
                        @foreach ($payments as $payment)
                            <tr class="transition hover:bg-primary/5 dark:hover:bg-stone-700/50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $payment->order) }}" class="font-semibold text-ink hover:text-primary dark:text-stone-100 dark:hover:text-blue-400" wire:navigate>
                                        #{{ $payment->order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-lg bg-surface px-3 py-1 text-xs font-bold uppercase text-muted dark:bg-stone-700 dark:text-stone-300">
                                        {{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted dark:text-stone-400">
                                    {{ $this->getPaymentMethodLabel($payment->payment_method) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-ink dark:text-stone-100">
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
                                <td class="px-6 py-4 text-center text-sm text-muted dark:text-stone-400">
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
                                            class="text-sm font-semibold text-primary hover:text-primary-hover dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            {{ $payment->status === 'ditolak' ? 'Lihat Alasan' : 'Lihat Detail' }}
                                        </button>
                                    @elseif ($payment->status === 'terverifikasi')
                                        <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="text-sm font-semibold text-primary hover:text-primary-hover dark:text-blue-400 dark:hover:text-blue-300">
                                            Unduh Invoice
                                        </a>
                                    @else
                                        <span class="text-sm text-muted dark:text-stone-400">—</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Rejection Modal -->
                            @if ($payment->status === 'ditolak')
                                <dialog id="rejectionModal-{{ $payment->id }}" class="modal">
                                    <div class="modal-box max-w-md bg-white dark:bg-stone-800">
                                        <h3 class="mb-4 text-lg font-bold text-ink">Alasan Penolakan</h3>
                                        <p class="mb-6 text-muted">{{ $payment->rejection_note }}</p>
                                        <p class="mb-6 text-sm text-muted">
                                            Anda dapat membuat pembayaran baru dengan metode atau bukti yang benar.
                                        </p>
                                        <div class="flex gap-3">
                                            <button
                                                onclick="document.getElementById('rejectionModal-{{ $payment->id }}').close()"
                                                class="flex-1 rounded-full border border-border px-4 py-2 font-semibold text-ink hover:bg-surface"
                                            >
                                                Tutup
                                            </button>
                                            <a
                                                href="{{ route('orders.show', $payment->order) }}"
                                                wire:navigate
                                                onclick="document.getElementById('rejectionModal-{{ $payment->id }}').close()"
                                                class="flex-1 rounded-full bg-primary px-4 py-2 text-center font-semibold text-white hover:bg-primary-hover"
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
            <div class="md:hidden divide-y divide-border">
                @foreach ($payments as $payment)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <a href="{{ route('orders.show', $payment->order) }}" class="text-sm font-bold text-primary hover:text-primary-hover dark:text-blue-400 dark:hover:text-blue-300" wire:navigate>
                                    #{{ $payment->order->order_number }}
                                </a>
                                <p class="text-xs text-muted dark:text-stone-400">{{ $payment->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <span @class([
                                'inline-flex rounded-full px-3 py-1 text-xs font-bold border',
                                $this->getStatusColor($payment->status),
                            ])>
                                {{ $this->getStatusLabel($payment->status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg bg-surface p-3 dark:bg-stone-700">
                            <div>
                                <p class="text-xs text-muted dark:text-stone-400">
                                    {{ $this->getPaymentMethodLabel($payment->payment_method) }} · 
                                    <span class="font-semibold">{{ $payment->payment_type === 'dp' ? 'DP' : 'Pelunasan' }}</span>
                                </p>
                                <p class="text-lg font-bold text-ink dark:text-stone-100">Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if ($payment->status === 'ditolak')
                            <div class="rounded-lg border border-red-200 bg-red-50 p-3 dark:bg-red-900/30 dark:border-red-800">
                                <p class="text-xs font-semibold text-red-800 dark:text-red-300">Alasan Penolakan:</p>
                                <p class="text-xs text-red-700 dark:text-red-400">{{ $payment->rejection_note }}</p>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            @if ($payment->status === 'ditolak')
                                <a
                                    href="{{ route('orders.show', $payment->order) }}"
                                    wire:navigate
                                    class="flex-1 rounded-full bg-primary px-3 py-2 text-center text-xs font-semibold text-white hover:bg-primary-hover"
                                >
                                    Bayar Ulang
                                </a>
                            @elseif ($payment->status === 'terverifikasi')
                                <a
                                    href="{{ route('payments.invoice', $payment) }}"
                                    target="_blank"
                                    class="flex-1 rounded-full bg-border px-3 py-2 text-center text-xs font-semibold text-ink hover:bg-border/80 dark:bg-stone-700 dark:text-stone-300 dark:hover:bg-stone-600"
                                >
                                    Unduh Invoice
                                </a>
                            @endif
                            <a
                                href="{{ route('orders.show', $payment->order) }}"
                                wire:navigate
                                class="flex-1 rounded-full border border-border px-3 py-2 text-center text-xs font-semibold text-ink hover:bg-surface dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"
                            >
                                Pesanan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="border-t border-border bg-surface px-6 py-4 dark:border-stone-700 dark:bg-stone-800/50">
                {{ $payments->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="mb-4 h-12 w-12 text-border dark:text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-semibold text-ink dark:text-stone-300">Belum ada riwayat pembayaran</p>
                <p class="mt-1 text-sm text-muted dark:text-stone-400">
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

<div class="page-enter space-y-6">
    <div data-reveal>
        <h1 class="text-2xl font-bold">Jadwal Appointment</h1>
        <p class="text-sm text-slate-500">Kelola agenda konsultasi dan fitting.</p>
    </div>

    <div data-reveal data-reveal-delay="1" class="max-w-sm">
        <label class="text-sm font-semibold text-slate-700">Filter Tanggal</label>
        <input
            type="date"
            wire:model.live="dateFilter"
            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2 text-base"
        />
    </div>

    @php
        $groupedAppointments = $appointments->groupBy(function ($item) {
            return $item->appointment_date->format('d M Y');
        });
    @endphp

    <div class="space-y-6">
        @forelse ($groupedAppointments as $date => $items)
            <section class="space-y-3">
                <h2 class="text-lg font-semibold text-slate-800">{{ $date }}</h2>

                @foreach ($items as $appointment)
                    <div class="hover-lift rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-base font-semibold text-slate-900">
                                    {{ $appointment->appointment_date->format('H:i') }} - {{ $appointment->order->order_number }}
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ $appointment->customer->name }} · {{ $appointment->order->service->name }}
                                </div>
                                <div class="mt-2">
                                    <x-status-badge type="appointment" :status="$appointment->status" />
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    wire:click="confirm({{ $appointment->id }})"
                                    @class([
                                        'rounded-xl px-4 py-2 text-sm font-semibold',
                                        'bg-slate-900 text-white hover:bg-slate-800' => $appointment->status === 'menunggu',
                                        'bg-slate-200 text-slate-500 cursor-not-allowed' => $appointment->status !== 'menunggu',
                                    ])
                                    @disabled($appointment->status !== 'menunggu')
                                >
                                    Konfirmasi Kedatangan
                                </button>
                                <button
                                    type="button"
                                    wire:click="complete({{ $appointment->id }})"
                                    @class([
                                        'rounded-xl px-4 py-2 text-sm font-semibold',
                                        'bg-emerald-600 text-white hover:bg-emerald-500' => $appointment->status === 'terkonfirmasi',
                                        'bg-slate-200 text-slate-500 cursor-not-allowed' => $appointment->status !== 'terkonfirmasi',
                                    ])
                                    @disabled($appointment->status !== 'terkonfirmasi')
                                >
                                    Selesaikan Appointment
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @empty
            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-6 text-sm text-slate-500">
                Tidak ada appointment pada tanggal ini.
            </div>
        @endforelse
    </div>

    <div class="border-t border-slate-200 pt-4">
        {{ $appointments->links() }}
    </div>
</div>

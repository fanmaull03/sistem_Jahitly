<x-slot name="header">Jadwal Fitting</x-slot>



<x-slot name="actions">
    <div class="flex items-center gap-3">
        <button type="button" class="relative flex items-center gap-2 rounded-xl border border-border bg-white px-4 py-2 text-sm font-bold text-ink transition hover:border-primary hover:text-primary hover:shadow-sm overflow-hidden cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Pilih Tanggal
            <input type="date" wire:model.live="dateFilter" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
        </button>
        <button type="button" class="flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20" onclick="alert('Fitur tambah jadwal baru dari admin akan segera hadir.')">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Jadwal Baru
        </button>
    </div>
</x-slot>

<div class="page-enter">
    <div class="mb-8 mt-2">
        <p class="text-sm font-bold uppercase tracking-widest text-primary/80 mb-1">Interaksi</p>
        <h1 class="text-3xl font-display font-bold text-ink dark:text-stone-100">Jadwal Fitting</h1>
        <p class="mt-2 text-base text-muted dark:text-stone-400">Kelola janji temu fitting dengan pelanggan Anda.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-6">
        <!-- Sidebar -->
        <div class="lg:col-span-4 xl:col-span-3 space-y-6">
            <!-- Calendar -->
            <div class="rounded-2xl border border-border bg-white p-5 shadow-sm relative overflow-hidden dark:border-stone-700 dark:bg-stone-800">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-ink dark:text-stone-100">{{ $monthName }}</h3>
                    <div class="flex gap-4">
                        <button wire:click="previousMonth" class="text-muted transition hover:text-primary">
                            <svg class="h-4 w-4 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button wire:click="nextMonth" class="text-muted transition hover:text-primary">
                            <svg class="h-4 w-4 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold uppercase tracking-widest text-muted mb-3">
                    <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                </div>
                <div class="grid grid-cols-7 gap-y-2 gap-x-1">
                    @foreach ($calendarDays as $day)
                        <div class="flex justify-center py-1">
                            <button 
                                wire:click="selectDate('{{ $day['date'] }}')"
                                @class([
                                    'relative h-9 w-9 rounded-full flex items-center justify-center text-sm transition-colors font-semibold',
                                    'text-muted/40' => ! $day['is_current_month'],
                                    'text-ink hover:bg-surface' => $day['is_current_month'] && $dateFilter !== $day['date'],
                                    'bg-primary text-white font-bold shadow-sm' => $dateFilter === $day['date'],
                                ])
                            >
                                {{ $day['day'] }}
                                @if ($day['has_appointments'])
                                    <span @class([
                                        'absolute bottom-1 h-1.5 w-1.5 rounded-full',
                                        'bg-accent' => $dateFilter !== $day['date'],
                                        'bg-white' => $dateFilter === $day['date'],
                                    ])></span>
                                @endif
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h3 class="text-[11px] font-bold uppercase tracking-widest text-muted mb-5">Ringkasan Hari Ini</h3>
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm font-medium text-ink dark:text-stone-300">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            Total Fitting
                        </div>
                        <span class="font-extrabold text-xl text-ink dark:text-stone-100">{{ $summary['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm font-medium text-ink dark:text-stone-300">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-accent/10 text-accent">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Menunggu
                        </div>
                        <span class="font-extrabold text-xl text-accent">{{ $summary['menunggu'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm font-medium text-ink dark:text-stone-300">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-surface text-muted border border-border">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            Selesai
                        </div>
                        <span class="font-extrabold text-xl text-ink dark:text-stone-100">{{ $summary['selesai'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-8 xl:col-span-9">
            <div class="rounded-2xl border border-border bg-white shadow-sm overflow-hidden dark:border-stone-700 dark:bg-stone-800">
                <!-- Header section of list -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 border-b border-border bg-surface dark:border-stone-700 dark:bg-stone-800">
                    <div>
                        <h2 class="text-2xl font-bold text-ink dark:text-stone-100">
                            {{ \Carbon\Carbon::parse($dateFilter)->translatedFormat('l, d F Y') }}
                        </h2>
                        <p class="text-sm font-medium text-muted mt-1">{{ $summary['total'] }} Jadwal Fitting Hari Ini</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex rounded-xl border border-border bg-white p-1">
                        <button class="rounded-lg bg-surface px-5 py-2 text-sm font-bold text-ink shadow-sm border border-border">Daftar</button>
                        <button class="rounded-lg px-5 py-2 text-sm font-semibold text-muted hover:text-ink transition">Timeline</button>
                    </div>
                </div>

                <!-- List -->
                <div class="divide-y divide-border dark:divide-stone-700">
                    @forelse ($appointments as $appointment)
                        <div class="group flex flex-col sm:flex-row p-6 gap-6 hover:bg-primary/5 transition relative">
                            @if($appointment->status === 'menunggu')
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent"></div>
                            @elseif($appointment->status === 'terkonfirmasi')
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                            @endif
                            
                            <!-- Time -->
                            <div class="w-full sm:w-28 shrink-0 border-b sm:border-b-0 sm:border-r border-border pb-4 sm:pb-0 sm:pr-6">
                                <div @class([
                                    'text-3xl font-extrabold font-mono',
                                    'text-primary' => $appointment->status === 'terkonfirmasi',
                                    'text-accent' => $appointment->status === 'menunggu',
                                    'text-muted' => $appointment->status === 'selesai',
                                ])>
                                    {{ $appointment->appointment_date->format('H:i') }}
                                </div>
                                <div @class([
                                    'text-[11px] font-bold uppercase tracking-widest mt-1',
                                    'text-primary' => $appointment->status === 'terkonfirmasi',
                                    'text-accent' => $appointment->status === 'menunggu',
                                    'text-muted' => $appointment->status === 'selesai',
                                ])>
                                    {{ $appointment->appointment_date->format('H') < 12 ? 'Pagi' : ($appointment->appointment_date->format('H') < 15 ? 'Siang' : 'Sore') }}
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-bold text-ink dark:text-stone-100 text-lg truncate">{{ $appointment->customer->name }}</div>
                                        <div class="text-sm font-medium text-muted mt-1 font-mono">ID: {{ $appointment->order->order_number }}</div>
                                    </div>
                                    <div class="shrink-0 ml-4">
                                        <x-status-badge type="appointment" :status="$appointment->status" />
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span class="inline-flex items-center rounded-lg bg-surface px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-muted border border-border dark:bg-stone-700 dark:text-stone-300">
                                        {{ $appointment->order->service->name }}
                                    </span>
                                    <span class="inline-flex items-center rounded-lg bg-surface px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-muted border border-border dark:bg-stone-700 dark:text-stone-300">
                                        Fitting
                                    </span>
                                </div>

                                @if($appointment->notes)
                                    <div class="mt-4 rounded-xl bg-accent/5 p-4 text-sm font-medium text-ink/80 border border-accent/20 relative">
                                        <span class="font-bold text-accent text-[11px] uppercase tracking-widest block mb-1">Catatan Customer</span> 
                                        {{ $appointment->notes }}
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="w-full sm:w-40 shrink-0 flex flex-col justify-center gap-3 mt-4 sm:mt-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($appointment->status === 'menunggu')
                                    <button type="button" wire:click="confirm({{ $appointment->id }})" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20 text-center">
                                        Konfirmasi Hadir
                                    </button>
                                    <button type="button" wire:click="openRescheduleModal({{ $appointment->id }})" class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-bold text-ink transition hover:border-primary hover:text-primary text-center">
                                        Jadwal Ulang
                                    </button>
                                @elseif($appointment->status === 'terkonfirmasi')
                                    <button type="button" wire:click="complete({{ $appointment->id }})" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md hover:shadow-emerald-600/20 text-center">
                                        Selesai Fitting
                                    </button>
                                    <button type="button" wire:click="openRescheduleModal({{ $appointment->id }})" class="w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm font-bold text-ink transition hover:border-primary hover:text-primary text-center">
                                        Jadwal Ulang
                                    </button>
                                @else
                                    <div class="w-full rounded-xl bg-surface border border-border px-4 py-2.5 text-sm font-bold text-muted text-center flex items-center justify-center gap-2">
                                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Selesai
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-16 text-center text-muted font-medium">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-surface mb-4">
                                <svg class="h-8 w-8 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            Tidak ada jadwal fitting pada tanggal ini.
                        </div>
                    @endforelse
                </div>
                
                @if ($appointments->hasPages())
                    <div class="border-t border-border p-4 bg-surface">
                        {{ $appointments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div x-data="{ show: @entangle('showRescheduleModal') }" 
         x-show="show" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div x-show="show" class="fixed inset-0 bg-ink/60 backdrop-blur-sm transition-opacity" @click="show = false"></div>

        <div x-show="show" 
             x-transition
             class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 border border-border dark:bg-stone-800 dark:border-stone-700">
            <form wire:submit.prevent="saveReschedule">
                <div class="bg-white dark:bg-stone-800 p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="text-center sm:text-left w-full">
                            <h3 class="text-xl font-bold leading-6 text-ink dark:text-stone-100 mb-2">
                                Jadwal Ulang Fitting
                            </h3>
                            <p class="text-sm text-muted dark:text-stone-400 mb-6">Pilih tanggal dan waktu baru untuk jadwal fitting ini.</p>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Tanggal Baru</label>
                                    <input type="date" wire:model="rescheduleDate" 
                                           class="block w-full rounded-xl border border-border bg-white text-ink shadow-sm 
                                                  focus:border-primary focus:ring-2 focus:ring-primary/20 sm:text-sm px-4 py-3">
                                    @error('rescheduleDate') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-widest text-muted mb-1.5">Waktu Baru</label>
                                    <input type="time" wire:model="rescheduleTime" 
                                           class="block w-full rounded-xl border border-border bg-white text-ink shadow-sm 
                                                  focus:border-primary focus:ring-2 focus:ring-primary/20 sm:text-sm px-4 py-3">
                                    @error('rescheduleTime') <span class="text-rose-500 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-surface dark:bg-stone-700/50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-border dark:border-stone-700 gap-3">
                    <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20 sm:w-auto">
                        Simpan Perubahan
                    </button>
                    <button type="button" wire:click="closeRescheduleModal" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-ink shadow-sm border border-border hover:border-primary hover:text-primary transition sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

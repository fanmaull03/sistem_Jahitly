<x-slot name="header">
    <div class="flex flex-col">
        <h1 class="text-2xl font-bold text-stone-900">Jadwal Fitting</h1>
        <p class="text-sm text-stone-500">Kelola janji temu fitting dengan pelanggan Anda.</p>
    </div>
</x-slot>

<x-slot name="actions">
    <div class="flex items-center gap-3">
        <button type="button" class="relative flex items-center gap-2 rounded-lg border border-stone-300 bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-200 overflow-hidden cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Pilih Tanggal
            <input type="date" wire:model.live="dateFilter" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
        </button>
        <button type="button" class="flex items-center gap-2 rounded-lg bg-[#0e3b9e] px-4 py-2 text-sm font-semibold text-white hover:bg-blue-900" onclick="alert('Fitur tambah jadwal baru dari admin akan segera hadir.')">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Jadwal Baru
        </button>
    </div>
</x-slot>

<div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-6">
    <!-- Sidebar -->
    <div class="lg:col-span-4 xl:col-span-3 space-y-6">
        <!-- Calendar -->
        <div class="rounded-2xl border border-[#ece4d6] bg-white p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-stone-900">{{ $monthName }}</h3>
                <div class="flex gap-4">
                    <button wire:click="previousMonth" class="text-stone-400 hover:text-stone-900">
                        <svg class="h-4 w-4 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button wire:click="nextMonth" class="text-stone-400 hover:text-stone-900">
                        <svg class="h-4 w-4 font-bold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-stone-500 mb-3">
                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>
            <div class="grid grid-cols-7 gap-y-2 gap-x-1">
                @foreach ($calendarDays as $day)
                    <div class="relative flex justify-center py-1">
                        <button 
                            wire:click="selectDate('{{ $day['date'] }}')"
                            @class([
                                'h-9 w-9 rounded-full flex items-center justify-center text-sm transition-colors font-medium',
                                'text-stone-300' => ! $day['is_current_month'],
                                'text-stone-800 hover:bg-stone-100' => $day['is_current_month'] && $dateFilter !== $day['date'],
                                'bg-[#0e3b9e] text-white font-bold' => $dateFilter === $day['date'],
                            ])
                        >
                            {{ $day['day'] }}
                        </button>
                        @if ($day['has_appointments'])
                            <span @class([
                                'absolute bottom-0 h-1.5 w-1.5 rounded-full',
                                'bg-amber-400' => $dateFilter !== $day['date'],
                                'bg-white' => $dateFilter === $day['date'],
                            ])></span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="font-bold text-stone-900 mb-5">Ringkasan Hari Ini</h3>
            <div class="space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 text-sm font-medium text-stone-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-[#0e3b9e]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        Total Fitting
                    </div>
                    <span class="font-bold text-xl text-stone-900">{{ $summary['total'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 text-sm font-medium text-stone-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        Menunggu
                    </div>
                    <span class="font-bold text-xl text-amber-600">{{ $summary['menunggu'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 text-sm font-medium text-stone-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-stone-100 text-stone-500">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Selesai
                    </div>
                    <span class="font-bold text-xl text-stone-900">{{ $summary['selesai'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-8 xl:col-span-9">
        <div class="rounded-2xl border border-stone-200 bg-white shadow-sm overflow-hidden">
            <!-- Header section of list -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 border-b border-stone-200 bg-[#fdfaf5]">
                <div>
                    <h2 class="text-2xl font-bold text-stone-900">
                        {{ \Carbon\Carbon::parse($dateFilter)->translatedFormat('l, d F Y') }}
                    </h2>
                    <p class="text-sm font-medium text-stone-500 mt-1">{{ $summary['total'] }} Jadwal Fitting Hari Ini</p>
                </div>
                <div class="mt-4 sm:mt-0 flex rounded-lg border border-stone-200 bg-stone-100/50 p-1">
                    <button class="rounded-md bg-white px-5 py-2 text-sm font-bold text-[#0e3b9e] shadow-sm">Daftar</button>
                    <button class="rounded-md px-5 py-2 text-sm font-semibold text-stone-500 hover:text-stone-700">Timeline</button>
                </div>
            </div>

            <!-- List -->
            <div class="divide-y divide-stone-200">
                @forelse ($appointments as $appointment)
                    <div class="flex flex-col sm:flex-row p-6 gap-6 hover:bg-stone-50/50 transition relative">
                        @if($appointment->status === 'menunggu')
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                        @endif
                        
                        <!-- Time -->
                        <div class="w-full sm:w-28 shrink-0 border-b sm:border-b-0 sm:border-r border-stone-200 pb-4 sm:pb-0 sm:pr-6">
                            <div @class([
                                'text-3xl font-extrabold',
                                'text-[#0e3b9e]' => $appointment->status === 'menunggu' || $appointment->status === 'terkonfirmasi',
                                'text-stone-500' => $appointment->status === 'selesai',
                            ])>
                                {{ $appointment->appointment_date->format('H:i') }}
                            </div>
                            <div @class([
                                'text-sm mt-1',
                                'text-[#0e3b9e] font-semibold' => $appointment->status === 'menunggu' || $appointment->status === 'terkonfirmasi',
                                'text-stone-400' => $appointment->status === 'selesai',
                            ])>
                                {{ $appointment->appointment_date->format('H') < 12 ? 'Pagi' : ($appointment->appointment_date->format('H') < 15 ? 'Siang' : 'Sore') }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-bold text-stone-900 text-lg truncate">{{ $appointment->customer->name }}</div>
                                    <div class="text-sm font-medium text-stone-500 mt-1">ID Pesanan: #{{ $appointment->order->order_number }}</div>
                                </div>
                                <div class="shrink-0 ml-4">
                                    <x-status-badge type="appointment" :status="$appointment->status" />
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 mt-4">
                                <span class="inline-flex items-center rounded-md bg-stone-100 px-3 py-1.5 text-xs font-semibold text-stone-600">{{ $appointment->order->service->name }}</span>
                                <span class="inline-flex items-center rounded-md bg-stone-100 px-3 py-1.5 text-xs font-semibold text-stone-600">Fitting</span>
                            </div>

                            @if($appointment->notes)
                                <div class="mt-4 rounded-xl bg-[#fdf6ec] p-4 text-sm font-medium text-orange-800 border border-orange-100/50 relative">
                                    <span class="font-semibold">Catatan:</span> {{ $appointment->notes }}
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="w-full sm:w-40 shrink-0 flex flex-col justify-center gap-3 mt-4 sm:mt-0">
                            @if($appointment->status === 'menunggu')
                                <button type="button" wire:click="confirm({{ $appointment->id }})" class="w-full rounded-lg bg-[#0e3b9e] px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-900 text-center transition shadow-sm">
                                    Konfirmasi Hadir
                                </button>
                                <button type="button" wire:click="openRescheduleModal({{ $appointment->id }})" class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-center transition">
                                    Jadwal Ulang
                                </button>
                            @elseif($appointment->status === 'terkonfirmasi')
                                <button type="button" wire:click="complete({{ $appointment->id }})" class="w-full rounded-lg bg-[#0e3b9e] px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-900 text-center transition shadow-sm">
                                    Mulai Fitting
                                </button>
                                <button type="button" wire:click="openRescheduleModal({{ $appointment->id }})" class="w-full rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm font-bold text-stone-700 hover:bg-stone-50 hover:border-stone-300 text-center transition">
                                    Jadwal Ulang
                                </button>
                            @else
                                <button type="button" disabled class="w-full rounded-lg bg-stone-100 border border-stone-200 px-4 py-2.5 text-sm font-bold text-stone-400 cursor-not-allowed text-center">
                                    Selesai
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center text-stone-500 font-medium">
                        <svg class="mx-auto h-12 w-12 text-stone-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Tidak ada jadwal fitting pada tanggal ini.
                    </div>
                @endforelse
            </div>
            
            @if ($appointments->hasPages())
                <div class="border-t border-stone-200 p-4 bg-stone-50/50">
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
    <div x-show="show" class="fixed inset-0 bg-stone-900/60 transition-opacity" @click="show = false"></div>

    <div x-show="show" 
         x-transition
         class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 border border-stone-100">
        <form wire:submit.prevent="saveReschedule">
            <div class="bg-white px-6 pb-6 pt-6">
                <div class="sm:flex sm:items-start">
                    <div class="text-center sm:text-left w-full">
                        <h3 class="text-xl font-bold leading-6 text-stone-900 mb-2">
                            Jadwal Ulang Fitting
                        </h3>
                        <p class="text-sm text-stone-500 mb-6">Pilih tanggal dan waktu baru untuk jadwal fitting ini.</p>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-1.5">Tanggal Baru</label>
                                <input type="date" wire:model="rescheduleDate" class="block w-full rounded-xl border-stone-200 shadow-sm focus:border-[#0e3b9e] focus:ring-[#0e3b9e] sm:text-sm px-4 py-2.5">
                                @error('rescheduleDate') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-stone-700 mb-1.5">Waktu Baru</label>
                                <input type="time" wire:model="rescheduleTime" class="block w-full rounded-xl border-stone-200 shadow-sm focus:border-[#0e3b9e] focus:ring-[#0e3b9e] sm:text-sm px-4 py-2.5">
                                @error('rescheduleTime') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-stone-50/80 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-stone-100">
                <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-[#0e3b9e] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-900 sm:ml-3 sm:w-auto transition">
                    Simpan Perubahan
                </button>
                <button type="button" wire:click="closeRescheduleModal" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-stone-700 shadow-sm ring-1 ring-inset ring-stone-200 hover:bg-stone-50 hover:ring-stone-300 sm:mt-0 sm:w-auto transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
</div>

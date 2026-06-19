<div class="page-enter mx-auto max-w-4xl space-y-6 px-4 pb-24 sm:px-6 lg:pb-10">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-400 transition hover:text-stone-700 mb-3" wire:navigate>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Pesanan
            </a>
            <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl">Jadwalkan Appointment</h1>
            <p class="mt-1 text-sm text-stone-600">
                Pesanan #{{ $order->order_number }} — {{ $order->service->name }}
            </p>
        </div>
    </div>

    @if ($hasExistingAppointment)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <h2 class="mt-4 text-lg font-bold text-amber-800">Appointment Sudah Ada</h2>
            <p class="mt-2 text-sm text-amber-700">
                Anda sudah memiliki appointment untuk pesanan ini. Silakan cek status di halaman detail pesanan.
            </p>
            <a href="{{ route('orders.show', $order) }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700" wire:navigate>
                Lihat Detail Pesanan
            </a>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-6">
            {{-- Step 1: Pilih Tanggal --}}
            <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#003399] text-sm font-semibold text-white shadow-sm">1</span>
                    <h2 class="text-xl font-bold text-stone-900">Pilih Tanggal</h2>
                </div>

                <div class="max-w-xs">
                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        min="{{ $minDate }}"
                        max="{{ $maxDate }}"
                        class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-3 text-base font-semibold text-stone-900 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition"
                    />
                </div>
                @error('selectedDate')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- Step 2: Pilih Jam --}}
            @if ($selectedDate && count($availableSlots) > 0)
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#003399] text-sm font-semibold text-white shadow-sm">2</span>
                        <h2 class="text-xl font-bold text-stone-900">Pilih Jam</h2>
                    </div>
                    <p class="mb-5 text-sm text-stone-500">
                        Jam operasional: 08:00 - 19:00 · Istirahat: 12:00 - 13:00 · Durasi: ±1 jam
                    </p>

                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5">
                        @foreach ($availableSlots as $slot)
                            @php
                                $isSelected = $selectedHour === $slot['hour'];
                                $isBreak = $slot['label'] === 'Jam Istirahat';
                                $isBooked = $slot['label'] === 'Sudah Terbooking';
                                $isPast = $slot['label'] === 'Sudah Lewat';
                                $isDisabled = !$slot['available'];
                            @endphp

                            <button
                                type="button"
                                @if(!$isDisabled) wire:click="selectSlot({{ $slot['hour'] }})" @endif
                                @class([
                                    'relative flex flex-col items-center justify-center rounded-xl px-3 py-4 text-center transition focus:outline-none',
                                    // Selected
                                    'border-2 border-[#003399] bg-blue-50 text-[#003399] shadow-sm' => $isSelected,
                                    // Available
                                    'border border-stone-200 bg-white text-stone-700 hover:border-[#003399] hover:bg-blue-50/30 cursor-pointer' => !$isDisabled && !$isSelected,
                                    // Break
                                    'border border-stone-100 bg-stone-50 text-stone-300 cursor-not-allowed' => $isBreak,
                                    // Booked
                                    'border border-red-100 bg-red-50 text-red-300 cursor-not-allowed' => $isBooked,
                                    // Past
                                    'border border-stone-100 bg-stone-50 text-stone-300 cursor-not-allowed' => $isPast && !$isBreak && !$isBooked,
                                ])
                                @disabled($isDisabled)
                            >
                                <span class="text-lg font-bold">{{ $slot['time'] }}</span>
                                <span @class([
                                    'mt-1 text-[10px] font-semibold uppercase tracking-wide',
                                    'text-[#003399]' => $isSelected,
                                    'text-emerald-600' => $slot['available'] && !$isSelected,
                                    'text-stone-400' => ($isBreak || $isPast) && !$isBooked,
                                    'text-red-400' => $isBooked,
                                ])>
                                    @if ($isSelected)
                                        ✓ Dipilih
                                    @else
                                        {{ $slot['label'] }}
                                    @endif
                                </span>

                                @if ($isSelected)
                                    <div class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-[#003399] text-white">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    @error('selectedHour')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Legend --}}
                    <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-stone-100 pt-4 text-xs text-stone-500">
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full border border-stone-200 bg-white"></span>
                            Tersedia
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-red-50 border border-red-200"></span>
                            Terbooking
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-stone-100 border border-stone-200"></span>
                            Istirahat / Lewat
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-blue-50 border-2 border-[#003399]"></span>
                            Dipilih
                        </span>
                    </div>
                </section>
            @endif

            {{-- Step 3: Catatan --}}
            @if ($selectedHour)
                <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[#003399] text-sm font-semibold text-white shadow-sm">3</span>
                        <h2 class="text-xl font-bold text-stone-900">Catatan (Opsional)</h2>
                    </div>
                    <textarea
                        rows="3"
                        wire:model="notes"
                        class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600 transition"
                        placeholder="Contoh: Saya akan datang bersama rekan, tolong sediakan ruangan yang lebih luas..."
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                {{-- Summary & Submit --}}
                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-emerald-900">Konfirmasi Jadwal</h3>
                            <p class="mt-1 text-sm text-emerald-800">
                                <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong>
                                pukul <strong>{{ sprintf('%02d:00', $selectedHour) }} - {{ sprintf('%02d:00', $selectedHour + 1) }}</strong> WIB
                            </p>
                            <p class="mt-1 text-xs text-emerald-600">Appointment akan menunggu konfirmasi admin.</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-4">
                        <a href="{{ route('orders.show', $order) }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900 transition" wire:navigate>
                            Nanti Saja
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#003399] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:ring-offset-2 disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="submit">Konfirmasi Jadwal</span>
                            <span wire:loading wire:target="submit">Memproses...</span>
                            <svg wire:loading.remove wire:target="submit" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </section>
            @endif
        </form>
    @endif
</div>

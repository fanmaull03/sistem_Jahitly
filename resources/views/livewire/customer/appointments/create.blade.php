<div class="page-enter mx-auto max-w-4xl space-y-6 px-4 pb-24 sm:px-6 lg:pb-10">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-muted transition hover:text-primary mb-3 dark:text-stone-400 dark:hover:text-stone-300" wire:navigate>
                ← Kembali ke Pesanan
            </a>
            <h1 class="font-display text-3xl font-bold text-ink dark:text-stone-100">Jadwalkan Appointment</h1>
            <p class="mt-1 text-sm text-muted dark:text-stone-400">
                Pesanan #{{ $order->order_number }} — {{ $order->service->name }}
            </p>
        </div>
    </div>

    @if ($hasExistingAppointment)
        <div class="rounded-2xl border border-accent/20 bg-accent-soft p-6 text-center dark:border-amber-800 dark:bg-amber-900/30">
            <svg class="mx-auto h-12 w-12 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <h2 class="mt-4 text-lg font-bold text-ink dark:text-amber-300">Appointment Sudah Ada</h2>
            <p class="mt-2 text-sm text-muted dark:text-amber-400">
                Anda sudah memiliki appointment untuk pesanan ini. Silakan cek status di halaman detail pesanan.
            </p>
            <a href="{{ route('orders.show', $order) }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover" wire:navigate>
                Lihat Detail Pesanan
            </a>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-6">
            {{-- Step 1: Pilih Tanggal --}}
            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm sm:p-8 dark:border-stone-700 dark:bg-stone-800">
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">1</span>
                    <h2 class="text-xl font-bold text-ink dark:text-stone-100">Pilih Tanggal</h2>
                </div>

                <div class="max-w-xs">
                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        min="{{ $minDate }}"
                        max="{{ $maxDate }}"
                        class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-base font-semibold text-ink focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition dark:bg-stone-900 dark:text-stone-100 dark:border-stone-600 dark:focus:bg-stone-800"
                    />
                </div>
                @error('selectedDate')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            {{-- Step 2: Pilih Jam --}}
            @if ($selectedDate && count($availableSlots) > 0)
                <section class="rounded-2xl border border-border bg-white p-6 shadow-sm sm:p-8 dark:border-stone-700 dark:bg-stone-800">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">2</span>
                        <h2 class="text-xl font-bold text-ink dark:text-stone-100">Pilih Jam</h2>
                    </div>
                    <p class="mb-5 text-sm text-muted dark:text-stone-400">
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
                                    'border-2 border-primary bg-primary/5 text-primary shadow-sm dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500' => $isSelected,
                                    // Available
                                    'border border-border bg-white text-ink hover:border-primary/50 hover:bg-primary/5 cursor-pointer dark:bg-stone-800 dark:border-stone-700 dark:text-stone-300 dark:hover:border-blue-500 dark:hover:bg-blue-900/20' => !$isDisabled && !$isSelected,
                                    // Break
                                    'border border-border bg-surface text-muted/50 cursor-not-allowed dark:border-stone-700 dark:bg-stone-900/50 dark:text-stone-600' => $isBreak,
                                    // Booked
                                    'border border-red-200 bg-red-50 text-red-800/80 cursor-not-allowed dark:border-red-900/50 dark:bg-red-900/10 dark:text-red-800/80' => $isBooked,
                                    // Past
                                    'border border-border bg-surface text-muted/50 cursor-not-allowed dark:border-stone-700 dark:bg-stone-900/50 dark:text-stone-600' => $isPast && !$isBreak && !$isBooked,
                                ])
                                @disabled($isDisabled)
                            >
                                <span class="text-lg font-bold">{{ $slot['time'] }}</span>
                                <span @class([
                                    'mt-1 text-[10px] font-semibold uppercase tracking-wide',
                                    'text-primary' => $isSelected,
                                    'text-emerald-600 dark:text-emerald-500' => $slot['available'] && !$isSelected,
                                    'text-muted/70 dark:text-stone-600' => ($isBreak || $isPast) && !$isBooked,
                                    'text-red-600 dark:text-red-700' => $isBooked,
                                ])>
                                    @if ($isSelected)
                                        ✓ Dipilih
                                    @else
                                        {{ $slot['label'] }}
                                    @endif
                                </span>

                                @if ($isSelected)
                                    <div class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-white">
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
                    <div class="mt-5 flex flex-wrap items-center gap-4 border-t border-border pt-4 text-xs text-muted dark:border-stone-700 dark:text-stone-400">
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full border border-border bg-white dark:bg-stone-800 dark:border-stone-600"></span>
                            Tersedia
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800"></span>
                            Terbooking
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-surface border border-border dark:bg-stone-800 dark:border-stone-700"></span>
                            Istirahat / Lewat
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-3 w-3 rounded-full bg-primary/20 border-2 border-primary dark:bg-blue-900/30 dark:border-blue-500"></span>
                            Dipilih
                        </span>
                    </div>
                </section>
            @endif

            {{-- Step 3: Catatan --}}
            @if ($selectedHour)
                <section class="rounded-2xl border border-border bg-white p-6 shadow-sm sm:p-8 dark:border-stone-700 dark:bg-stone-800">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-accent text-sm font-bold text-white shadow-sm shadow-accent/25">3</span>
                        <h2 class="text-xl font-bold text-ink dark:text-stone-100">Catatan (Opsional)</h2>
                    </div>
                    <textarea
                        rows="3"
                        wire:model="notes"
                        class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-1 focus:ring-primary transition dark:bg-stone-900 dark:border-stone-600 dark:text-stone-100 dark:placeholder-stone-500"
                        placeholder="Contoh: Saya akan datang bersama rekan, tolong sediakan ruangan yang lebih luas..."
                    ></textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                {{-- Summary & Submit --}}
                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm dark:border-emerald-800 dark:bg-emerald-900/30">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-300">Konfirmasi Jadwal</h3>
                            <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-400">
                                <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</strong>
                                pukul <strong>{{ sprintf('%02d:00', $selectedHour) }} - {{ sprintf('%02d:00', $selectedHour + 1) }}</strong> WIB
                            </p>
                            <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-500">Appointment akan menunggu konfirmasi admin.</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-4">
                        <a href="{{ route('orders.show', $order) }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-900 transition dark:text-emerald-400 dark:hover:text-emerald-300" wire:navigate>
                            Nanti Saja
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-bold text-white shadow-md shadow-primary/25 transition hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
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

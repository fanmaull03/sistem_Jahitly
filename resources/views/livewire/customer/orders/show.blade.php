<div class="page-enter mx-auto max-w-6xl space-y-6 px-4 pb-24 sm:px-6 lg:pb-10">
    {{-- ── Header ── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-stone-400 transition hover:text-stone-700 mb-3" wire:navigate>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Pesanan
            </a>
            <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl">Lacak Pesanan</h1>
            <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
                <span class="font-semibold text-orange-600">ID: #{{ $order->order_number }}</span>
                <span class="text-stone-300">•</span>
                <span class="text-stone-500">Estimasi Selesai: <strong class="text-stone-700">{{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : 'Menunggu' }}</strong></span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @php
                $statusBadge = match(true) {
                    in_array($order->status, ['menunggu_konfirmasi', 'menunggu_fitting', 'menunggu_dp', 'menunggu_bahan']) => [
                        'label' => 'MENUNGGU',
                        'class' => 'bg-gradient-to-r from-amber-400 to-orange-500 text-white',
                    ],
                    in_array($order->status, ['dalam_antrian', 'dijahit']) => [
                        'label' => 'SEDANG DIKERJAKAN',
                        'class' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
                    ],
                    in_array($order->status, ['selesai_produksi', 'siap_diambil']) => [
                        'label' => 'SIAP DIAMBIL / LUNASI',
                        'class' => 'bg-gradient-to-r from-teal-500 to-teal-600 text-white',
                    ],
                    $order->status === 'selesai' => [
                        'label' => 'SELESAI',
                        'class' => 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white',
                    ],
                    in_array($order->status, ['ditolak', 'dibatalkan']) => [
                        'label' => strtoupper($order->status),
                        'class' => 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                    ],
                    default => [
                        'label' => strtoupper(str_replace('_', ' ', $order->status)),
                        'class' => 'bg-stone-200 text-stone-700',
                    ],
                };
            @endphp

            <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold tracking-wider shadow-sm {{ $statusBadge['class'] }}">
                {{ $statusBadge['label'] }}
            </span>

            @if ($canCancel)
                <a href="{{ route('orders.cancel', $order) }}" class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20 transition hover:bg-red-100" wire:navigate>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batalkan
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 mb-6">
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ── Aksi Konfirmasi Pengiriman Pakaian (Vermak) ── --}}
    @if ($order->status === 'menunggu_pakaian_dikirim')
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div>
                    <h2 class="text-lg font-bold text-amber-900">Menunggu Pakaian Dikirim</h2>
                    <p class="mt-1 text-sm text-amber-800">Silakan kirim atau antar langsung pakaian yang akan divermak ke workshop kami. Klik tombol di samping jika Anda sudah menyerahkannya.</p>
                </div>
                <button 
                    type="button" 
                    wire:click="confirmClothesSent"
                    class="w-full shrink-0 rounded-xl bg-amber-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-amber-700 sm:w-auto shadow-md hover-lift hover:shadow-lg"
                >
                    Konfirmasi Pakaian Dikirim
                </button>
            </div>
        </div>
    @endif

    {{-- ── Aksi Konfirmasi Selesai ── --}}
    @if ($order->status === 'siap_diambil')
        <div x-data="{ showConfirmModal: false }" class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div>
                    <h2 class="text-lg font-bold text-blue-900">Pesanan Siap Diambil!</h2>
                    <p class="mt-1 text-sm text-blue-800">Jika Anda sudah mengambil pesanan ini di workshop atau sudah menerima kiriman, silakan klik tombol konfirmasi.</p>
                </div>
                <button 
                    type="button" 
                    @click="showConfirmModal = true"
                    class="w-full shrink-0 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700 sm:w-auto shadow-md hover-lift hover:shadow-lg"
                >
                    Konfirmasi Pesanan Selesai
                </button>
            </div>

            {{-- ── Custom Confirm Modal ── --}}
            <div 
                x-show="showConfirmModal" 
                style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm transition-opacity"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <div 
                    @click.outside="showConfirmModal = false"
                    class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 shadow-2xl transition-all"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div class="flex items-center justify-center mb-4 h-16 w-16 rounded-full bg-blue-50 mx-auto">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    
                    <h3 class="text-center text-xl font-extrabold text-slate-900">Konfirmasi Penyelesaian</h3>
                    <p class="mt-2 text-center text-sm text-slate-500">
                        Apakah Anda yakin pesanan sudah diambil dan selesai? Tindakan ini tidak dapat dibatalkan dan status pesanan akan menjadi "Selesai".
                    </p>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button 
                            type="button" 
                            @click="showConfirmModal = false"
                            class="w-full rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:w-auto transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="button" 
                            wire:click="markAsCompleted"
                            @click="showConfirmModal = false"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 sm:w-auto shadow-md transition-colors hover:shadow-lg"
                        >
                            Ya, Sudah Diambil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Testimonial Form ── --}}
    @if ($order->status === 'selesai' && !$order->testimonial)
        <div class="mb-6 rounded-2xl border border-orange-200 bg-orange-50 p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-orange-900">Beri Penilaian Pesanan</h2>
                <p class="mt-1 text-sm text-orange-800">Bagaimana hasil jahitan kami? Penilaian Anda tidak wajib dan bisa dilakukan kapan saja, namun sangat berarti bagi kami!</p>
            </div>
            
            <div class="flex flex-col gap-4 max-w-2xl">
                <div>
                    <label class="block text-sm font-medium text-orange-900 mb-2">Rating (Bintang)</label>
                    <div class="flex items-center gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button 
                                type="button" 
                                wire:click="$set('rating', {{ $i }})"
                                class="transition-transform hover:scale-110 focus:outline-none"
                            >
                                <svg class="h-8 w-8 {{ $rating >= $i ? 'text-amber-400' : 'text-orange-200 hover:text-amber-300' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    @error('rating') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-orange-900 mb-2">Ulasan Singkat</label>
                    <textarea 
                        wire:model="review" 
                        rows="3" 
                        class="w-full rounded-xl border border-orange-200 bg-white px-4 py-2.5 text-sm focus:border-orange-400 focus:outline-none focus:ring-1 focus:ring-orange-400"
                        placeholder="Ceritakan pengalaman Anda dengan layanan kami..."
                    ></textarea>
                    @error('review') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button 
                        type="button" 
                        wire:click="submitTestimonial"
                        class="rounded-xl bg-orange-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-orange-700 shadow-md hover-lift hover:shadow-lg"
                    >
                        Kirim Penilaian
                    </button>
                </div>
            </div>
        </div>
    @elseif ($order->testimonial)
        <div class="mb-6 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-bold text-stone-900 mb-3">Penilaian Anda</h2>
            <div class="flex items-center gap-1 mb-2">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="h-4 w-4 {{ $order->testimonial->rating >= $i ? 'text-amber-400' : 'text-stone-200' }}" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
            @if ($order->testimonial->review)
                <p class="text-sm text-stone-600 italic">"{{ $order->testimonial->review }}"</p>
            @endif
        </div>
    @endif

    {{-- ── Main Grid ── --}}
    <div class="grid gap-6 lg:grid-cols-5">
        {{-- ── Left: Status Produksi (3 cols) ── --}}
        <div class="lg:col-span-3">
            <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-lg font-bold text-stone-900">Status Produksi</h2>

                <div class="mt-8 flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach ($statusSteps as $index => $step)
                            @php
                                $isCompleted = $index < $currentStepIndex || ($step['key'] === 'selesai' && $index === $currentStepIndex);
                                $isActive    = $index === $currentStepIndex;
                                $isLast      = $loop->last;
                                $matchedLog  = $statusLogs->firstWhere('status', $step['key']);
                            @endphp
                            <li>
                                <div class="relative pb-8">
                                    {{-- Connector line --}}
                                    @if (!$isLast)
                                        <span class="absolute left-4 top-8 -ml-px h-full w-0.5 {{ $isCompleted ? 'bg-gradient-to-b from-orange-400 to-orange-500' : 'bg-stone-200' }}" aria-hidden="true"></span>
                                    @endif

                                    <div class="relative flex items-start gap-4">
                                        {{-- Step indicator --}}
                                        <div class="relative z-10">
                                            @if ($isCompleted)
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-orange-400 to-orange-600 shadow-sm shadow-orange-200">
                                                    <svg class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            @elseif ($isActive)
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-blue-700 shadow-md shadow-blue-200 ring-4 ring-blue-50">
                                                    <span class="h-2 w-2 rounded-full bg-white"></span>
                                                </span>
                                            @else
                                                <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-stone-200 bg-white">
                                                    <span class="h-2 w-2 rounded-full bg-stone-300"></span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Step content --}}
                                        <div class="flex-1 pt-0.5">
                                            <p class="text-sm font-bold {{ $isActive ? 'text-stone-900' : ($isCompleted ? 'text-stone-800' : 'text-stone-400') }}">
                                                {{ $step['label'] }}
                                            </p>

                                            @if ($matchedLog)
                                                <p class="mt-0.5 text-xs text-stone-500">
                                                    @if ($isCompleted && $matchedLog->notes)
                                                        {{ $matchedLog->notes }} · {{ $matchedLog->created_at->format('d M Y') }}
                                                    @else
                                                        {{ $matchedLog->created_at->format('d M Y, H:i') }} WIB
                                                    @endif
                                                </p>
                                            @elseif (!$isCompleted && !$isActive)
                                                <p class="mt-0.5 text-xs text-stone-400 italic">{{ $step['description'] ?? '' }}</p>
                                            @endif

                                            @if ($isActive)
                                                {{-- Active step highlight --}}
                                                <div class="mt-3 rounded-xl bg-blue-50 p-4 ring-1 ring-inset ring-blue-100">
                                                    <p class="text-sm text-blue-800">
                                                        {{ $step['description'] ?? 'Pakaian Anda sedang ditangani oleh Penjahit Ahli kami.' }}
                                                    </p>

                                                    {{-- Progress bar --}}
                                                    <div class="mt-4">
                                                        <div class="flex items-center justify-between text-xs">
                                                            <span class="font-medium text-blue-700">Progress</span>
                                                            <span class="font-bold text-blue-800">{{ $progressPercent }}%</span>
                                                        </div>
                                                        <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-blue-100">
                                                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>

        {{-- ── Right: Info Cards (2 cols) ── --}}
        <aside class="space-y-5 lg:col-span-2">
            {{-- ── Informasi Pakaian ── --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-400">Informasi Pakaian</h3>

                <div class="mt-4 flex items-start gap-4">
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-stone-100 bg-stone-100">
                        @if ($order->service->image_path ?? false)
                            <img src="{{ asset('storage/' . $order->service->image_path) }}" alt="preview" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <svg class="h-8 w-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-base font-bold text-stone-900">{{ $order->service->name }}</p>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            @if ($order->service->type ?? false)
                                <span class="inline-flex rounded-md bg-orange-50 px-2 py-0.5 text-[11px] font-semibold text-orange-700">{{ $order->service->type }}</span>
                            @endif
                            @if ($order->service->description ?? false)
                                <span class="inline-flex rounded-md bg-stone-100 px-2 py-0.5 text-[11px] font-medium text-stone-600">{{ \Illuminate\Support\Str::limit($order->service->description, 30) }}</span>
                            @endif
                        </div>
                        <span class="mt-1.5 inline-flex rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-blue-700">{{ $order->quantity }} Pcs</span>
                    </div>
                </div>

                @if ($order->service->type === 'vermak')
                    <div class="mt-4 space-y-3 border-t border-stone-100 pt-4">
                        <div class="flex items-start gap-3 text-sm">
                            <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 14.25l-2.25 2.25m-4.5 0L5.25 14.25M12 12l2.25-2.25m0-4.5l-4.5 4.5M12 12V3" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <span class="font-medium text-stone-500">Rincian Vermak</span>
                                @php
                                    $vermakDetails = json_decode($order->alteration_details, true) ?? [];
                                @endphp
                                @if (count($vermakDetails) > 0)
                                    <ul class="mt-1 space-y-1">
                                        @foreach ($vermakDetails as $detail)
                                            <li class="flex justify-between font-bold text-stone-800 text-xs">
                                                <span>- {{ $detail['name'] ?? 'Vermak' }}</span>
                                                <span class="text-blue-700">+Rp {{ number_format($detail['price'] ?? 0, 0, ',', '.') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="mt-1 font-bold text-stone-800">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Material info --}}
                    <div class="mt-4 space-y-3 border-t border-stone-100 pt-4">
                        <div class="flex items-start gap-3 text-sm">
                            <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <span class="font-medium text-stone-500">Sumber Bahan</span>
                                    <span class="font-bold text-stone-800 text-right">
                                        {{ $order->material_source === 'customer' ? 'Bawa Sendiri' : ($order->material_source === 'jasa' ? 'Beli di Penjahit' : '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($order->notes)
                            <div class="flex items-start gap-3 text-sm">
                                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <span class="block font-medium text-stone-500">Catatan Tambahan</span>
                                    <p class="mt-1 font-bold text-stone-800">{{ $order->notes }}</p>
                                </div>
                            </div>
                        @endif

                        @if ($order->fabric)
                            <div class="flex items-start gap-4 text-sm mt-3">
                                @if ($order->fabric->image_path)
                                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-stone-200">
                                        <img src="{{ Storage::url($order->fabric->image_path) }}" class="h-full w-full object-cover" alt="{{ $order->fabric->name }}">
                                    </div>
                                @else
                                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-stone-100 text-stone-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <span class="block font-medium text-stone-500">Bahan Kain</span>
                                    <p class="mt-1 font-bold text-stone-800">{{ $order->fabric->name }} — {{ $order->fabric->color }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        <span class="rounded-md bg-stone-100 px-1.5 py-0.5 text-[10px] font-medium text-stone-600">{{ $order->fabric->category_label }}</span>
                                        <span class="text-[10px] text-stone-400">Rp {{ number_format((float) $order->fabric->price_per_meter, 0, ',', '.') }}/m</span>
                                        @if ($order->fabric->stock_status === 'tersedia')
                                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Ready</span>
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">PO ~{{ $order->fabric->po_days }} hari</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ── Total Biaya ── --}}
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-stone-500">Total Biaya Keseluruhan</span>
                    <span class="text-xl font-bold text-stone-900">Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}</span>
                </div>

                @if ($order->dp_amount && $order->service->type !== 'vermak')
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-stone-500">Tagihan DP</span>
                        <span class="text-base font-semibold text-stone-700">Rp {{ number_format((float) $order->dp_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                
                @php
                    $totalPaid = $order->payments->where('status', 'terverifikasi')->sum('amount');
                    $remaining = max(0, (float) $order->estimated_price - $totalPaid);
                @endphp

                @if ($totalPaid > 0)
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-stone-500">Sudah Dibayar</span>
                        <span class="text-base font-semibold text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                    </div>
                @endif
                
                @if ($remaining > 0 && $order->payment_status !== 'belum_bayar')
                    <div class="mt-2 flex items-center justify-between border-t border-stone-100 pt-2">
                        <span class="text-sm font-medium text-stone-500">Sisa Pelunasan</span>
                        <span class="text-base font-bold text-rose-600">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                @endif

                @php
                    $paymentLabels = [
                        'belum_bayar' => 'Belum Lunas',
                        'menunggu'    => 'Menunggu Verifikasi',
                        'dp'          => 'DP Terbayar',
                        'lunas'       => $order->service->type === 'vermak' ? 'Lunas' : 'Lunas (DP + Pelunasan)',
                    ];
                    $paymentBadgeClass = match($order->payment_status) {
                        'lunas'       => 'text-emerald-700 bg-emerald-50',
                        'dp'          => 'text-blue-700 bg-blue-50',
                        'menunggu'    => 'text-amber-700 bg-amber-50',
                        default       => 'text-red-700 bg-red-50',
                    };
                @endphp

                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs text-stone-400">Status Pembayaran</span>
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $paymentBadgeClass }}">
                        {{ $paymentLabels[$order->payment_status] ?? 'Belum Lunas' }}
                    </span>
                </div>
            </div>

            {{-- ── Design Files ── --}}
            @if ($order->designFiles->isNotEmpty())
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-stone-400">File Desain</h3>
                    <div class="mt-3 space-y-2">
                        @foreach ($order->designFiles as $file)
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="group flex items-center gap-3 rounded-xl border border-stone-100 p-3 transition hover:border-orange-200 hover:bg-orange-50/40">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-stone-100 text-stone-500 transition group-hover:bg-orange-100 group-hover:text-orange-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-stone-800">{{ $file->original_filename }}</p>
                                    <p class="text-xs text-stone-400 group-hover:text-orange-600">Lihat Gambar &rarr;</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            {{-- ── Appointment Info ── --}}
            @if ($this->needsAppointment)
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-stone-400">Appointment</h3>

                    @if ($order->appointment)
                        @php
                            $apptBadge = match($order->appointment->status) {
                                'menunggu' => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-amber-100 text-amber-700'],
                                'terkonfirmasi' => ['label' => 'Terkonfirmasi', 'class' => 'bg-blue-100 text-blue-700'],
                                'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700'],
                                'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'],
                                default => ['label' => ucfirst($order->appointment->status), 'class' => 'bg-stone-100 text-stone-700'],
                            };
                        @endphp

                        <div class="mt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-stone-500">Jadwal</span>
                                <span class="text-sm font-bold text-stone-900">
                                    {{ $order->appointment->appointment_date->translatedFormat('l, d M Y H:i') }} WIB
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-stone-500">Status</span>
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $apptBadge['class'] }}">
                                    {{ $apptBadge['label'] }}
                                </span>
                            </div>
                            @if ($order->appointment->notes)
                                <div class="mt-2 border-t border-stone-100 pt-2">
                                    <span class="text-xs font-medium text-stone-500">Catatan:</span>
                                    <p class="mt-0.5 text-sm text-stone-700">{{ $order->appointment->notes }}</p>
                                </div>
                            @endif
                        </div>

                        @if ($order->appointment->status === 'selesai' && !$this->canPay)
                            <div class="mt-3 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-700">
                                Appointment selesai. Silakan lakukan pembayaran.
                            </div>
                        @endif
                    @else
                        <div class="mt-3 flex flex-col items-center text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-stone-700">Belum ada appointment</p>
                            <p class="mt-1 text-xs text-stone-500">Buat appointment untuk konsultasi desain.</p>
                            <a href="{{ route('orders.appointments.create', $order) }}"
                               class="mt-3 inline-flex items-center gap-1.5 rounded-xl bg-[#003399] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-800"
                               wire:navigate>
                                Buat Appointment
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </aside>
    </div>

    {{-- ── Bottom Action Buttons ── --}}
    <div class="flex flex-col items-center justify-center gap-3 border-t border-stone-100 pt-6 sm:flex-row">
        @if ($order->status === 'selesai')
            <a href="{{ route('orders.invoice', $order) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-stone-300 bg-white px-6 py-3 text-sm font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50"
               target="_blank">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Lihat Invoice
            </a>
        @endif

        @php
            $waUrl = 'https://wa.me/6281234567890?text=' . urlencode("Halo CS Jahitly, saya ingin bertanya tentang pesanan #{$order->order_number}");
        @endphp
        <a href="{{ $waUrl }}" target="_blank"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-700 hover:shadow-lg hover-lift">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Hubungi CS (WhatsApp)
        </a>
    </div>

    {{-- ── Floating Payment Bar ── --}}
    @if ($order->payment_status !== 'lunas' && $this->canPay)
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white/95 p-4 shadow-[0_-4px_16px_rgb(0,0,0,0.06)] backdrop-blur-sm lg:sticky lg:bottom-6 lg:mt-6 lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm lg:backdrop-blur-0">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-center sm:text-left">
                    <p class="text-sm font-bold text-stone-900">Tagihan Pembayaran</p>
                    <p class="mt-0.5 text-xs text-stone-500">
                        @if ($hasPendingPayment)
                            Ada pembayaran yang sedang menunggu verifikasi admin.
                        @else
                            Silakan lakukan pembayaran agar pesanan bisa segera diproses.
                        @endif
                    </p>
                </div>

                <a
                    href="{{ route('payments.create', $order) }}"
                    class="w-full rounded-xl bg-blue-600 px-6 py-3 text-center text-sm font-bold text-white transition hover:bg-blue-700 sm:w-auto {{ $hasPendingPayment ? 'pointer-events-none opacity-50 grayscale' : 'hover-lift shadow-md hover:shadow-lg' }}"
                    wire:navigate
                >
                    Bayar Sekarang
                </a>
            </div>
        </div>
    @elseif ($order->payment_status !== 'lunas' && $this->needsAppointment && !$this->canPay)
        {{-- Info bar: appointment belum selesai --}}
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-amber-200 bg-amber-50/95 p-4 shadow-[0_-4px_16px_rgb(0,0,0,0.06)] backdrop-blur-sm lg:sticky lg:bottom-6 lg:mt-6 lg:rounded-2xl lg:border lg:p-6 lg:shadow-sm lg:backdrop-blur-0">
            <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="text-center sm:text-left">
                    <p class="text-sm font-bold text-amber-900">Pembayaran Belum Tersedia</p>
                    <p class="mt-0.5 text-xs text-amber-700">
                        @if (!$order->appointment)
                            Buat appointment terlebih dahulu untuk konsultasi desain.
                        @else
                            Pembayaran dapat dilakukan setelah appointment selesai.
                        @endif
                    </p>
                </div>
                @if (!$order->appointment)
                    <a href="{{ route('orders.appointments.create', $order) }}"
                       class="w-full rounded-xl bg-amber-600 px-6 py-3 text-center text-sm font-bold text-white transition hover:bg-amber-700 sm:w-auto hover-lift shadow-md hover:shadow-lg"
                       wire:navigate>
                        Buat Appointment
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

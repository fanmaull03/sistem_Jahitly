<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth scroll-pt-24">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Jahitly') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="bg-surface text-ink antialiased font-sans">
        <header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="mt-4 flex items-center justify-between rounded-2xl border border-white/30 bg-white/85 px-5 py-3.5 shadow-md backdrop-blur-md ring-1 ring-black/5">
                    <div class="font-display text-xl font-bold tracking-tight text-ink">Jahit<span class="text-primary">ly</span></div>
                    <nav class="hidden items-center gap-8 text-sm font-medium text-ink/70 md:flex">
                        <a href="#layanan" class="hover:text-ink transition">Layanan</a>
                        <a href="#cara-kerja" class="hover:text-ink transition">Cara Kerja</a>
                        <a href="#testimoni" class="hover:text-ink transition">Testimoni</a>
                        <a href="#faq" class="hover:text-ink transition">FAQ</a>
                    </nav>
                    <div class="hidden items-center gap-4 md:flex">
                        @auth
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('orders.index') }}" class="text-sm font-semibold text-ink/70 hover:text-ink transition">
                                {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-ink/70 hover:text-ink transition">Masuk</a>
                        @endauth
                        <a
                            href="{{ route('orders.create') }}"
                            class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md"
                        >
                            Buat Pesanan
                        </a>
                    </div>
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center justify-center rounded-full border border-border bg-white px-3 py-2 text-sm font-semibold text-ink md:hidden"
                        aria-label="Toggle menu"
                    >
                        Menu
                    </button>
                </div>
                <div x-cloak x-show="open" x-transition class="mt-3 rounded-2xl border border-border bg-white p-4 shadow-lg md:hidden">
                    <nav class="flex flex-col gap-3 text-sm font-medium text-ink/70">
                        <a href="#layanan" @click="open = false" class="hover:text-ink">Layanan</a>
                        <a href="#cara-kerja" @click="open = false" class="hover:text-ink">Cara Kerja</a>
                        <a href="#testimoni" @click="open = false" class="hover:text-ink">Testimoni</a>
                        <a href="#faq" @click="open = false" class="hover:text-ink">FAQ</a>
                        <div class="flex items-center gap-3 pt-2">
                            @auth
                                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('orders.index') }}" class="text-sm font-semibold text-ink/70 hover:text-ink">
                                    {{ auth()->user()->name }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-semibold text-ink/70 hover:text-ink">Masuk</a>
                            @endauth
                            <a
                                href="{{ route('orders.create') }}"
                                class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                            >
                                Buat Pesanan
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        {{-- ── HERO ── --}}
        <section class="relative overflow-hidden bg-ink min-h-screen flex items-center">
            <!-- Background dot pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 24px 24px;"></div>
            <!-- Accent blob kiri bawah -->
            <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-primary/20 blur-3xl"></div>
            <!-- Accent blob kanan atas -->
            <div class="absolute -top-20 right-1/3 h-64 w-64 rounded-full bg-accent/15 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-6xl grid-cols-1 gap-12 px-4 py-32 sm:px-6 lg:grid-cols-2 lg:items-center">
                <!-- LEFT: Text -->
                <div data-reveal class="space-y-7">
                    <span class="inline-flex items-center gap-2 rounded-full border border-accent/30 bg-accent/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-accent">
                        ✦ Jahit Rapi, Proses Transparan
                    </span>

                    <h1 class="font-display text-5xl font-extrabold leading-[1.1] text-white sm:text-6xl">
                        Jahit Lebih Pasti,<br>
                        <span class="text-accent">Tanpa Repot</span> Antri.
                    </h1>

                    <p class="text-base text-white/60 leading-relaxed sm:text-lg max-w-md">
                        Dari vermak harian hingga seragam keluarga — pesan jasa jahit terpercaya dan pantau prosesnya real-time dari HP Anda.
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('orders.create') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/30 transition hover:bg-primary-hover hover:shadow-primary/50 btn-glow">
                            Mulai Pesanan
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                            </svg>
                        </a>
                        <a href="#layanan"
                           class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-7 py-3.5 text-sm font-semibold text-white/80 transition hover:border-white/40 hover:text-white">
                            Lihat Layanan
                        </a>
                    </div>

                    <!-- Trust signals -->
                    <div class="flex items-center gap-4 pt-2">
                        <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-full border-2 border-ink bg-primary/30 ring-1 ring-white/10"></div>
                            <div class="h-8 w-8 rounded-full border-2 border-ink bg-accent/30 ring-1 ring-white/10"></div>
                            <div class="h-8 w-8 rounded-full border-2 border-ink bg-emerald-400/30 ring-1 ring-white/10"></div>
                        </div>
                        <p class="text-xs text-white/50">200+ pelanggan puas bulan ini</p>
                    </div>
                </div>

                <!-- RIGHT: Image -->
                <div data-reveal data-reveal-delay="2" class="relative hidden lg:block">
                    <div class="relative h-[520px] overflow-hidden rounded-3xl">
                        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Jahitly" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/40 to-transparent"></div>
                    </div>
                    <!-- Floating card — stat -->
                    <div class="absolute -bottom-6 -left-6 rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-md">
                        <div class="text-2xl font-bold text-white">98%</div>
                        <div class="text-xs text-white/60">Pelanggan puas</div>
                    </div>
                    <!-- Floating card — badge -->
                    <div class="absolute -right-4 top-10 rounded-2xl border border-white/10 bg-white/10 p-3 backdrop-blur-md">
                        <div class="text-xs font-semibold text-accent">✓ Tracking Real-time</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── LAYANAN ── --}}
        <section id="layanan" class="bg-surface py-24">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div data-reveal class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-widest text-primary">Layanan</p>
                        <h2 class="mt-1 font-display text-4xl font-bold text-ink">Yang Kami Kerjakan</h2>
                    </div>
                    <p class="max-w-xs text-sm text-muted">Semua pesanan tercatat dan bisa dipantau kapan saja.</p>
                </div>

                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    <!-- Card 1: Vermak -->
                    <div data-reveal class="group relative overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="h-1.5 w-full bg-primary"></div>
                        <div class="h-52 overflow-hidden">
                            <div class="h-full w-full bg-stone-200 bg-cover bg-center transition duration-500 group-hover:scale-105" style="background-image: url('{{ asset('images/service-vermak.jpg') }}');"></div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="inline-block rounded-full bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-primary">Vermak Harian</span>
                            <h3 class="text-xl font-bold text-ink">Perbaikan cepat untuk baju favorit.</h3>
                            <p class="text-sm text-muted leading-relaxed">Proses cepat tanpa antre panjang. Hasil rapi dan kuat.</p>
                            <ul class="space-y-1.5 text-sm text-ink/70">
                                <li class="flex items-center gap-2"><span class="text-primary">✓</span> Tanpa DP</li>
                                <li class="flex items-center gap-2"><span class="text-primary">✓</span> Jahitan kuat & rapi</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: Seragam -->
                    <div data-reveal data-reveal-delay="1" class="group relative overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="h-1.5 w-full bg-accent"></div>
                        <div class="h-52 overflow-hidden">
                            <div class="h-full w-full bg-stone-200 bg-cover bg-center transition duration-500 group-hover:scale-105" style="background-image: url('{{ asset('images/service-seragam.jpg') }}');"></div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="inline-block rounded-full bg-accent/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-accent">Jahit Seragam</span>
                            <h3 class="text-xl font-bold text-ink">Pesanan partai untuk keluarga & instansi.</h3>
                            <p class="text-sm text-muted leading-relaxed">Cocok untuk seragam instansi, acara keluarga, atau kelompok.</p>
                            <ul class="space-y-1.5 text-sm text-ink/70">
                                <li class="flex items-center gap-2"><span class="text-accent">✓</span> Ukuran seragam per individu</li>
                                <li class="flex items-center gap-2"><span class="text-accent">✓</span> Membutuhkan DP sebelum produksi</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: Custom -->
                    <div data-reveal data-reveal-delay="2" class="group relative overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="h-1.5 w-full bg-emerald-500"></div>
                        <div class="h-52 overflow-hidden">
                            <div class="h-full w-full bg-stone-200 bg-cover bg-center transition duration-500 group-hover:scale-105" style="background-image: url('{{ asset('images/service-custom.jpg') }}');"></div>
                        </div>
                        <div class="p-6 space-y-3">
                            <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-600">Custom</span>
                            <h3 class="text-xl font-bold text-ink">Desain eksklusif sesuai bentuk tubuh.</h3>
                            <p class="text-sm text-muted leading-relaxed">Konsultasi detail, penyesuaian ukuran, dan proses fitting terjadwal.</p>
                            <ul class="space-y-1.5 text-sm text-ink/70">
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Ukuran pas sesuai bentuk badan</li>
                                <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> Perlu appointment sebelum jahit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── CARA KERJA ── --}}
        <section id="cara-kerja" class="bg-white py-24">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div data-reveal class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">Proses</p>
                    <h2 class="mt-1 font-display text-4xl font-bold text-ink">Bagaimana Cara Kerjanya?</h2>
                    <p class="mx-auto mt-3 max-w-lg text-base text-muted">
                        Proses dibuat sederhana agar pesanan Anda terasa aman dan jelas.
                    </p>
                </div>

                <div class="relative mt-16 grid gap-8 md:grid-cols-4">
                    <!-- Connector line (desktop only) -->
                    <div class="absolute left-0 right-0 top-10 hidden h-px bg-gradient-to-r from-transparent via-border to-transparent md:block" style="margin: 0 12%"></div>

                    @foreach ([
                        ['title' => 'Booking & Fitting', 'desc' => 'Hubungi kami dan jadwalkan fitting.', 'num' => '1'],
                        ['title' => 'Bayar DP', 'desc' => 'Mulai pengerjaan setelah pembayaran tanda jadi.', 'num' => '2'],
                        ['title' => 'Pantau Proses Live', 'desc' => 'Cek status jahitan Anda langsung dari HP.', 'num' => '3'],
                        ['title' => 'Ambil Pakaian', 'desc' => 'Pesanan selesai, siap dipakai atau dikirim.', 'num' => '4'],
                    ] as $step)
                        <div data-reveal class="relative text-center">
                            <div class="relative mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 ring-4 ring-white">
                                <span class="text-2xl font-extrabold text-primary">{{ $step['num'] }}</span>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-ink">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-muted">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── MENGAPA MEMILIH ── --}}
        <section class="bg-surface py-24">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">Keunggulan</p>
                    <h2 class="mt-1 font-display text-4xl font-bold text-ink">Mengapa Memilih Jahitly?</h2>
                    <p class="mt-3 text-base text-muted">Fokus pada kualitas, transparansi, dan kenyamanan.</p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <div data-reveal class="rounded-3xl border border-border bg-white p-6 text-center ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 22h14a2 2 0 002-2v-6a6 6 0 00-6-6H9a6 6 0 00-6 6v6a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink">Harga Transparan</h3>
                        <p class="mt-2 text-sm text-muted">Estimasi biaya jelas di awal, tidak ada biaya tersembunyi.</p>
                    </div>
                    <div data-reveal data-reveal-delay="1" class="rounded-3xl border border-border bg-white p-6 text-center ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink">Tepat Waktu</h3>
                        <p class="mt-2 text-sm text-muted">Sistem antrian digital menjaga proses tetap terorganisir.</p>
                    </div>
                    <div data-reveal data-reveal-delay="2" class="rounded-3xl border border-border bg-white p-6 text-center ring-1 ring-border transition hover:shadow-lg hover:-translate-y-1 duration-300">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6l7 4-7 4-7-4 7-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v6l7 4 7-4v-6" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink">Garansi Fitting</h3>
                        <p class="mt-2 text-sm text-muted">Jahitan kurang pas? Kami perbaiki hingga nyaman dipakai.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── TESTIMONI ── --}}
        <section id="testimoni" class="bg-white py-24">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">Testimoni</p>
                    <h2 class="mt-1 font-display text-4xl font-bold text-ink">Apa Kata Pelanggan Kami?</h2>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @forelse ($testimonials as $testimoni)
                        <div data-reveal class="rounded-3xl border border-border bg-surface p-6 shadow-sm">
                            <div class="flex items-center gap-1 text-amber-500">
                                @for($i = 0; $i < $testimoni->rating; $i++)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="mt-4 text-sm italic text-muted">"{{ $testimoni->comment }}"</p>
                            <div class="mt-4 flex items-center gap-3 border-t border-border pt-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                    {{ substr($testimoni->customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-ink">{{ $testimoni->customer->name }}</div>
                                    <div class="text-xs text-muted">{{ $testimoni->order->service->name ?? 'Layanan' }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Fallback Dummy Testimonials --}}
                        @foreach ([
                            ['name' => 'Ibu Sari', 'service' => 'Jahit Seragam', 'desc' => 'Jahitannya sangat rapi dan tepat waktu. Paling suka karena bisa pantau dari HP!'],
                            ['name' => 'Pak Budi', 'service' => 'Vermak Pakaian', 'desc' => 'Vermak jas di sini hasilnya pas banget. Gak kelihatan kalau habis dipermak.'],
                            ['name' => 'Maya Putri', 'service' => 'Jahit Custom', 'desc' => 'Seragam kantor pesanan kami selesai lebih cepat dari jadwal. Sangat profesional!'],
                        ] as $dummy)
                            <div data-reveal class="rounded-3xl border border-border bg-surface p-6 shadow-sm">
                                <div class="flex items-center gap-1 text-amber-500">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    @endfor
                                </div>
                                <p class="mt-4 text-sm italic text-muted">"{{ $dummy['desc'] }}"</p>
                                <div class="mt-4 flex items-center gap-3 border-t border-border pt-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                        {{ substr($dummy['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-ink">{{ $dummy['name'] }}</div>
                                        <div class="text-xs text-muted">{{ $dummy['service'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ── FAQ ── --}}
        <section id="faq" class="bg-surface py-24">
            <div data-reveal class="mx-auto max-w-4xl px-4 sm:px-6">
                <div class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-widest text-primary">FAQ</p>
                    <h2 class="mt-1 font-display text-4xl font-bold text-ink">Pertanyaan Umum</h2>
                    <p class="mt-3 text-base text-muted">Pertanyaan yang sering diajukan pelanggan.</p>
                </div>

                <div x-data="{ active: null }" class="mt-10 rounded-3xl border border-border bg-white p-2 shadow-sm">
                    @foreach ([
                        ['q' => 'Apakah bisa bawa kain sendiri?', 'a' => 'Bisa. Silakan bawa kain saat konsultasi atau tulis di catatan pesanan.'],
                        ['q' => 'Apakah vermak perlu bayar DP?', 'a' => 'Untuk vermak harian tidak perlu DP. Pembayaran dilakukan saat pesanan selesai.'],
                        ['q' => 'Berapa lama proses pembuatan pakaian custom?', 'a' => 'Tergantung tingkat kesulitan. Rata-rata 7–14 hari setelah appointment dan DP.'],
                    ] as $index => $faq)
                        <div data-reveal class="border-b border-border last:border-0 px-4">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between py-5 text-left text-base font-semibold text-ink transition hover:text-primary"
                                @click="active = (active === {{ $index }} ? null : {{ $index }})"
                            >
                                <span>{{ $faq['q'] }}</span>
                                <svg class="h-5 w-5 shrink-0 text-primary transition-transform duration-300"
                                     :class="active === {{ $index }} ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="active === {{ $index }}" x-transition class="pb-5 text-sm text-muted leading-relaxed" style="display: none;">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ── FOOTER ── --}}
        <footer class="bg-ink">
            <div class="h-1 w-full bg-gradient-to-r from-primary via-accent to-emerald-500"></div>
            <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                <div data-reveal class="grid gap-8 md:grid-cols-2">
                    <div>
                        <div class="font-display text-2xl font-bold text-white">Jahit<span class="text-accent">ly</span></div>
                        <p class="mt-2 max-w-xs text-sm text-white/50">Solusi jahit modern untuk keluarga dan usaha Anda.</p>
                    </div>
                    <div class="space-y-2 text-sm text-white/50">
                        <p>WhatsApp: +62 812-3456-7890</p>
                        <p>&copy; 2026 Jahitly. Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const targets = document.querySelectorAll('[data-reveal]');

                const checkReveal = () => {
                    const triggerBottom = window.innerHeight * 0.95;
                    targets.forEach(el => {
                        const rect = el.getBoundingClientRect();
                        if (rect.top < triggerBottom) {
                            el.classList.add('is-visible');
                        }
                        else {
                            el.classList.remove('is-visible');
                        }
                    });
                };

                checkReveal();
                window.addEventListener('scroll', checkReveal, { passive: true });
                window.addEventListener('resize', checkReveal, { passive: true });

                setTimeout(checkReveal, 150);
                setTimeout(checkReveal, 500);
            });
        </script>
        @livewireScripts
    </body>
</html>
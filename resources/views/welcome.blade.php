<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Jahitly') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="bg-stone-50 text-stone-900 antialiased">
        <header x-data="{ open: false }" class="fixed inset-x-0 top-0 z-50">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="mt-4 flex items-center justify-between rounded-2xl border border-white/20 bg-white/70 px-4 py-3 shadow-sm backdrop-blur">
                    <div class="text-lg font-bold tracking-tight">Jahitly</div>
                    <nav class="hidden items-center gap-6 text-sm font-semibold text-stone-700 md:flex">
                        <a href="#layanan" class="hover:text-stone-900">Layanan</a>
                        <a href="#cara-kerja" class="hover:text-stone-900">Cara Kerja</a>
                        <a href="#testimoni" class="hover:text-stone-900">Testimoni</a>
                        <a href="#faq" class="hover:text-stone-900">FAQ</a>
                    </nav>
                    <div class="hidden items-center gap-4 md:flex">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-stone-700 hover:text-stone-900">Masuk</a>
                        <a
                            href="{{ route('orders.create') }}"
                            class="rounded-full border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-600 hover:text-white"
                        >
                            Buat Pesanan
                        </a>
                    </div>
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-3 py-2 text-sm font-semibold text-stone-700 md:hidden"
                        aria-label="Toggle menu"
                    >
                        Menu
                    </button>
                </div>
                <div x-cloak x-show="open" x-transition class="mt-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm md:hidden">
                    <nav class="flex flex-col gap-3 text-sm font-semibold text-stone-700">
                        <a href="#layanan" class="hover:text-stone-900">Layanan</a>
                        <a href="#cara-kerja" class="hover:text-stone-900">Cara Kerja</a>
                        <a href="#testimoni" class="hover:text-stone-900">Testimoni</a>
                        <a href="#faq" class="hover:text-stone-900">FAQ</a>
                        <div class="flex items-center gap-3 pt-2">
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-stone-700 hover:text-stone-900">Masuk</a>
                            <a
                                href="{{ route('orders.create') }}"
                                class="rounded-full border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-600 hover:bg-blue-600 hover:text-white"
                            >
                                Buat Pesanan
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <section
            class="relative min-h-screen bg-cover bg-center"
            style="background-image: url('{{ asset('images/hero-bg.jpg') }}');"
        >
            <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-stone-900/80 to-transparent md:bg-gradient-to-r"></div>
            <div class="relative mx-auto flex min-h-screen max-w-6xl items-end px-4 pb-20 pt-32 sm:px-6 md:items-center">
                <div data-reveal class="max-w-xl space-y-6 text-stone-50">
                    <p class="inline-flex items-center gap-2 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-200">
                        Jahit Rapi, Proses Transparan
                    </p>
                    <h1 class="text-4xl font-bold leading-tight sm:text-5xl">
                        Jahit Lebih Pasti, Tanpa Repot Antri.
                    </h1>
                    <p class="text-base text-stone-200 sm:text-lg">
                        Dari vermak harian hingga seragam keluarga. Pesan jasa jahit terpercaya dan pantau prosesnya secara real-time dari HP Anda.
                    </p>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a
                            href="{{ route('orders.create') }}"
                            class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-blue-700"
                        >
                            Mulai Pesanan
                        </a>
                        <a
                            href="#layanan"
                            class="inline-flex items-center justify-center rounded-full border border-white px-6 py-3 text-base font-semibold text-white hover:bg-white/10"
                        >
                            Lihat Layanan
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="layanan" class="bg-stone-50 py-20">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold">Layanan Kami</h2>
                    <p class="mt-3 text-base text-stone-600">
                        Pilih layanan sesuai kebutuhan. Semua pesanan tercatat rapi dan bisa dipantau kapan saja.
                    </p>
                </div>

                <div class="mt-12 space-y-16">
                    <div data-reveal class="grid items-center gap-8 lg:grid-cols-2">
                        <div
                            class="h-64 rounded-2xl bg-stone-200 bg-cover bg-center shadow-sm sm:h-80"
                            style="background-image: url('{{ asset('images/service-vermak.jpg') }}');"
                        ></div>
                        <div class="space-y-4">
                            <p class="text-sm font-semibold uppercase tracking-wide text-amber-500">Vermak Harian</p>
                            <h3 class="text-2xl font-semibold">Perbaikan cepat untuk baju favorit.</h3>
                            <p class="text-base text-stone-600">
                                Cocok untuk perbaikan kecil hingga penyesuaian ukuran. Proses cepat dan hasil rapi.
                            </p>
                            <ul class="space-y-2 text-sm text-stone-700">
                                <li>• Cepat selesai tanpa antre panjang</li>
                                <li>• Hasil jahitan rapi dan kuat</li>
                                <li>• Tanpa DP, langsung proses</li>
                            </ul>
                        </div>
                    </div>

                    <div data-reveal class="grid items-center gap-8 lg:grid-cols-2">
                        <div class="space-y-4 lg:order-1">
                            <p class="text-sm font-semibold uppercase tracking-wide text-amber-500">Seragam</p>
                            <h3 class="text-2xl font-semibold">Pesanan skala besar tetap presisi.</h3>
                            <p class="text-base text-stone-600">
                                Seragam kantor, keluarga, atau komunitas dikerjakan terstruktur dengan kontrol kualitas ketat.
                            </p>
                            <ul class="space-y-2 text-sm text-stone-700">
                                <li>• Kapasitas produksi besar</li>
                                <li>• Detail ukuran tetap konsisten</li>
                                <li>• Membutuhkan DP sebelum produksi</li>
                            </ul>
                        </div>
                        <div
                            class="h-64 rounded-2xl bg-stone-200 bg-cover bg-center shadow-sm sm:h-80 lg:order-2"
                            style="background-image: url('{{ asset('images/service-seragam.jpg') }}');"
                        ></div>
                    </div>

                    <div data-reveal class="grid items-center gap-8 lg:grid-cols-2">
                        <div
                            class="h-64 rounded-2xl bg-stone-200 bg-cover bg-center shadow-sm sm:h-80"
                            style="background-image: url('{{ asset('images/service-custom.jpg') }}');"
                        ></div>
                        <div class="space-y-4">
                            <p class="text-sm font-semibold uppercase tracking-wide text-amber-500">Custom</p>
                            <h3 class="text-2xl font-semibold">Desain eksklusif sesuai bentuk tubuh.</h3>
                            <p class="text-base text-stone-600">
                                Konsultasi detail, penyesuaian ukuran, dan proses fitting yang terjadwal.
                            </p>
                            <ul class="space-y-2 text-sm text-stone-700">
                                <li>• Ukuran pas sesuai bentuk badan</li>
                                <li>• Desain eksklusif & pilihan bahan</li>
                                <li>• Perlu appointment sebelum jahit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="bg-white py-20">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold">Bagaimana Cara Kerjanya?</h2>
                    <p class="mt-3 text-base text-stone-600">
                        Proses dibuat sederhana agar pesanan Anda terasa aman dan jelas.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-4">
                    @foreach ([
                        ['title' => 'Booking & Fitting', 'desc' => 'Hubungi kami dan jadwalkan fitting.', 'num' => '1'],
                        ['title' => 'Bayar DP', 'desc' => 'Mulai pengerjaan setelah pembayaran tanda jadi.', 'num' => '2'],
                        ['title' => 'Pantau Proses Live', 'desc' => 'Cek status jahitan Anda langsung dari HP.', 'num' => '3'],
                        ['title' => 'Ambil Pakaian', 'desc' => 'Pesanan selesai, siap dipakai atau dikirim.', 'num' => '4'],
                    ] as $step)
                        <div data-reveal class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500 text-base font-semibold text-white">
                                {{ $step['num'] }}
                            </div>
                            <h3 class="mt-4 text-lg font-semibold">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm text-stone-600">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-stone-50 py-20">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-bold">Mengapa Memilih Jahitly?</h2>
                    <p class="mt-3 text-base text-stone-600">Fokus pada kualitas, transparansi, dan kenyamanan.</p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <div data-reveal class="rounded-2xl border border-stone-200 bg-white p-6 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-blue-600/10 text-blue-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 22h14a2 2 0 002-2v-6a6 6 0 00-6-6H9a6 6 0 00-6 6v6a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Harga Transparan</h3>
                        <p class="mt-2 text-sm text-stone-600">Estimasi biaya jelas di awal, tidak ada biaya tersembunyi.</p>
                    </div>
                    <div data-reveal class="rounded-2xl border border-stone-200 bg-white p-6 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-blue-600/10 text-blue-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Tepat Waktu</h3>
                        <p class="mt-2 text-sm text-stone-600">Sistem antrian digital menjaga proses tetap terorganisir.</p>
                    </div>
                    <div data-reveal class="rounded-2xl border border-stone-200 bg-white p-6 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-blue-600/10 text-blue-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6l7 4-7 4-7-4 7-4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v6l7 4 7-4v-6" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Garansi Fitting</h3>
                        <p class="mt-2 text-sm text-stone-600">Jahitan kurang pas? Kami perbaiki hingga nyaman dipakai.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimoni" class="bg-amber-50/70 py-20">
            <div data-reveal class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold">Apa Kata Pelanggan Kami?</h2>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @forelse ($testimonials as $testimoni)
                        <div data-reveal class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center gap-1 text-amber-500">
                                @for($i = 0; $i < $testimoni->rating; $i++)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="mt-4 text-sm italic text-stone-600">"{{ $testimoni->comment }}"</p>
                            <div class="mt-4 flex items-center gap-3 border-t border-stone-100 pt-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                    {{ substr($testimoni->customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-stone-900">{{ $testimoni->customer->name }}</div>
                                    <div class="text-xs text-stone-500">{{ $testimoni->order->service->name ?? 'Layanan' }}</div>
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
                            <div data-reveal class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                                <div class="flex items-center gap-1 text-amber-500">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                </div>
                                <p class="mt-4 text-sm italic text-stone-600">"{{ $dummy['desc'] }}"</p>
                                <div class="mt-4 flex items-center gap-3 border-t border-stone-100 pt-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                        {{ substr($dummy['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-stone-900">{{ $dummy['name'] }}</div>
                                        <div class="text-xs text-stone-500">{{ $dummy['service'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <section id="faq" class="bg-white py-20">
            <div data-reveal class="mx-auto max-w-4xl px-4 sm:px-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold">FAQ</h2>
                    <p class="mt-3 text-base text-stone-600">Pertanyaan yang sering diajukan pelanggan.</p>
                </div>

                <div x-data="{ active: null }" class="mt-10 space-y-4">
                    @foreach ([
                        ['q' => 'Apakah bisa bawa kain sendiri?', 'a' => 'Bisa. Silakan bawa kain saat konsultasi atau tulis di catatan pesanan.'],
                        ['q' => 'Apakah vermak perlu bayar DP?', 'a' => 'Untuk vermak harian tidak perlu DP. Pembayaran dilakukan saat pesanan selesai.'],
                        ['q' => 'Berapa lama proses pembuatan pakaian custom?', 'a' => 'Tergantung tingkat kesulitan. Rata-rata 7–14 hari setelah appointment dan DP.'],
                    ] as $index => $faq)
                        <div data-reveal class="rounded-2xl border border-stone-200 p-5">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between text-left text-base font-semibold"
                                @click="active = active === {{ $index }} ? null : {{ $index }}"
                            >
                                <span>{{ $faq['q'] }}</span>
                                <span class="text-amber-500" x-text="active === {{ $index }} ? '-' : '+'"></span>
                            </button>
                            <div x-cloak x-show="active === {{ $index }}" x-transition class="mt-3 text-sm text-stone-600">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>


        <footer class="bg-stone-900 py-10 text-stone-300">
            <div data-reveal class="mx-auto flex max-w-6xl flex-col gap-4 px-4 sm:px-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="text-lg font-bold text-white">Jahitly</div>
                    <p class="mt-2 text-sm text-stone-400">Solusi jahit modern untuk keluarga dan usaha Anda.</p>
                </div>
                <div class="text-sm">
                    <p>WhatsApp: +62 812-3456-7890</p>
                    <p class="mt-2">&copy; 2026 Jahitly. Semua hak dilindungi.</p>
                </div>
            </div>
        </footer>
        <script>
            // Failsafe scroll reveal (Bypasses any potential Vite/JS loading issues)
            document.addEventListener('DOMContentLoaded', function() {
                const targets = document.querySelectorAll('[data-reveal]');
                
                const checkReveal = () => {
                    const triggerBottom = window.innerHeight * 0.95;
                    targets.forEach(el => {
                        const rect = el.getBoundingClientRect();
                        // Jika elemen masuk ke dalam layar, tambahkan class (animasi jalan)
                        if (rect.top < triggerBottom) {
                            el.classList.add('is-visible');
                        } 
                        // Jika elemen keluar batas bawah layar (scroll ke atas), hapus class agar bisa dianimasikan lagi
                        else {
                            el.classList.remove('is-visible');
                        }
                    });
                };
                
                checkReveal();
                window.addEventListener('scroll', checkReveal, { passive: true });
                window.addEventListener('resize', checkReveal, { passive: true });
                
                // Fallbacks
                setTimeout(checkReveal, 150);
                setTimeout(checkReveal, 500);
            });
        </script>
    </body>
</html>
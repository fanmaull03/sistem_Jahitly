<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Jahitly') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="font-sans text-ink antialiased bg-white">
        <div class="flex min-h-screen">

            <!-- LEFT: Brand panel -->
            <div class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-ink p-12 lg:flex">
                <!-- Blob dekoratif -->
                <div class="absolute -right-20 top-1/3 h-72 w-72 rounded-full bg-primary/20 blur-3xl"></div>
                <div class="absolute -left-10 bottom-1/4 h-48 w-48 rounded-full bg-accent/20 blur-2xl"></div>

                <!-- Logo -->
                <div class="relative">
                    <a href="/" class="font-display text-3xl font-bold text-white">Jahit<span class="text-accent">ly</span></a>
                </div>

                <!-- Center quote -->
                <div class="relative space-y-4">
                    <p class="font-display text-4xl font-bold leading-snug text-white">
                        "Setiap jahitan<br>punya ceritanya."
                    </p>
                    <p class="text-sm text-white/50">Pantau proses jahit Anda secara transparan, real-time.</p>
                </div>

                <!-- Bottom stats -->
                <div class="relative flex gap-8">
                    <div>
                        <div class="text-2xl font-bold text-white">500+</div>
                        <div class="text-xs text-white/50">Pelanggan</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white">98%</div>
                        <div class="text-xs text-white/50">Puas</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white">3 Layanan</div>
                        <div class="text-xs text-white/50">Tersedia</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Form panel -->
            <div class="flex w-full flex-col items-center justify-center px-6 py-12 lg:w-1/2">
                <!-- Mobile logo (hanya muncul di mobile) -->
                <div class="mb-8 lg:hidden">
                    <a href="/" class="font-display text-2xl font-bold text-ink">Jahit<span class="text-primary">ly</span></a>
                </div>
                <div data-reveal class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>
        @livewireScripts
    </body>
</html>

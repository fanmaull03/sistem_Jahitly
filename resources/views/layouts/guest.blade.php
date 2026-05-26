<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Jahitly') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased bg-stone-50">
        <div class="relative min-h-screen px-4 py-12 sm:px-6">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-stone-50 via-stone-100/40 to-white"></div>

            <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col items-center justify-center gap-8">
                <div data-reveal class="text-center">
                    <a href="/" class="inline-flex items-center justify-center gap-2 text-lg font-bold tracking-tight text-stone-900">
                        Jahitly
                    </a>
                    <p class="mt-2 text-sm text-stone-500">Solusi jahit modern untuk kebutuhan harian Anda.</p>
                </div>

                <div data-reveal data-reveal-delay="1" class="w-full max-w-md rounded-2xl border border-stone-200 bg-white/90 px-6 py-6 shadow-sm backdrop-blur">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

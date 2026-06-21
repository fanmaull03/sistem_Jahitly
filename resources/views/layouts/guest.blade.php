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
        @livewireStyles
    </head>
    <body class="font-sans text-stone-900 antialiased bg-stone-50">
        <div class="relative min-h-screen px-4 py-12 sm:px-6">
            <div class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/login-bg.png') }}');"></div>
            <div class="pointer-events-none absolute inset-0 bg-stone-900/60 backdrop-blur-sm"></div>

            <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col items-center justify-center gap-8">
                <div data-reveal class="text-center">
                    <a href="/" class="inline-flex items-center justify-center gap-2 text-2xl font-bold tracking-tight text-white drop-shadow-md">
                        Jahitly
                    </a>
                    <p class="mt-2 text-sm text-stone-200 drop-shadow">Solusi jahit modern untuk kebutuhan harian Anda.</p>
                </div>

                <div data-reveal data-reveal-delay="1" class="w-full max-w-md rounded-2xl border border-stone-200 bg-white/95 px-6 py-6 shadow-xl backdrop-blur-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>

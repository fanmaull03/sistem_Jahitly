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
    <body class="font-sans antialiased bg-stone-50 text-stone-900 dark:bg-stone-900 dark:text-stone-100">
        <div class="min-h-screen bg-stone-50 dark:bg-stone-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="mx-auto mt-6 max-w-6xl px-4 sm:px-6">
                    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pt-24 sm:pt-28">
                @if (session('success'))
                    <div class="mx-auto mt-6 max-w-6xl px-4 sm:px-6">
                        <div class="slide-in-right rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Jahitly') }} - Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="font-sans text-slate-900">
        <div class="min-h-screen bg-slate-50">
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <div class="text-2xl font-bold">Jahitly Admin</div>
                        <div class="text-sm text-slate-500">Panel kontrol sederhana untuk penjahit.</div>
                    </div>
                    <nav class="flex flex-wrap gap-2 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:border-slate-300" wire:navigate>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:border-slate-300" wire:navigate>
                            Pesanan
                        </a>
                        <a href="{{ route('admin.queue.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:border-slate-300" wire:navigate>
                            Antrian
                        </a>
                        <a href="{{ route('admin.appointments.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:border-slate-300" wire:navigate>
                            Appointment
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:border-slate-300" wire:navigate>
                            Pembayaran
                        </a>
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
                {{ $slot }}
            </main>
        </div>

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3500)"
                x-show="show"
                x-cloak
                class="fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 shadow-lg"
            >
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 4000)"
                x-show="show"
                x-cloak
                class="fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 shadow-lg"
            >
                {{ session('error') }}
            </div>
        @endif

        @livewireScripts
    </body>
</html>

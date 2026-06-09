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
    <body class="font-sans text-stone-900 bg-stone-100">
        <div x-data="{ open: false, logoutConfirm: false, logoutTarget: null }" class="min-h-screen">
            <!-- Mobile Drawer -->
            <div x-cloak x-show="open" class="fixed inset-0 z-40 bg-stone-900/60 lg:hidden" @click="open = false"></div>
            <aside
                x-cloak
                x-show="open"
                x-transition
                class="fixed inset-y-0 left-0 z-50 flex h-full w-72 flex-col bg-stone-900 text-stone-300 lg:hidden"
            >
                <div class="flex items-center justify-between border-b border-stone-800 px-6 py-5">
                    <div>
                        <div class="text-lg font-bold text-white">Jahitly Admin</div>
                        <div class="text-xs text-stone-400">Panel kerja penjahit</div>
                    </div>
                    <button class="rounded-full border border-stone-700 px-2 py-1 text-xs" @click="open = false">Tutup</button>
                </div>
                <nav class="space-y-2 px-4 py-4 text-sm font-semibold">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.dashboard') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-10.5z" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.orders.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" />
                        </svg>
                        Kelola Pesanan
                    </a>
                    <a href="{{ route('admin.queue.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.queue.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                        Antrian Produksi
                    </a>
                    <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.appointments.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Jadwal Fitting
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.payments.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        Verifikasi Pembayaran
                    </a>
                    <a href="{{ route('admin.fabrics.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.fabrics.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Kelola Bahan
                    </a>
                </nav>
                <div class="mt-auto border-t border-stone-800 px-4 py-4">
                    <form method="POST" action="{{ route('logout') }}" x-ref="logoutMobileForm">
                        @csrf
                        <button
                            type="button"
                                class="flex w-full items-center justify-center rounded-xl border border-stone-700 bg-stone-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-600 hover:shadow"
                            @click="logoutTarget = 'logoutMobileForm'; logoutConfirm = true; open = false"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-screen">
                <!-- Desktop Sidebar -->
                <aside class="hidden min-h-screen w-72 bg-stone-900 text-stone-300 lg:flex lg:flex-col">
                    <div class="border-b border-stone-800 px-6 py-6">
                        <div class="text-lg font-bold text-white">Jahitly Admin</div>
                        <div class="text-xs text-stone-400">Panel kerja penjahit</div>
                    </div>
                    <nav class="flex-1 space-y-2 px-4 py-4 text-sm font-semibold">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.dashboard') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-10.5z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.orders.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" />
                            </svg>
                            Kelola Pesanan
                        </a>
                        <a href="{{ route('admin.queue.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.queue.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                            </svg>
                            Antrian Produksi
                        </a>
                        <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.appointments.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Jadwal Fitting
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.payments.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                            Verifikasi Pembayaran
                        </a>
                        <a href="{{ route('admin.fabrics.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.fabrics.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Kelola Bahan
                        </a>
                    </nav>
                    <div class="mt-auto border-t border-stone-800 px-4 py-4">
                        <form method="POST" action="{{ route('logout') }}" x-ref="logoutDesktopForm">
                            @csrf
                            <button
                                type="button"
                                class="flex w-full items-center justify-center rounded-xl border border-stone-700 bg-stone-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-600 hover:shadow"
                                @click="logoutTarget = 'logoutDesktopForm'; logoutConfirm = true"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Content Area -->
                <div class="flex-1">
                    <header class="sticky top-0 z-30 border-b border-stone-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="rounded-lg border border-stone-200 px-3 py-2 text-sm font-semibold text-stone-700 lg:hidden"
                                    @click="open = true"
                                >
                                    Menu
                                </button>
                                <div class="text-lg font-bold text-stone-900">
                                    @isset($header)
                                        {{ $header }}
                                    @else
                                        Dashboard
                                    @endisset
                                </div>
                            </div>
                            <div class="hidden sm:block text-xs text-stone-500">
                                @isset($actions)
                                    {{ $actions }}
                                @else
                                    Admin Panel
                                @endisset
                            </div>
                        </div>
                    </header>

                    <main class="bg-stone-100 px-4 py-6 sm:px-6">
                        {{ $slot }}
                    </main>
                </div>
            </div>

            <div
                x-cloak
                x-show="logoutConfirm"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 px-4"
            >
                <div class="w-full max-w-sm rounded-2xl border border-stone-200 bg-white p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-stone-900">Konfirmasi Logout</h3>
                    <p class="mt-2 text-sm text-stone-600">Anda yakin ingin keluar dari akun admin?</p>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-lg border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50"
                            @click="logoutConfirm = false; logoutTarget = null"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                            @click="if ($refs[logoutTarget]) { $refs[logoutTarget].submit(); }"
                        >
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 3500)"
                x-show="show"
                x-cloak
                class="slide-in-right fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 shadow-lg"
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
                class="slide-in-right fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 shadow-lg"
            >
                {{ session('error') }}
            </div>
        @endif

        @livewireScripts
    </body>
</html>

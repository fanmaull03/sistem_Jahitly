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
    <body class="font-sans text-stone-900 bg-stone-100 dark:bg-stone-900 dark:text-stone-100">
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
                    <a href="{{ route('admin.vermaks.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.vermaks.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 14.25l-2.25 2.25m-4.5 0L5.25 14.25M12 12l2.25-2.25m0-4.5l-4.5 4.5M12 12V3" />
                        </svg>
                        Kelola Vermak
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
                        <a href="{{ route('admin.vermaks.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 hover:bg-stone-800 {{ request()->routeIs('admin.vermaks.*') ? 'bg-stone-800 text-white' : '' }}" wire:navigate>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 14.25l-2.25 2.25m-4.5 0L5.25 14.25M12 12l2.25-2.25m0-4.5l-4.5 4.5M12 12V3" />
                            </svg>
                            Kelola Vermak
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
                    <header class="sticky top-0 z-30 border-b border-stone-200 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="rounded-lg border border-stone-200 px-3 py-2 text-sm font-semibold text-stone-700 dark:border-stone-600 dark:text-stone-300 lg:hidden"
                                    @click="open = true"
                                >
                                    Menu
                                </button>
                                <div class="text-lg font-bold text-stone-900 dark:text-stone-100">
                                    @isset($header)
                                        {{ $header }}
                                    @else
                                        Dashboard
                                    @endisset
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <!-- Dark Mode Toggle -->
                                <button
                                    type="button"
                                    @click="$store.darkMode.toggle()"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 text-stone-600 transition hover:bg-stone-100 dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-700"
                                    :aria-label="$store.darkMode.on ? 'Mode Terang' : 'Mode Gelap'"
                                    :title="$store.darkMode.on ? 'Mode Terang' : 'Mode Gelap'"
                                >
                                    <svg x-show="$store.darkMode.on" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <svg x-show="!$store.darkMode.on" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                </button>
                                <div class="hidden sm:block text-xs text-stone-500 dark:text-stone-400">
                                    @isset($actions)
                                        {{ $actions }}
                                    @else
                                        Admin Panel
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="bg-stone-100 px-4 py-6 sm:px-6 dark:bg-stone-900">
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
                <div class="w-full max-w-sm rounded-2xl border border-stone-200 bg-white p-6 shadow-xl dark:border-stone-700 dark:bg-stone-800">
                    <h3 class="text-lg font-bold text-stone-900 dark:text-stone-100">Konfirmasi Logout</h3>
                    <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Anda yakin ingin keluar dari akun admin?</p>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-lg border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50 dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"
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
                class="slide-in-right fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 shadow-lg dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300"
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
                class="slide-in-right fixed right-4 top-4 z-50 w-full max-w-sm rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 shadow-lg dark:border-rose-800 dark:bg-rose-900/30 dark:text-rose-300"
            >
                {{ session('error') }}
            </div>
        @endif

        @livewireScripts
        @stack('scripts')
    </body>
</html>

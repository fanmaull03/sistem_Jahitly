@php
    $user = auth()->user();
    $isAdmin = $user && $user->isAdmin();
    $notificationCount = $user ? $user->unreadNotifications()->count() : 0;
    $latestNotifications = $user
        ? $user->notifications()->latest()->take(5)->get()
        : collect();
    $profilePhotoUrl = $user && $user->profile_photo_path
        ? asset('storage/' . $user->profile_photo_path)
        : null;
    $navItems = $isAdmin
        ? [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
            ['label' => 'Orders', 'route' => 'admin.orders.index', 'active' => 'admin.orders.*'],
            ['label' => 'Payments', 'route' => 'admin.payments.index', 'active' => 'admin.payments.*'],
            ['label' => 'Queue', 'route' => 'admin.queue.index', 'active' => 'admin.queue.*'],
            ['label' => 'Appointments', 'route' => 'admin.appointments.index', 'active' => 'admin.appointments.*'],
        ]
        : [
            ['label' => 'Pesanan', 'route' => 'orders.index', 'active' => 'orders.*'],
            ['label' => 'Pembayaran', 'route' => 'payments.index', 'active' => 'payments.*'],
        ];
    $homeRoute = $isAdmin ? 'admin.dashboard' : 'orders.index';
@endphp

<nav x-data="{ open: false, menuOpen: false, logoutConfirm: false, logoutTarget: null, notifOpen: false }" class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="mt-4 flex items-center justify-between rounded-2xl bg-white px-5 py-3.5 shadow-[0_2px_20px_-4px_rgba(0,0,0,0.08)] ring-1 ring-black/5 dark:bg-stone-800/90 dark:ring-stone-700">
            <a href="{{ route($homeRoute) }}" class="font-display text-xl font-bold tracking-tight text-ink dark:text-white">
                Jahit<span class="text-primary">ly</span>
            </a>

            <div class="hidden items-center gap-6 text-sm font-medium md:flex">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ request()->routeIs($item['active']) ? 'text-primary font-semibold' : 'text-ink/60 hover:text-ink transition dark:text-stone-400 dark:hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 md:flex">
                <!-- Dark Mode Toggle -->
                <button
                    type="button"
                    @click="$store.darkMode.toggle()"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-ink/50 transition hover:bg-surface hover:text-ink ring-1 ring-transparent hover:ring-border dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-white dark:hover:ring-stone-600"
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

                <div class="relative" @click.away="notifOpen = false">
                    <button
                        type="button"
                        @click="notifOpen = !notifOpen"
                        class="relative flex h-9 w-9 items-center justify-center rounded-full text-ink/50 transition hover:bg-surface hover:text-ink ring-1 ring-transparent hover:ring-border dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-white dark:hover:ring-stone-600"
                        aria-label="Notifikasi"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a3 3 0 006 0" />
                        </svg>
                        @if ($notificationCount > 0)
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-accent"></span>
                        @endif
                    </button>

                    <div
                        x-cloak
                        x-show="notifOpen"
                        x-transition
                        class="absolute right-0 mt-2 w-72 rounded-2xl border border-border bg-white p-4 shadow-lg dark:border-stone-700 dark:bg-stone-800"
                    >
                        <div class="text-xs font-semibold uppercase tracking-wide text-muted dark:text-stone-400">Notifikasi</div>
                        <div class="mt-3 space-y-2">
                            @forelse ($latestNotifications as $notification)
                                @php
                                    $url = $notification->data['url'] ?? null;
                                @endphp
                                <div class="rounded-xl border border-border bg-surface p-3 text-xs text-ink/70 dark:border-stone-700 dark:bg-stone-700/50 dark:text-stone-300">
                                    @if ($url)
                                        <a href="{{ $url }}" class="font-semibold text-ink hover:text-primary dark:text-stone-100 dark:hover:text-primary">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </a>
                                    @else
                                        <div class="font-semibold text-ink dark:text-stone-100">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </div>
                                    @endif
                                    <div class="mt-1 text-[11px] text-muted">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-border bg-surface p-3 text-xs text-muted dark:border-stone-700 dark:bg-stone-700/50 dark:text-stone-400">
                                    Belum ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" @click.away="menuOpen = false">
                    <button
                        type="button"
                        @click="menuOpen = !menuOpen"
                        class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-primary/10 text-sm font-bold text-primary ring-2 ring-transparent transition hover:ring-primary/30 dark:bg-primary/20 dark:text-blue-300"
                        aria-label="User menu"
                    >
                        @if ($profilePhotoUrl)
                            <img src="{{ $profilePhotoUrl }}" alt="Foto profil" class="h-full w-full object-cover" />
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                        @endif
                    </button>

                    <div
                        x-cloak
                        x-show="menuOpen"
                        x-transition
                        class="absolute right-0 mt-2 w-48 rounded-2xl border border-border bg-white p-2 shadow-lg dark:border-stone-700 dark:bg-stone-800"
                    >
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-ink/70 hover:bg-surface hover:text-ink dark:text-stone-300 dark:hover:bg-stone-700 dark:hover:text-white">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" x-ref="logoutDesktopForm">
                            @csrf
                            <button
                                type="button"
                                class="block w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-ink/70 hover:bg-surface hover:text-ink dark:text-stone-300 dark:hover:bg-stone-700 dark:hover:text-white"
                                @click="logoutTarget = 'logoutDesktopForm'; logoutConfirm = true; menuOpen = false"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <button
                type="button"
                @click="open = !open"
                class="inline-flex items-center justify-center rounded-full border border-border bg-white px-3 py-2 text-sm font-semibold text-ink md:hidden dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300"
                aria-label="Toggle menu"
            >
                Menu
            </button>
        </div>

        <div x-cloak x-show="open" x-transition class="mt-3 rounded-2xl border border-border bg-white p-4 shadow-lg md:hidden dark:border-stone-700 dark:bg-stone-800">
            <nav class="flex flex-col gap-3 text-sm font-medium text-ink/70 dark:text-stone-300">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ request()->routeIs($item['active']) ? 'text-primary font-semibold' : 'text-ink/60 hover:text-ink dark:text-stone-400 dark:hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="mt-2 rounded-2xl border border-border bg-surface p-3 dark:border-stone-700 dark:bg-stone-700/50">
                    <div class="text-sm font-semibold text-ink dark:text-stone-100">{{ $user->name }}</div>
                    <div class="text-xs text-muted dark:text-stone-400">{{ $user->email }}</div>
                    <div class="mt-3 flex flex-col gap-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-xl border border-border px-3 py-2 text-sm font-semibold text-ink/70 dark:border-stone-600 dark:text-stone-300">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" x-ref="logoutMobileForm">
                            @csrf
                            <button
                                type="button"
                                class="w-full rounded-xl border border-border px-3 py-2 text-left text-sm font-semibold text-ink/70 dark:border-stone-600 dark:text-stone-300"
                                @click="logoutTarget = 'logoutMobileForm'; logoutConfirm = true; open = false"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div
        x-cloak
        x-show="logoutConfirm"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-ink/50 px-4"
    >
        <div class="w-full max-w-sm rounded-2xl border border-border bg-white p-6 shadow-xl dark:border-stone-700 dark:bg-stone-800">
            <h3 class="text-lg font-bold text-ink dark:text-stone-100">Konfirmasi Keluar</h3>
            <p class="mt-2 text-sm text-muted dark:text-stone-400">Anda yakin ingin keluar dari akun?</p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    class="rounded-xl border border-border px-4 py-2 text-sm font-semibold text-ink/70 hover:bg-surface dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"
                    @click="logoutConfirm = false; logoutTarget = null"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    @click="if ($refs[logoutTarget]) { $refs[logoutTarget].submit(); }"
                >
                    Keluar
                </button>
            </div>
        </div>
    </div>
</nav>

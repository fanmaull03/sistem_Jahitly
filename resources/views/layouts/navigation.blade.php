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
        <div class="mt-4 flex items-center justify-between rounded-2xl border border-white/20 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
            <a href="{{ route($homeRoute) }}" class="text-lg font-bold tracking-tight text-stone-900">
                Jahitly
            </a>

            <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ request()->routeIs($item['active']) ? 'text-stone-900' : 'text-stone-600 hover:text-stone-900' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <div class="relative" @click.away="notifOpen = false">
                    <button
                        type="button"
                        @click="notifOpen = !notifOpen"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-700 transition hover:text-stone-900"
                        aria-label="Notifikasi"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a3 3 0 006 0" />
                        </svg>
                        @if ($notificationCount > 0)
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-amber-500"></span>
                        @endif
                    </button>

                    <div
                        x-cloak
                        x-show="notifOpen"
                        x-transition
                        class="absolute right-0 mt-2 w-72 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm"
                    >
                        <div class="text-xs font-semibold uppercase tracking-wide text-stone-500">Notifikasi</div>
                        <div class="mt-3 space-y-2">
                            @forelse ($latestNotifications as $notification)
                                @php
                                    $url = $notification->data['url'] ?? null;
                                @endphp
                                <div class="rounded-xl border border-stone-100 bg-stone-50 p-3 text-xs text-stone-700">
                                    @if ($url)
                                        <a href="{{ $url }}" class="font-semibold text-stone-900 hover:text-blue-700">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </a>
                                    @else
                                        <div class="font-semibold text-stone-900">
                                            {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                                        </div>
                                    @endif
                                    <div class="mt-1 text-[11px] text-stone-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border border-stone-100 bg-stone-50 p-3 text-xs text-stone-600">
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
                        class="inline-flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border-2 border-blue-600 text-blue-600 transition hover:bg-blue-50"
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
                        class="absolute right-0 mt-2 w-48 rounded-2xl border border-stone-200 bg-white p-2 shadow-sm"
                    >
                        <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" x-ref="logoutDesktopForm">
                            @csrf
                            <button
                                type="button"
                                class="block w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-stone-700 hover:bg-stone-50"
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
                class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-3 py-2 text-sm font-semibold text-stone-700 md:hidden"
                aria-label="Toggle menu"
            >
                Menu
            </button>
        </div>

        <div x-cloak x-show="open" x-transition class="mt-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm md:hidden">
            <nav class="flex flex-col gap-3 text-sm font-semibold text-stone-700">
                @foreach ($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ request()->routeIs($item['active']) ? 'text-stone-900' : 'text-stone-600 hover:text-stone-900' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <div class="mt-2 rounded-2xl border border-stone-200 bg-stone-50 p-3">
                    <div class="text-sm font-semibold text-stone-900">{{ $user->name }}</div>
                    <div class="text-xs text-stone-500">{{ $user->email }}</div>
                    <div class="mt-3 flex flex-col gap-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-xl border border-stone-200 px-3 py-2 text-sm font-semibold text-stone-700">
                            Profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" x-ref="logoutMobileForm">
                            @csrf
                            <button
                                type="button"
                                class="w-full rounded-xl border border-stone-200 px-3 py-2 text-left text-sm font-semibold text-stone-700"
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
        class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 px-4"
    >
        <div class="w-full max-w-sm rounded-2xl border border-stone-200 bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-stone-900">Konfirmasi Keluar</h3>
            <p class="mt-2 text-sm text-stone-600">Anda yakin ingin keluar dari akun?</p>
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
                    Keluar
                </button>
            </div>
        </div>
    </div>
</nav>

<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-accent">
                Akses Pelanggan
            </p>
            <h1 class="mt-3 text-2xl font-bold text-ink">Masuk ke Jahitly</h1>
            <p class="mt-1 text-sm text-muted">Kelola pesanan dan pantau progres jahitan Anda.</p>
        </div>

        <x-auth-session-status
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" class="text-xs font-bold uppercase tracking-wide text-ink/60" />
                <x-text-input
                    id="email"
                    class="mt-2 block w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-ink placeholder-muted/50 transition focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600" />
            </div>

            <div x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Kata Sandi')" class="text-xs font-bold uppercase tracking-wide text-ink/60" />
                <div class="relative mt-2">
                    <x-text-input
                        id="password"
                        type="password"
                        class="block w-full rounded-xl border border-border bg-surface px-4 py-3 pr-11 text-sm text-ink placeholder-muted/50 transition focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 z-10 flex items-center px-3 text-muted hover:text-ink focus:outline-none"
                        @click="showPassword = !showPassword"
                        aria-label="Tampilkan kata sandi"
                    >
                        <svg x-show="!showPassword" class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="h-5 w-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5a3 3 0 004.243 4.243" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.23 6.23C4.88 7.55 3.73 9.47 2.46 12c1.274 4.057 5.064 7 9.542 7 1.9 0 3.67-.53 5.17-1.44" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-600" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-muted">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-border text-primary focus:ring-primary/30"
                        name="remember"
                    >
                    Ingat saya
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-primary hover:text-primary-hover" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <button
                type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3.5 text-sm font-bold text-white transition hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/30"
            >
                Masuk ke Jahitly
            </button>
        </form>

        <p class="text-center text-sm text-muted">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-hover">Daftar sekarang</a>
        </p>
    </div>
</x-guest-layout>

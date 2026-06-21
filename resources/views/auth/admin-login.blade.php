<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <p class="inline-flex items-center gap-2 rounded-full bg-stone-900 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Admin Portal
            </p>
            <h1 class="mt-4 text-2xl font-bold text-stone-900">Login Administrator</h1>
            <p class="mt-1 text-sm text-stone-500">Masuk untuk mengelola pesanan & sistem Jahitly.</p>
        </div>

        <x-auth-session-status
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-semibold text-stone-700" />
                <x-text-input
                    id="email"
                    class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@jahitly.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600" />
            </div>

            <div x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Kata Sandi')" class="text-sm font-semibold text-stone-700" />
                <div class="relative mt-2">
                    <x-text-input
                        id="password"
                        type="password"
                        class="block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 pr-11 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 z-10 flex items-center px-3 text-stone-400 hover:text-stone-700 focus:outline-none"
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

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-stone-600">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-stone-300 text-stone-900 focus:ring-stone-900/30"
                        name="remember"
                    >
                    Ingat sesi saya
                </label>
            </div>

            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-900/30"
            >
                Masuk Dashboard
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <p class="text-center text-sm text-stone-500">
            Hanya untuk staf resmi Jahitly.
        </p>
    </div>
</x-guest-layout>

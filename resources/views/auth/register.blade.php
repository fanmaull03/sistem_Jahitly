<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1
                      text-xs font-bold uppercase tracking-widest text-primary">
                Mulai Pesanan
            </p>
            <h1 class="mt-3 font-display text-2xl font-bold text-ink">Buat Akun Jahitly</h1>
            <p class="mt-1 text-sm text-muted">Daftar agar Anda bisa memantau jahitan dan pembayaran.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input
                    id="name"
                    class="mt-1.5 block w-full"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nama Anda"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input
                    id="email"
                    class="mt-1.5 block w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div x-data="{ showPassword: false }">
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <div class="relative mt-1.5">
                    <x-text-input
                        id="password"
                        class="block w-full pr-11"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Buat kata sandi"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-primary transition"
                        @click="showPassword = !showPassword"
                        aria-label="Tampilkan kata sandi"
                    >
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5a3 3 0 004.243 4.243" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.23 6.23C4.88 7.55 3.73 9.47 2.46 12c1.274 4.057 5.064 7 9.542 7 1.9 0 3.67-.53 5.17-1.44" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div x-data="{ showPasswordConfirm: false }">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                <div class="relative mt-1.5">
                    <x-text-input
                        id="password_confirmation"
                        class="block w-full pr-11"
                        x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                    />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-primary transition"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                        aria-label="Tampilkan konfirmasi kata sandi"
                    >
                        <svg x-show="!showPasswordConfirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg x-show="showPasswordConfirm" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5a3 3 0 004.243 4.243" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.23 6.23C4.88 7.55 3.73 9.47 2.46 12c1.274 4.057 5.064 7 9.542 7 1.9 0 3.67-.53 5.17-1.44" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm
                       transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                Daftar Akun
            </button>
        </form>

        <p class="text-center text-sm text-muted">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-hover transition">
                Masuk
            </a>
        </p>
    </div>
</x-guest-layout>

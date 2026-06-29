<x-guest-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1
                      text-xs font-bold uppercase tracking-widest text-primary">
                Buat Kata Sandi Baru
            </p>
            <h1 class="mt-3 font-display text-2xl font-bold text-ink">Reset Kata Sandi</h1>
            <p class="mt-1 text-sm text-muted">Masukkan kata sandi baru yang kuat untuk akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" type="email" name="email" class="mt-1.5 block w-full"
                    :value="old('email', $request->email)" required autofocus autocomplete="username"
                    placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div x-data="{ show: false }">
                <x-input-label for="password" :value="__('Kata Sandi Baru')" />
                <div class="relative mt-1.5">
                    <x-text-input id="password" name="password" class="block w-full pr-11"
                        x-bind:type="show ? 'text' : 'password'" required autocomplete="new-password"
                        placeholder="Buat kata sandi baru" />
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-primary transition">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 10.5a3 3 0 004.243 4.243"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.23 6.23C4.88 7.55 3.73 9.47 2.46 12c1.274 4.057 5.064 7 9.542 7 1.9 0 3.67-.53 5.17-1.44"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                    class="mt-1.5 block w-full" required autocomplete="new-password"
                    placeholder="Ulangi kata sandi" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm
                       transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                Simpan Kata Sandi Baru
            </button>
        </form>
    </div>
</x-guest-layout>

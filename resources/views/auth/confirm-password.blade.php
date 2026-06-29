<x-guest-layout>
    <div class="space-y-6">
        <div>
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary/10 mb-4">
                <svg class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h1 class="font-display text-2xl font-bold text-ink">Konfirmasi Kata Sandi</h1>
            <p class="mt-1 text-sm text-muted">
                Area ini memerlukan konfirmasi ulang kata sandi untuk melanjutkan.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf
            <div>
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <x-text-input id="password" type="password" name="password"
                    class="mt-1.5 block w-full" required autocomplete="current-password"
                    placeholder="Masukkan kata sandi Anda" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white
                       transition hover:bg-primary-hover">
                Konfirmasi
            </button>
        </form>
    </div>
</x-guest-layout>

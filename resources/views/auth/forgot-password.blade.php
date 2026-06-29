<x-guest-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1
                      text-xs font-bold uppercase tracking-widest text-primary">
                Reset Kata Sandi
            </p>
            <h1 class="mt-3 font-display text-2xl font-bold text-ink">Lupa Kata Sandi?</h1>
            <p class="mt-1 text-sm text-muted">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.
            </p>
        </div>

        {{-- Session status --}}
        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input
                    id="email" type="email" name="email"
                    class="mt-1.5 block w-full"
                    :value="old('email')" required autofocus
                    placeholder="nama@email.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm
                       transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                Kirim Tautan Reset
            </button>
        </form>

        <p class="text-center text-sm text-muted">
            Ingat kata sandi?
            <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-hover transition">
                Kembali Masuk
            </a>
        </p>
    </div>
</x-guest-layout>

<x-guest-layout>
    <div class="space-y-6 text-center">
        {{-- Icon --}}
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mx-auto">
            <svg class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
            </svg>
        </div>

        <div>
            <h1 class="font-display text-2xl font-bold text-ink">Verifikasi Email Anda</h1>
            <p class="mt-2 text-sm text-muted max-w-sm mx-auto">
                Kami telah mengirim tautan verifikasi ke email Anda. Silakan cek kotak masuk (dan folder spam jika tidak ada).
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tautan baru telah dikirim ke email Anda.
            </div>
        @endif

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white
                           transition hover:bg-primary-hover">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-xl border border-border px-4 py-2.5 text-sm font-semibold
                           text-muted hover:border-primary hover:text-primary transition">
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>

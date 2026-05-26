<section>
    <header>
        <h2 class="text-lg font-bold text-stone-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-stone-500">
            Perbarui informasi akun dan kontak Anda agar kami mudah menghubungi Anda terkait pesanan jahitan.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-semibold text-stone-700" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-semibold text-stone-700" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-stone-600">
                        Email Anda belum terverifikasi.
                        <button
                            form="send-verification"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                        >
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-emerald-600">
                            Tautan verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Nomor WhatsApp / Telepon')" class="text-sm font-semibold text-stone-700" />
            <x-text-input
                id="phone"
                name="phone"
                type="tel"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                :value="old('phone', $user->phone ?? '')"
                autocomplete="tel"
                placeholder="Contoh: 0812 3456 7890"
            />
            <p class="mt-2 text-xs text-stone-500">Penting untuk konfirmasi fitting/bahan.</p>
        </div>

        <div>
            <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-sm font-semibold text-stone-700" />
            <textarea
                id="address"
                name="address"
                rows="3"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                placeholder="Tulis alamat lengkap untuk pengiriman/pengambilan"
            >{{ old('address', $user->address ?? '') }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
            >
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-stone-500"
                >Berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>

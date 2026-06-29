<section>
    <header>
        <h2 class="text-base font-bold text-ink dark:text-stone-100">
            Informasi Profil
        </h2>
    </header>

    @php
        $profilePhotoUrl = $user->profile_photo_path
            ? asset('storage/' . $user->profile_photo_path)
            : null;
        $profileInitial = strtoupper(substr($user->name ?? 'U', 0, 1));
    @endphp

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="mt-6 grid gap-4 sm:grid-cols-2"
    >
        @csrf
        @method('patch')

        <div class="sm:col-span-2 flex flex-wrap items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full
                        border border-border bg-surface dark:border-stone-700 dark:bg-stone-800">
                @if ($profilePhotoUrl)
                    <img src="{{ $profilePhotoUrl }}" alt="Foto profil" class="h-full w-full object-cover" />
                @else
                    <span class="text-lg font-bold text-primary/60">{{ $profileInitial }}</span>
                @endif
            </div>
            <div class="flex-1">
                <x-input-label for="profile_photo" :value="__('Foto Profil')" />
                <input
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/*"
                    class="mt-1.5 block w-full rounded-xl border border-border bg-white px-4 py-2 text-sm text-ink
                           file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-1.5
                           file:text-sm file:font-semibold file:text-primary
                           hover:file:bg-primary/20
                           dark:border-stone-600 dark:bg-stone-900 dark:text-stone-100"
                />
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                <p class="mt-1.5 text-xs text-muted dark:text-stone-400">PNG/JPG maksimal 2MB.</p>
            </div>
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1.5 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1.5 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-muted dark:text-stone-400">
                        Email Anda belum terverifikasi.
                        <button
                            form="send-verification"
                            class="text-sm font-semibold text-primary hover:text-primary-hover transition"
                        >
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                            Tautan verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Nomor WhatsApp')" />
            <x-text-input
                id="phone"
                name="phone"
                type="tel"
                class="mt-1.5 block w-full"
                :value="old('phone', $user->phone ?? '')"
                autocomplete="tel"
                placeholder="081234567890"
            />
        </div>

        <div class="sm:col-span-2">
            <x-input-label for="address" :value="__('Alamat Lengkap')" />
            <textarea
                id="address"
                name="address"
                rows="3"
                class="mt-1.5 block w-full rounded-xl border border-border bg-surface px-4 py-2.5 text-sm text-ink
                       placeholder-muted/50 transition focus:border-primary focus:outline-none focus:ring-2
                       focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                placeholder="Tulis alamat lengkap untuk pengiriman/pengambilan"
            >{{ old('address', $user->address ?? '') }}</textarea>
        </div>

        <div class="sm:col-span-2 flex items-center justify-end gap-4">
            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-600 dark:text-emerald-400"
                >Profil berhasil disimpan.</p>
            @endif

            <x-primary-button>
                Simpan Perubahan
            </x-primary-button>
        </div>
    </form>
</section>

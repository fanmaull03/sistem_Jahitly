<section>
    <header>
        <h2 class="text-base font-bold text-ink dark:text-stone-100">
            Keamanan
        </h2>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf
        @method('put')

        <div class="sm:col-span-2">
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1.5 block w-full"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1.5 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1.5 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="sm:col-span-2 flex items-center justify-end gap-4">
            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-600 dark:text-emerald-400"
                >Kata sandi berhasil diperbarui.</p>
            @endif

            <x-primary-button>
                Perbarui Password
            </x-primary-button>
        </div>
    </form>
</section>

<section>
    <header>
        <h2 class="text-base font-semibold text-stone-900 dark:text-stone-100">
            Keamanan
        </h2>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
        @csrf
        @method('put')

        <div class="sm:col-span-2">
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="text-sm font-semibold text-stone-700 dark:text-stone-300" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-stone-600 dark:bg-stone-900 dark:text-stone-100 dark:focus:border-blue-500"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" class="text-sm font-semibold text-stone-700 dark:text-stone-300" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-stone-600 dark:bg-stone-900 dark:text-stone-100 dark:focus:border-blue-500"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" class="text-sm font-semibold text-stone-700 dark:text-stone-300" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-stone-600 dark:bg-stone-900 dark:text-stone-100 dark:focus:border-blue-500"
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
                    class="text-xs text-stone-500 dark:text-stone-400"
                >Berhasil disimpan.</p>
            @endif

            <button
                type="submit"
                class="rounded-lg bg-stone-100 px-5 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-200 focus:outline-none focus:ring-2 focus:ring-stone-300"
            >
                Perbarui Password
            </button>
        </div>
    </form>
</section>

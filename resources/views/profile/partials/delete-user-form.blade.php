<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-stone-900">
            Hapus Akun
        </h2>

        <p class="mt-1 text-sm text-stone-500">
            Setelah akun Anda dihapus, semua data pesanan dan riwayat jahitan akan dihapus secara permanen. Pastikan Anda tidak memiliki pesanan yang sedang berjalan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:text-red-700"
    >
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-stone-900">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="mt-1 text-sm text-stone-500">
                Setelah akun dihapus, semua data pesanan dan riwayat jahitan akan hilang permanen. Masukkan kata sandi untuk mengonfirmasi penghapusan akun.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 block w-full rounded-lg border border-stone-200 bg-stone-50 px-4 py-3 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                    placeholder="Kata sandi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="rounded-lg border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 hover:bg-stone-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>

<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-ink dark:text-stone-100">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-muted dark:text-stone-400">
            Akun yang dihapus tidak dapat dipulihkan. Pastikan tidak ada pesanan yang masih berjalan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold
               text-rose-600 transition hover:bg-rose-100
               dark:border-rose-800 dark:bg-rose-900/10 dark:text-rose-400"
    >
        Hapus Akun Saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-ink dark:text-stone-100">
                Apakah Anda yakin?
            </h2>

            <p class="mt-1 text-sm text-muted dark:text-stone-400">
                Setelah akun dihapus, semua data pesanan dan riwayat jahitan akan hilang permanen. Masukkan kata sandi untuk konfirmasi.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Kata Sandi') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1.5 block w-full"
                    placeholder="Masukkan kata sandi"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

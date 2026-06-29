<div class="page-enter space-y-6">
    <x-slot name="header">Kelola Vermak</x-slot>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-primary/60">Layanan</p>
            <h1 class="mt-0.5 text-2xl font-bold text-ink dark:text-stone-100">Kelola Vermak</h1>
            <p class="text-sm text-muted dark:text-stone-400">Atur opsi layanan vermak yang tersedia beserta harganya.</p>
        </div>
        <button wire:click="openModal" class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Opsi Vermak
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-xl border border-border border-l-4 border-l-emerald-500 bg-emerald-500/5 p-4 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface dark:border-stone-700 dark:bg-stone-800/60">
                        <th class="whitespace-nowrap px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Nama Layanan</th>
                        <th class="whitespace-nowrap px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Deskripsi</th>
                        <th class="whitespace-nowrap px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted">Harga</th>
                        <th class="whitespace-nowrap px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-muted text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border dark:divide-stone-700">
                    @forelse ($vermaks as $vermak)
                        <tr class="transition hover:bg-primary/5 dark:hover:bg-primary/10 group">
                            <td class="whitespace-nowrap px-6 py-4 font-bold text-ink dark:text-stone-100">
                                {{ $vermak->name }}
                            </td>
                            <td class="px-6 py-4 text-muted dark:text-stone-300">
                                {{ $vermak->description ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-extrabold text-ink dark:text-stone-100">
                                Rp {{ number_format($vermak->price, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="editVermak({{ $vermak->id }})" class="rounded-lg border border-border bg-white px-3 py-1.5 text-xs font-bold text-ink transition hover:border-primary hover:text-primary dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $vermak->id }})" wire:confirm="Apakah Anda yakin ingin menghapus opsi vermak ini?" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-400">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-muted">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-surface mb-4">
                                    <svg class="h-8 w-8 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-ink dark:text-stone-300">Belum ada opsi layanan vermak.</p>
                                <p class="mt-1 text-xs text-muted dark:text-stone-400">Klik "Tambah Opsi Vermak" untuk menambahkan layanan baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-ink/60 backdrop-blur-sm p-4">
            <div class="modal-content-enter relative w-full max-w-md rounded-3xl bg-white shadow-2xl dark:bg-stone-800 border border-border dark:border-stone-700 overflow-hidden">
                <div class="flex items-center justify-between border-b border-border p-6 dark:border-stone-700">
                    <div>
                        <h3 class="text-lg font-bold text-ink dark:text-stone-100">
                            {{ $editMode ? 'Edit Layanan Vermak' : 'Tambah Layanan Vermak' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="rounded-full p-2 text-muted transition hover:bg-surface hover:text-ink dark:hover:bg-stone-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-muted">Nama Layanan</label>
                            <input type="text" wire:model="name" class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100" placeholder="Misal: Potong Celana">
                            @error('name') <span class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-muted">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="3" class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100" placeholder="Keterangan singkat..."></textarea>
                            @error('description') <span class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-muted">Harga (Rp)</label>
                            <input type="number" wire:model="price" class="block w-full rounded-xl border border-border bg-white px-4 py-2.5 text-sm text-ink shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100" placeholder="Misal: 25000">
                            @error('price') <span class="mt-1.5 text-xs font-bold text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-border bg-surface p-6 dark:border-stone-700 dark:bg-stone-800/80">
                        <button type="button" wire:click="closeModal" class="rounded-xl border border-border bg-white px-5 py-2.5 text-sm font-bold text-ink transition hover:border-primary hover:text-primary dark:bg-stone-800 dark:border-stone-600 dark:text-stone-300">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

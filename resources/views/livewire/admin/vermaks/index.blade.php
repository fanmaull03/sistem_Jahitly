<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Layanan Vermak</h1>
            <p class="text-sm text-slate-500">Atur opsi layanan vermak yang tersedia beserta harganya.</p>
        </div>
        <button wire:click="openModal" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Opsi Vermak
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 rounded-xl bg-green-50 p-4 text-sm text-green-800 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-900">
                    <tr>
                        <th class="whitespace-nowrap px-6 py-4 font-semibold">Nama Layanan</th>
                        <th class="whitespace-nowrap px-6 py-4 font-semibold">Deskripsi</th>
                        <th class="whitespace-nowrap px-6 py-4 font-semibold">Harga</th>
                        <th class="whitespace-nowrap px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($vermaks as $vermak)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">
                                {{ $vermak->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $vermak->description ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                Rp {{ number_format($vermak->price, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <button wire:click="editVermak({{ $vermak->id }})" class="mr-2 inline-flex items-center text-sky-600 hover:text-sky-900 font-medium">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $vermak->id }})" wire:confirm="Apakah Anda yakin ingin menghapus opsi vermak ini?" class="inline-flex items-center text-rose-600 hover:text-rose-900 font-medium">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                                Belum ada opsi layanan vermak yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $editMode ? 'Edit Layanan Vermak' : 'Tambah Layanan Vermak' }}
                    </h3>
                    <button wire:click="closeModal" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Layanan</label>
                        <input type="text" wire:model="name" class="block w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400" placeholder="Misal: Potong Celana">
                        @error('name') <span class="mt-1 text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi (Opsional)</label>
                        <textarea wire:model="description" rows="3" class="block w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400" placeholder="Keterangan singkat..."></textarea>
                        @error('description') <span class="mt-1 text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Harga (Rp)</label>
                        <input type="number" wire:model="price" class="block w-full rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400" placeholder="Misal: 25000">
                        @error('price') <span class="mt-1 text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

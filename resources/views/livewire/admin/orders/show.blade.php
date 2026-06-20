<div class="page-enter space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Detail Pesanan</h1>
        <p class="text-sm text-slate-500">Kontrol pesanan untuk customer {{ $order->customer->name }}.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <div class="text-lg font-semibold text-slate-900">{{ $order->order_number }}</div>
        <x-status-badge :status="$order->status" />
        <x-status-badge type="payment_status" :status="$order->payment_status" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Informasi Pesanan</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <div class="text-sm text-slate-500">Customer</div>
                        <div class="text-base font-semibold">{{ $order->customer->name }}</div>
                        <div class="text-sm text-slate-500">{{ $order->customer->email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Layanan</div>
                        <div class="text-base font-semibold">{{ $order->service->name }}</div>
                        <div class="text-sm text-slate-500">{{ strtoupper($order->service->type) }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Estimasi Selesai</div>
                        <div class="text-base font-semibold">
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Estimasi Harga</div>
                        <div class="text-base font-semibold">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">DP (Ditetapkan Admin)</div>
                        <div class="text-base font-semibold text-orange-600">
                            {{ $order->dp_amount ? 'Rp ' . number_format((float) $order->dp_amount, 0, ',', '.') : 'Belum ditetapkan' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Appointment</div>
                        <div class="text-base font-semibold">
                            {{ $order->appointment?->appointment_date?->format('d M Y H:i') ?? '-' }}
                        </div>
                        @if ($order->appointment)
                            <x-status-badge type="appointment" :status="$order->appointment->status" />
                        @endif
                    </div>
                    <div>
                        <div class="text-sm text-slate-500">Jumlah Item</div>
                        <div class="text-base font-semibold">{{ $order->quantity }}</div>
                    </div>
                </div>
                @if ($order->notes)
                    <div class="mt-4 text-sm text-slate-600">
                        <div class="font-semibold text-slate-900">Catatan Customer</div>
                        <p class="mt-1">{{ $order->notes }}</p>
                    </div>
                @endif
                
                <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="text-sm font-semibold text-slate-900">Informasi Bahan</div>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2 text-sm">
                        <div>
                            <span class="text-slate-500">Sumber Bahan:</span>
                            <span class="font-medium text-slate-900">
                                {{ $order->material_source === 'customer' ? 'Bawa Sendiri' : ($order->material_source === 'jasa' ? 'Beli di Penjahit' : 'Belum ditentukan') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-500">Status Bahan:</span>
                            <span class="font-medium text-slate-900">
                                @if($order->material_status)
                                    <x-status-badge type="material" :status="$order->material_status" />
                                @else
                                    Belum ditentukan
                                @endif
                            </span>
                        </div>
                    </div>

                    @if ($order->fabric)
                        <div class="mt-3 border-t border-slate-200 pt-3">
                            <div class="text-xs font-semibold text-slate-500 mb-2">DETAIL KAIN:</div>
                            <div class="grid gap-2 sm:grid-cols-2 text-sm">
                                <div>
                                    <span class="text-slate-500">Nama:</span>
                                    <span class="font-medium text-slate-900">{{ $order->fabric->name }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500">Warna:</span>
                                    <span class="font-medium text-slate-900">{{ $order->fabric->color }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500">Kategori:</span>
                                    <span class="font-medium text-slate-900">{{ $order->fabric->category_label }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-500">Harga:</span>
                                    <span class="font-medium text-slate-900">Rp {{ number_format((float) $order->fabric->price_per_meter, 0, ',', '.') }}/m</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Timeline Status</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($statusLogs as $log)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$log->status" />
                                <div class="text-xs text-slate-500">{{ $log->created_at->format('d M Y H:i') }}</div>
                                <div class="text-xs text-slate-500">oleh {{ $log->user?->name ?? 'System' }}</div>
                            </div>
                            @if ($log->notes)
                                <div class="mt-2 text-sm text-slate-600">{{ $log->notes }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada riwayat status.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Daftar Pembayaran</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($order->payments as $payment)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 p-3">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ strtoupper($payment->payment_type) }} - Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-500">Metode: {{ strtoupper($payment->payment_method) }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge type="payment" :status="$payment->status" />
                                @if ($payment->proof_file_path)
                                    <a
                                        href="{{ route('payments.proof', $payment) }}"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-slate-300"
                                        target="_blank"
                                    >
                                        Lihat Bukti
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            {{-- ── Panel Aksi Admin berdasarkan Status ── --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Aksi Admin</h2>
                
                <div class="mt-4">
                    {{-- 1. Menunggu Konfirmasi --}}
                    @if ($order->status === 'menunggu_konfirmasi')
                        <div class="space-y-3">
                            <p class="text-sm text-slate-600">Pesanan baru. Silakan terima atau tolak.</p>
                            <div class="flex gap-2">
                                <button type="button" wire:click="acceptOrder" class="flex-1 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                    Terima Pesanan
                                </button>
                                <button type="button" wire:click="openRejectForm" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                                    Tolak
                                </button>
                            </div>

                            @if ($showRejectForm)
                                <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
                                    <label class="text-sm font-semibold text-slate-700">Alasan Penolakan</label>
                                    <textarea wire:model="rejectionReason" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
                                    @error('rejectionReason') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="rejectOrder" class="flex-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Konfirmasi Tolak</button>
                                        <button type="button" wire:click="closeRejectForm" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-white">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                    {{-- 2. Menunggu Fitting --}}
                    @elseif ($order->status === 'menunggu_fitting')
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm text-amber-800">
                                Menunggu customer mengatur dan menyelesaikan jadwal fitting. <br>
                                <a href="{{ route('admin.appointments.index') }}" class="font-semibold underline" wire:navigate>Lihat Jadwal Appointment</a>
                            </p>
                        </div>

                    {{-- 3. Menunggu DP --}}
                    @elseif ($order->status === 'menunggu_dp')
                        <div class="space-y-4">
                            @if (!$order->dp_amount)
                                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                                    <p class="text-sm text-orange-800 mb-3">
                                        Silakan tetapkan nominal DP yang harus dibayar customer.
                                    </p>
                                    <button type="button" wire:click="openDpForm" class="w-full rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">
                                        Set Nominal DP
                                    </button>
                                </div>
                            @else
                                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                                    <p class="text-sm text-sky-800">
                                        DP ditetapkan sebesar <strong>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</strong>.<br>
                                        Menunggu customer melakukan pembayaran.
                                    </p>
                                    <button type="button" wire:click="openDpForm" class="mt-3 w-full rounded-xl border border-sky-300 px-4 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-100">
                                        Ubah Nominal DP
                                    </button>
                                </div>
                            @endif

                            @if ($showDpForm)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <label class="text-sm font-semibold text-slate-700">Nominal DP (Rp)</label>
                                    <input type="number" wire:model="dpAmount" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                    @error('dpAmount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="setDpAmount" class="flex-1 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Simpan DP</button>
                                        <button type="button" wire:click="closeDpForm" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                    {{-- 4. Menunggu Bahan --}}
                    @elseif ($order->status === 'menunggu_bahan')
                        <div class="space-y-4">
                            <p class="text-sm text-slate-600">Tetapkan bahan yang akan digunakan.</p>
                            
                            <button type="button" wire:click="openMaterialForm" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Set / Ubah Bahan
                            </button>

                            @if ($showMaterialForm)
                                <div class="rounded-xl border border-slate-200 p-4 space-y-3">
                                    <div>
                                        <label class="text-sm font-semibold text-slate-700">Sumber Bahan</label>
                                        <select wire:model.live="material_source" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                            <option value="">Pilih sumber</option>
                                            <option value="customer">Bawa Sendiri</option>
                                            <option value="jasa">Beli di Penjahit</option>
                                        </select>
                                        @error('material_source') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    @if ($material_source === 'jasa')
                                        <div>
                                            <label class="text-sm font-semibold text-slate-700">Pilih Kain</label>
                                            <select wire:model.live="fabric_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                                <option value="">Pilih kain...</option>
                                                @foreach($fabrics as $fabric)
                                                    <option value="{{ $fabric->id }}">{{ $fabric->name }} - {{ $fabric->color }} (Rp {{ number_format($fabric->price_per_meter, 0, ',', '.') }}/m)</option>
                                                @endforeach
                                            </select>
                                            @error('fabric_id') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    @endif

                                    <div>
                                        <label class="text-sm font-semibold text-slate-700">Status Bahan</label>
                                        <select wire:model.live="material_status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                            <option value="">Pilih status</option>
                                            <option value="ready">Ready / Tersedia</option>
                                            <option value="po">Pre-Order (PO)</option>
                                        </select>
                                        @error('material_status') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    @if ($material_status === 'po')
                                        <div>
                                            <label class="text-sm font-semibold text-slate-700">Durasi PO (Hari)</label>
                                            <input type="number" wire:model="poDays" min="3" max="7" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                            @error('poDays') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="updateMaterial" class="flex-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Simpan</button>
                                        <button type="button" wire:click="closeMaterialForm" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                                    </div>
                                </div>
                            @endif

                            @if ($order->material_status === 'po')
                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-sm text-amber-800 mb-3">
                                        Bahan sedang di-PO (~{{ $order->po_days }} hari). Klik tombol di bawah jika bahan sudah tiba.
                                    </p>
                                    <button type="button" wire:click="markMaterialReady" class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                        Tandai Bahan Ready
                                    </button>
                                </div>
                            @elseif ($order->material_status === 'ready')
                                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                                    <p class="text-sm text-emerald-800 mb-3">
                                        Bahan sudah berstatus "Ready". Klik tombol di bawah untuk memasukkan pesanan ke antrian produksi.
                                    </p>
                                    <button type="button" wire:click="forceMoveToQueue" class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                        Masukkan ke Antrian Produksi
                                    </button>
                                </div>
                            @endif
                        </div>

                    {{-- 5. Dalam Antrian --}}
                    @elseif ($order->status === 'dalam_antrian')
                        <div class="space-y-4">
                            <p class="text-sm text-slate-600">Pesanan siap diproduksi.</p>
                            
                            <button type="button" wire:click="openProductionForm" class="w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Mulai Proses Jahit
                            </button>

                            @if ($showProductionForm)
                                <div class="rounded-xl border border-slate-200 p-4">
                                    <label class="text-sm font-semibold text-slate-700">Estimasi Hari Pengerjaan</label>
                                    <input type="number" wire:model="productionDays" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                    @error('productionDays') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="startProduction" class="flex-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">Mulai Produksi</button>
                                        <button type="button" wire:click="closeProductionForm" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                    {{-- 6. Dijahit --}}
                    @elseif ($order->status === 'dijahit')
                        <div class="space-y-4">
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                                <p class="text-sm text-indigo-800">
                                    Sedang dikerjakan. Estimasi selesai: <strong>{{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}</strong>.
                                </p>
                            </div>
                            <button type="button" wire:click="finishProduction" class="w-full rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">
                                Tandai Selesai Produksi
                            </button>
                        </div>

                    {{-- 7. Selesai Produksi --}}
                    @elseif ($order->status === 'selesai_produksi')
                        @php
                            $totalVerified = $order->payments->where('status', 'terverifikasi')->sum('amount');
                            $isPaid = $totalVerified >= $order->estimated_price;
                        @endphp
                        
                        <div class="space-y-4">
                            @if ($isPaid)
                                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 mb-3">
                                    <p class="text-sm text-emerald-800 font-semibold">Pelunasan sudah lunas.</p>
                                </div>
                                <button type="button" wire:click="markReadyForPickup" class="w-full rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                                    Tandai Siap Diambil
                                </button>
                            @else
                                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                                    <p class="text-sm text-rose-800">
                                        Menunggu customer melakukan pelunasan sisa tagihan sebesar <strong>Rp {{ number_format($order->estimated_price - $totalVerified, 0, ',', '.') }}</strong>.
                                    </p>
                                </div>
                                <button type="button" class="w-full rounded-xl bg-slate-300 px-4 py-2 text-sm font-semibold text-slate-500 cursor-not-allowed" disabled>
                                    Tandai Siap Diambil (Belum Lunas)
                                </button>
                            @endif
                        </div>

                    {{-- 8. Siap Diambil --}}
                    @elseif ($order->status === 'siap_diambil')
                        <div class="space-y-4">
                            <p class="text-sm text-slate-600">Menunggu customer mengambil pakaian.</p>
                            <button type="button" wire:click="markComplete" class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                Tandai Pesanan Selesai
                            </button>
                        </div>

                    {{-- 9. Selesai / Ditolak / Dibatalkan --}}
                    @else
                        <p class="text-sm text-slate-500">Pesanan ini sudah berada di status akhir ({{ str_replace('_', ' ', $order->status) }}) dan tidak dapat diubah lagi.</p>
                    @endif
                </div>
            </section>

            {{-- ── Edit Harga Pesanan (Khusus sebelum lunas) ── --}}
            @if (!in_array($order->status, ['siap_diambil', 'selesai', 'ditolak', 'dibatalkan']))
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Edit Total Harga</h2>
                        @if (!$showPriceForm)
                            <button type="button" wire:click="openPriceForm" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-slate-300 hover:bg-slate-50">
                                Edit
                            </button>
                        @endif
                    </div>

                    <div class="mt-3">
                        <div class="text-sm text-slate-500">Harga Saat Ini</div>
                        <div class="text-xl font-bold text-slate-900">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </div>
                    </div>

                    @if ($showPriceForm)
                        <div class="mt-4 space-y-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Harga Baru (Rp)</label>
                                <input type="number" wire:model="editEstimatedPrice" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold">
                                @error('editEstimatedPrice') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="updatePrice" class="flex-1 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
                                <button type="button" wire:click="closePriceForm" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Preview Desain</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($order->designFiles as $file)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2">
                            <div class="text-sm text-slate-700">{{ $file->original_filename }}</div>
                            <button
                                type="button"
                                wire:click="previewDesign({{ $file->id }})"
                                class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-slate-300"
                            >
                                Lihat
                            </button>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada file desain.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>

    @if ($showDesignModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
            <div class="modal-content-enter w-full max-w-2xl rounded-2xl bg-white p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $designPreviewName }}</h3>
                    <button
                        type="button"
                        wire:click="closeDesignPreview"
                        class="rounded-lg border border-slate-200 px-3 py-1 text-sm font-semibold text-slate-600 hover:border-slate-300"
                    >
                        Tutup
                    </button>
                </div>
                <div class="mt-4">
                    <img src="{{ $designPreviewUrl }}" alt="Preview" class="max-h-[70vh] w-full rounded-lg object-contain" />
                </div>
            </div>
        </div>
    @endif
</div>

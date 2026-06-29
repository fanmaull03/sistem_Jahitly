<div class="page-enter space-y-6">
    <div>
        <a href="{{ route('admin.orders.index') }}" wire:navigate
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-primary transition mb-3">
            ← Kembali ke Pesanan
        </a>
        <h1 class="text-2xl font-bold text-ink dark:text-stone-100">Detail Pesanan</h1>
        <p class="text-sm text-muted dark:text-stone-400">Kontrol pesanan untuk customer {{ $order->customer->name }}.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <span class="font-mono text-base font-bold text-ink dark:text-stone-100">{{ $order->order_number }}</span>
        <x-status-badge :status="$order->status" />
        <x-status-badge type="payment_status" :status="$order->payment_status" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="text-base font-bold text-ink dark:text-stone-100">Informasi Pesanan</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Customer</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">{{ $order->customer->name }}</p>
                        <p class="text-xs text-muted dark:text-stone-400">{{ $order->customer->email }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Layanan</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">{{ $order->service->name }}</p>
                        <p class="text-xs text-muted dark:text-stone-400">{{ strtoupper($order->service->type) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Estimasi Selesai</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">
                            {{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Estimasi Harga</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </p>
                    </div>
                    @if ($order->service->type !== 'vermak')
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">DP (Ditetapkan Admin)</p>
                        <p class="mt-1 text-sm font-semibold text-accent dark:text-accent">
                            {{ $order->dp_amount ? 'Rp ' . number_format((float) $order->dp_amount, 0, ',', '.') : 'Belum ditetapkan' }}
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Appointment</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">
                            {{ $order->appointment?->appointment_date?->format('d M Y H:i') ?? '-' }}
                        </p>
                        @if ($order->appointment)
                            <div class="mt-1"><x-status-badge type="appointment" :status="$order->appointment->status" /></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Jumlah Item</p>
                        <p class="mt-1 text-sm font-semibold text-ink dark:text-stone-100">{{ $order->quantity }}</p>
                    </div>
                </div>
                @if ($order->notes)
                    <div class="mt-5 pt-5 border-t border-border dark:border-stone-700">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted dark:text-stone-500">Catatan Customer</p>
                        <p class="mt-1 text-sm text-ink/70 dark:text-stone-300">{{ $order->notes }}</p>
                    </div>
                @endif
                
                @if ($order->service->type === 'vermak')
                    <div class="mt-5 rounded-xl border border-border bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted mb-3">Rincian Vermak</p>
                        @php
                            $vermakDetails = json_decode($order->alteration_details, true) ?? [];
                        @endphp
                        @if (count($vermakDetails) > 0)
                            <ul class="space-y-1 text-sm text-ink/70 dark:text-stone-300">
                                @foreach ($vermakDetails as $detail)
                                    <li class="flex justify-between">
                                        <span>- {{ $detail['name'] ?? 'Vermak' }}</span>
                                        <span class="font-semibold">+Rp {{ number_format($detail['price'] ?? 0, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-muted">Tidak ada rincian vermak tersimpan.</p>
                        @endif
                    </div>
                @else
                    <div class="mt-5 rounded-xl border border-border bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted mb-3">Informasi Bahan</p>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="text-muted dark:text-stone-400">Sumber Bahan:</span>
                                <span class="font-semibold text-ink dark:text-stone-100">
                                    {{ $order->material_source === 'customer' ? 'Bawa Sendiri' : ($order->material_source === 'jasa' ? 'Beli di Penjahit' : 'Belum ditentukan') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-muted dark:text-stone-400">Status Bahan:</span>
                                <span class="font-semibold text-ink dark:text-stone-100">
                                    @if($order->material_status)
                                        <x-status-badge type="material" :status="$order->material_status" />
                                    @else
                                        Belum ditentukan
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if ($order->fabric)
                            <div class="mt-4 border-t border-border pt-4 dark:border-stone-700">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-muted mb-3">Detail Kain</p>
                                <div class="grid gap-3 sm:grid-cols-2 text-sm">
                                    <div>
                                        <span class="text-muted dark:text-stone-400">Nama:</span>
                                        <span class="font-semibold text-ink dark:text-stone-100">{{ $order->fabric->name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted dark:text-stone-400">Warna:</span>
                                        <span class="font-semibold text-ink dark:text-stone-100">{{ $order->fabric->color }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted dark:text-stone-400">Kategori:</span>
                                        <span class="font-semibold text-ink dark:text-stone-100">{{ $order->fabric->category_label }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted dark:text-stone-400">Harga:</span>
                                        <span class="font-semibold text-ink dark:text-stone-100">Rp {{ number_format((float) $order->fabric->price_per_meter, 0, ',', '.') }}/m</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </section>

            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="text-base font-bold text-ink dark:text-stone-100">Timeline Status</h2>
                
                <div class="mt-5 space-y-0">
                    @forelse ($statusLogs as $index => $log)
                        <div class="relative flex gap-4 pb-6 last:pb-0">
                            @if (!$loop->last)
                                <div class="absolute left-3.5 top-7 h-full w-px bg-border dark:bg-stone-700"></div>
                            @endif
                            
                            <div class="relative z-10 flex h-7 w-7 shrink-0 items-center justify-center rounded-full 
                                        {{ $loop->first ? 'bg-primary' : 'bg-surface border-2 border-border dark:bg-stone-700 dark:border-stone-600' }}">
                                <span class="h-2 w-2 rounded-full {{ $loop->first ? 'bg-white' : 'bg-muted/40' }}"></span>
                            </div>
                            
                            <div class="flex-1 pt-0.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-status-badge :status="$log->status" />
                                    <span class="text-xs text-muted dark:text-stone-400">
                                        {{ $log->created_at->format('d M Y H:i') }} · {{ $log->user?->name ?? 'System' }}
                                    </span>
                                </div>
                                @if ($log->notes)
                                    <p class="mt-1.5 text-sm text-muted dark:text-stone-400">{{ $log->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted dark:text-stone-400">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="text-base font-bold text-ink dark:text-stone-100">Daftar Pembayaran</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($order->payments as $payment)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-border 
                                    bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-widest text-muted">
                                    {{ strtoupper($payment->payment_type) }}
                                </p>
                                <p class="mt-0.5 text-lg font-bold text-ink dark:text-stone-100">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-muted">Metode: {{ strtoupper($payment->payment_method) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge type="payment" :status="$payment->status" />
                                @if ($payment->proof_file_path)
                                    <a href="{{ route('payments.proof', $payment) }}" target="_blank"
                                       class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-ink 
                                              hover:border-primary hover:text-primary transition dark:border-stone-600 dark:text-stone-300">
                                        Lihat Bukti ↗
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted dark:text-stone-400">Belum ada pembayaran.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            {{-- ── Panel Aksi Admin berdasarkan Status ── --}}
            <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                <h2 class="text-base font-bold text-ink dark:text-stone-100">Aksi Admin</h2>
                
                <div class="mt-5">
                    {{-- 1. Menunggu Konfirmasi --}}
                    @if ($order->status === 'menunggu_konfirmasi')
                        <div class="space-y-3">
                            <p class="text-sm text-muted">Pesanan baru. Silakan terima atau tolak.</p>
                            <div class="flex gap-2">
                                <button type="button" wire:click="acceptOrder" 
                                        class="flex-1 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                    Terima Pesanan
                                </button>
                                <button type="button" wire:click="openRejectForm" 
                                        class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-400">
                                    Tolak
                                </button>
                            </div>

                            @if ($showRejectForm)
                                <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900 dark:bg-rose-900/20">
                                    <label class="text-[11px] font-bold uppercase tracking-widest text-rose-700 dark:text-rose-400">Alasan Penolakan</label>
                                    <textarea wire:model="rejectionReason" rows="3" 
                                              class="mt-1.5 w-full rounded-xl border border-rose-200 bg-white px-3 py-2 text-sm 
                                                     focus:border-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-400/20
                                                     dark:bg-stone-800 dark:border-rose-800 dark:text-stone-100"></textarea>
                                    @error('rejectionReason') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="rejectOrder" class="flex-1 rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-700">Konfirmasi Tolak</button>
                                        <button type="button" wire:click="closeRejectForm" class="rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:bg-stone-800 dark:text-rose-400">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                    {{-- 1.5. Menunggu Pakaian Dikirim (Vermak) --}}
                    @elseif ($order->status === 'menunggu_pakaian_dikirim')
                        <div class="rounded-xl border border-border border-l-4 border-l-accent bg-accent/5 p-4">
                            <p class="text-sm text-ink/70 dark:text-stone-300">
                                Menunggu customer mengantar atau mengirimkan pakaian yang akan divermak.
                            </p>
                        </div>

                    {{-- 1.6. Pakaian Dikirim (Vermak) --}}
                    @elseif ($order->status === 'pakaian_dikirim')
                        <div class="space-y-4">
                            <div class="rounded-xl border border-border border-l-4 border-l-primary bg-primary/5 p-4">
                                <p class="text-sm text-ink/70 dark:text-stone-300 mb-3">
                                    Customer telah mengonfirmasi bahwa pakaian sudah dikirim/diantar. Jika pakaian sudah Anda terima, silakan konfirmasi untuk memasukkan pesanan ke antrian produksi.
                                </p>
                                <button type="button" wire:click="markClothesReceived" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                    Konfirmasi Pakaian Diterima
                                </button>
                            </div>
                        </div>

                    {{-- 2. Menunggu Fitting --}}
                    @elseif ($order->status === 'menunggu_fitting')
                        <div class="rounded-xl border border-border border-l-4 border-l-accent bg-accent/5 p-4">
                            <p class="text-sm text-ink/70 dark:text-stone-300">
                                Menunggu customer mengatur dan menyelesaikan jadwal fitting. <br>
                                <a href="{{ route('admin.appointments.index') }}" class="font-semibold text-accent hover:underline mt-1 inline-block" wire:navigate>Lihat Jadwal Appointment →</a>
                            </p>
                        </div>

                    {{-- 3. Menunggu DP --}}
                    @elseif ($order->status === 'menunggu_dp')
                        @if($order->service->type === 'vermak')
                            <div class="space-y-4">
                                <div class="rounded-xl border border-border border-l-4 border-l-primary bg-primary/5 p-4">
                                    <p class="text-sm text-ink/70 dark:text-stone-300 mb-3">
                                        Layanan vermak tidak memerlukan DP. Anda dapat langsung memasukkan pesanan ini ke antrian produksi.
                                    </p>
                                    <button type="button" wire:click="forceMoveToQueue" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                        Lanjut ke Antrian Produksi
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                @if (!$order->dp_amount)
                                    <div class="rounded-xl border border-border border-l-4 border-l-accent bg-accent/5 p-4">
                                        <p class="text-sm text-ink/70 dark:text-stone-300 mb-3">
                                            Silakan tetapkan nominal DP yang harus dibayar customer.
                                        </p>
                                        <button type="button" wire:click="openDpForm" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                            Set Nominal DP
                                        </button>
                                    </div>
                                @else
                                    <div class="rounded-xl border border-border border-l-4 border-l-primary bg-primary/5 p-4">
                                        <p class="text-sm text-ink/70 dark:text-stone-300">
                                            DP ditetapkan sebesar <strong>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</strong>.<br>
                                            Menunggu customer melakukan pembayaran.
                                        </p>
                                        <button type="button" wire:click="openDpForm" class="mt-3 w-full rounded-xl border border-primary px-4 py-2.5 text-sm font-semibold text-primary hover:bg-primary/5 transition dark:hover:bg-primary/20">
                                            Ubah Nominal DP
                                        </button>
                                    </div>
                                @endif

                                @if ($showDpForm)
                                    <div class="rounded-xl border border-border bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Nominal DP (Rp)</label>
                                        <input type="number" wire:model="dpAmount" 
                                               class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                      focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                      dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                        @error('dpAmount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        
                                        <div class="mt-3 flex gap-2">
                                            <button type="button" wire:click="setDpAmount" class="flex-1 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary-hover">Simpan DP</button>
                                            <button type="button" wire:click="closeDpForm" class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted hover:bg-white dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-800">Batal</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                    {{-- 4. Menunggu Bahan --}}
                    @elseif ($order->status === 'menunggu_bahan')
                        <div class="space-y-4">
                            <p class="text-sm text-muted">Tetapkan bahan yang akan digunakan.</p>
                            
                            <button type="button" wire:click="openMaterialForm" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                Set / Ubah Bahan
                            </button>

                            @if ($showMaterialForm)
                                <div class="rounded-xl border border-border bg-surface p-4 space-y-4 dark:border-stone-700 dark:bg-stone-700/30">
                                    <div>
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Sumber Bahan</label>
                                        <select wire:model.live="material_source" 
                                                class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                       dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                            <option value="">Pilih sumber</option>
                                            <option value="customer">Bawa Sendiri</option>
                                            <option value="jasa">Beli di Penjahit</option>
                                        </select>
                                        @error('material_source') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    @if ($material_source === 'jasa')
                                        <div>
                                            <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Pilih Kain</label>
                                            <select wire:model.live="fabric_id" 
                                                    class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                           focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                           dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                                <option value="">Pilih kain...</option>
                                                @foreach($fabrics as $fabric)
                                                    <option value="{{ $fabric->id }}">{{ $fabric->name }} - {{ $fabric->color }} (Rp {{ number_format($fabric->price_per_meter, 0, ',', '.') }}/m)</option>
                                                @endforeach
                                            </select>
                                            @error('fabric_id') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    @endif

                                    <div>
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Status Bahan</label>
                                        <select wire:model.live="material_status" 
                                                class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                       focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                       dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                            <option value="">Pilih status</option>
                                            <option value="ready">Ready / Tersedia</option>
                                            <option value="po">Pre-Order (PO)</option>
                                        </select>
                                        @error('material_status') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    </div>

                                    @if ($material_status === 'po')
                                        <div>
                                            <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Durasi PO (Hari)</label>
                                            <input type="number" wire:model="poDays" min="3" max="7" 
                                                   class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                          dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                            @error('poDays') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="updateMaterial" class="flex-1 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary-hover">Simpan</button>
                                        <button type="button" wire:click="closeMaterialForm" class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted hover:bg-white dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-800">Batal</button>
                                    </div>
                                </div>
                            @endif

                            @if ($order->material_status === 'po')
                                <div class="mt-4 rounded-xl border border-border border-l-4 border-l-accent bg-accent/5 p-4">
                                    <p class="text-sm text-ink/70 dark:text-stone-300 mb-3">
                                        Bahan sedang di-PO (~{{ $order->po_days }} hari). Klik tombol di bawah jika bahan sudah tiba.
                                    </p>
                                    <button type="button" wire:click="markMaterialReady" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md hover:shadow-emerald-600/20">
                                        Tandai Bahan Ready
                                    </button>
                                </div>
                            @elseif ($order->material_status === 'ready')
                                <div class="mt-4 rounded-xl border border-border border-l-4 border-l-emerald-500 bg-emerald-500/5 p-4">
                                    <p class="text-sm text-ink/70 dark:text-stone-300 mb-3">
                                        Bahan sudah berstatus "Ready". Klik tombol di bawah untuk memasukkan pesanan ke antrian produksi.
                                    </p>
                                    <button type="button" wire:click="forceMoveToQueue" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                        Masukkan ke Antrian Produksi
                                    </button>
                                </div>
                            @endif
                        </div>

                    {{-- 5. Dalam Antrian --}}
                    @elseif ($order->status === 'dalam_antrian')
                        <div class="space-y-4">
                            <p class="text-sm text-muted">Pesanan siap diproduksi.</p>
                            
                            <button type="button" wire:click="openProductionForm" class="w-full rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20">
                                Mulai Proses Jahit
                            </button>

                            @if ($showProductionForm)
                                <div class="rounded-xl border border-border bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                                    <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Estimasi Hari Pengerjaan</label>
                                    <input type="number" wire:model="productionDays" 
                                           class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm text-ink 
                                                  focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                                  dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                    @error('productionDays') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                                    
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" wire:click="startProduction" class="flex-1 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-primary-hover">Mulai Produksi</button>
                                        <button type="button" wire:click="closeProductionForm" class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted hover:bg-white dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-800">Batal</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                    {{-- 6. Dijahit --}}
                    @elseif ($order->status === 'dijahit')
                        <div class="space-y-4">
                            <div class="rounded-xl border border-border border-l-4 border-l-primary bg-primary/5 p-4">
                                <p class="text-sm text-ink/70 dark:text-stone-300">
                                    Sedang dikerjakan. Estimasi selesai: <strong>{{ $order->estimated_finish_date ? $order->estimated_finish_date->format('d M Y') : '-' }}</strong>.
                                </p>
                            </div>
                            <button type="button" wire:click="finishProduction" class="w-full rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-purple-700 hover:shadow-md hover:shadow-purple-600/20">
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
                                <div class="rounded-xl border border-border border-l-4 border-l-emerald-500 bg-emerald-500/5 p-4 mb-3">
                                    <p class="text-sm text-emerald-700 font-semibold dark:text-emerald-400">Pelunasan sudah lunas.</p>
                                </div>
                                <button type="button" wire:click="markReadyForPickup" class="w-full rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-teal-700 hover:shadow-md hover:shadow-teal-600/20">
                                    Tandai Siap Diambil
                                </button>
                            @else
                                <div class="rounded-xl border border-border border-l-4 border-l-rose-500 bg-rose-500/5 p-4">
                                    <p class="text-sm text-rose-700 dark:text-rose-400">
                                        Menunggu customer melakukan pelunasan sisa tagihan sebesar <strong>Rp {{ number_format($order->estimated_price - $totalVerified, 0, ',', '.') }}</strong>.
                                    </p>
                                </div>
                                <button type="button" class="w-full rounded-xl bg-surface border border-border px-4 py-2.5 text-sm font-bold text-muted cursor-not-allowed dark:bg-stone-700 dark:border-stone-600">
                                    Tandai Siap Diambil (Belum Lunas)
                                </button>
                            @endif
                        </div>

                    {{-- 8. Siap Diambil --}}
                    @elseif ($order->status === 'siap_diambil')
                        <div class="space-y-4">
                            <p class="text-sm text-muted">Menunggu customer mengambil pakaian.</p>
                            <button type="button" wire:click="markComplete" class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md hover:shadow-emerald-600/20">
                                Tandai Pesanan Selesai
                            </button>
                        </div>

                    {{-- 9. Selesai / Ditolak / Dibatalkan --}}
                    @else
                        <p class="text-sm text-muted">Pesanan ini sudah berada di status akhir ({{ str_replace('_', ' ', $order->status) }}) dan tidak dapat diubah lagi.</p>
                    @endif
                </div>
            </section>

            {{-- ── Edit Harga Pesanan (Khusus sebelum lunas) ── --}}
            @if (!in_array($order->status, ['siap_diambil', 'selesai', 'ditolak', 'dibatalkan']))
                <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-ink dark:text-stone-100">Edit Total Harga</h2>
                        @if (!$showPriceForm)
                            <button type="button" wire:click="openPriceForm" class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted hover:border-primary hover:text-primary transition dark:border-stone-600">
                                Edit
                            </button>
                        @endif
                    </div>

                    <div class="mt-3">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-muted">Harga Saat Ini</p>
                        <p class="mt-1 text-2xl font-extrabold text-ink dark:text-stone-100">
                            Rp {{ number_format((float) $order->estimated_price, 0, ',', '.') }}
                        </p>
                    </div>

                    @if ($showPriceForm)
                        <div class="mt-4 space-y-3 rounded-xl border border-border bg-surface p-4 dark:border-stone-700 dark:bg-stone-700/30">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-widest text-muted">Harga Baru (Rp)</label>
                                <input type="number" wire:model="editEstimatedPrice" 
                                       class="mt-1.5 w-full rounded-xl border border-border bg-white px-3 py-2 text-sm font-bold text-ink 
                                              focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20
                                              dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100">
                                @error('editEstimatedPrice') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="updatePrice" class="flex-1 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white hover:bg-primary-hover">Simpan</button>
                                <button type="button" wire:click="closePriceForm" class="rounded-xl border border-border px-4 py-2 text-sm font-semibold text-muted hover:bg-white dark:border-stone-600 dark:text-stone-400 dark:hover:bg-stone-800">Batal</button>
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            @if ($order->service->type !== 'vermak')
                <section class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <h2 class="text-base font-bold text-ink dark:text-stone-100">Preview Desain</h2>
                    <div class="mt-4 space-y-2">
                        @forelse ($order->designFiles as $file)
                            <div class="flex items-center justify-between rounded-xl border border-border px-4 py-3 dark:border-stone-700">
                                <div class="text-sm font-semibold text-ink dark:text-stone-300">{{ $file->original_filename }}</div>
                                <button
                                    type="button"
                                    wire:click="previewDesign({{ $file->id }})"
                                    class="rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-muted hover:border-primary hover:text-primary transition dark:border-stone-600 dark:text-stone-300 dark:hover:bg-stone-700"
                                >
                                    Lihat
                                </button>
                            </div>
                        @empty
                            <div class="text-sm text-muted dark:text-stone-400">Belum ada file desain.</div>
                        @endforelse
                    </div>
                </section>
            @endif
        </aside>
    </div>

    @if ($showDesignModal)
        <div class="modal-backdrop-enter fixed inset-0 z-50 flex items-center justify-center bg-ink/60 px-4 backdrop-blur-sm">
            <div class="modal-content-enter w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-stone-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-ink dark:text-stone-100">{{ $designPreviewName }}</h3>
                    <button
                        type="button"
                        wire:click="closeDesignPreview"
                        class="rounded-full p-1.5 text-muted hover:bg-surface hover:text-ink transition dark:hover:bg-stone-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <img src="{{ $designPreviewUrl }}" alt="Preview" class="max-h-[70vh] w-full rounded-xl object-contain border border-border dark:border-stone-700" />
                </div>
            </div>
        </div>
    @endif
</div>

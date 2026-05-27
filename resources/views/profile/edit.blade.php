<x-app-layout>
    <div class="page-enter mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-stone-900">Profil Pelanggan</h2>
            <p class="mt-1 text-sm text-stone-500">
                Kelola informasi akun dan bagikan pengalaman Anda bersama kami.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                    <div class="flex items-center gap-2 text-amber-700">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 3H7a2 2 0 00-2 2v14l3-3h8a2 2 0 002-2V9l-5-6z" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-semibold">Menunggu Ulasan</h3>
                    </div>
                    <p class="mt-3 text-sm text-amber-900">
                        Bagaimana hasil jahitan kami untuk pesanan ini?
                    </p>
                    @if ($pendingReview)
                        <form action="{{ route('testimonials.store') }}" method="POST" x-data="{ rating: 0, hoverRating: 0 }">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $pendingReview->id }}" />

                            <div class="mt-3 rounded-xl border border-amber-100 bg-white/70 p-3 text-xs text-amber-900">
                                <div class="font-semibold">Pesanan {{ $pendingReview->order_number }}</div>
                                <div>{{ $pendingReview->service->name ?? 'Layanan' }}</div>
                                <div>Selesai: {{ optional($pendingReview->estimated_finish_date)->format('d M Y') ?? $pendingReview->updated_at->format('d M Y') }}</div>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center gap-1">
                                    <template x-for="star in [1,2,3,4,5]" :key="star">
                                        <button
                                            type="button"
                                            class="transition"
                                            @mouseenter="hoverRating = star"
                                            @mouseleave="hoverRating = 0"
                                            @click="rating = star"
                                            :aria-label="`Beri rating ${star}`"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                :class="(hoverRating >= star || rating >= star) ? 'text-amber-500' : 'text-amber-200'"
                                            >
                                                <path d="M12 2l2.9 6.1 6.7.6-5 4.4 1.5 6.6L12 16.8 5.9 19.7 7.4 13 2.4 8.7l6.7-.6L12 2z" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" :value="rating" />
                            </div>

                            <textarea
                                name="comment"
                                rows="3"
                                class="mt-3 w-full rounded-xl border border-amber-100 bg-white px-3 py-2 text-xs text-stone-700 placeholder:text-stone-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-200"
                                placeholder="Ceritakan pengalaman Anda..."
                                required
                            ></textarea>
                            <button
                                type="submit"
                                class="mt-4 w-full rounded-xl bg-amber-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-600"
                            >
                                Kirim Testimoni
                            </button>
                        </form>
                    @else
                        <div class="mt-3 rounded-xl border border-amber-100 bg-white/70 p-3 text-xs text-amber-900">
                            Tidak ada pesanan yang perlu diulas saat ini.
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-2 text-stone-700">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-stone-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 1016 0 8 8 0 10-16 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-semibold">Riwayat Testimoni</h3>
                    </div>

                    <div class="mt-4 space-y-5 border-l border-stone-200 pl-4">
                        @forelse ($testimonials as $t)
                            <div class="relative">
                                <span class="absolute -left-[9px] top-1 h-2 w-2 rounded-full bg-blue-600"></span>
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 overflow-hidden rounded-full border bg-stone-50">
                                        @if ($t->customer && $t->customer->profile_photo_path)
                                            <img src="{{ asset('storage/' . $t->customer->profile_photo_path) }}" alt="avatar" class="h-full w-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs text-stone-500">{{ strtoupper(substr($t->customer->name ?? 'U',0,1)) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-stone-900">{{ $t->order->service->name ?? 'Pesanan' }}</span>
                                            <span class="text-stone-400">{{ $t->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="mt-2 flex items-center gap-1 text-amber-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-3.5 w-3.5 {{ $i <= ($t->rating ?? 0) ? 'text-amber-500' : 'text-amber-200' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 2l2.9 6.1 6.7.6-5 4.4 1.5 6.6L12 16.8 5.9 19.7 7.4 13 2.4 8.7l6.7-.6L12 2z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="mt-2 text-xs italic text-stone-600">"{{ $t->comment }}"</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-stone-500">Anda belum mengirim testimoni.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

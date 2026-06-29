<x-app-layout>
    <div class="page-enter mx-auto max-w-6xl px-4 pb-16 sm:px-6">
        <div class="mb-8 mt-2">
            <p class="text-sm font-bold uppercase tracking-widest text-primary/80 mb-1">Akun</p>
            <h2 class="text-3xl font-display font-bold text-ink dark:text-stone-100">Profil Saya</h2>
            <p class="mt-2 text-base text-muted dark:text-stone-400">Kelola informasi akun Anda.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="space-y-6">
                {{-- Card Testimoni --}}
                <div class="rounded-2xl border border-accent/20 bg-accent/5 p-6 shadow-sm dark:border-accent/10">
                    <div class="flex items-center gap-2 text-accent">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-accent/10">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 16h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 3H7a2 2 0 00-2 2v14l3-3h8a2 2 0 002-2V9l-5-6z" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold">Menunggu Ulasan</h3>
                    </div>
                    <p class="mt-3 text-sm text-ink/70 dark:text-stone-300">
                        Bagaimana hasil jahitan kami?
                    </p>
                    @if ($pendingReview)
                        <form action="{{ route('testimonials.store') }}" method="POST" x-data="{ rating: 0, hoverRating: 0 }">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $pendingReview->id }}" />

                            <div class="mt-3 rounded-xl border border-border bg-white p-3 text-xs text-ink dark:bg-stone-800 dark:border-stone-600">
                                <div class="font-bold">Pesanan {{ $pendingReview->order_number }}</div>
                                <div class="text-muted">{{ $pendingReview->service->name ?? 'Layanan' }}</div>
                                <div class="text-muted">Selesai: {{ optional($pendingReview->estimated_finish_date)->format('d M Y') ?? $pendingReview->updated_at->format('d M Y') }}</div>
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
                                                class="h-5 w-5"
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
                                class="mt-3 w-full rounded-xl border border-border bg-white px-3 py-2 text-xs text-ink placeholder:text-muted/50 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-stone-900 dark:border-stone-600 dark:text-stone-100 dark:placeholder-stone-500"
                                placeholder="Ceritakan pengalaman Anda..."
                                required
                            ></textarea>
                            <button
                                type="submit"
                                class="mt-4 w-full rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white
                                       transition hover:bg-primary-hover"
                            >
                                Kirim Testimoni
                            </button>
                        </form>
                    @else
                        <div class="mt-3 rounded-xl border border-border bg-white p-3 text-xs text-muted dark:bg-stone-800 dark:border-stone-600">
                            Tidak ada pesanan yang perlu diulas saat ini.
                        </div>
                    @endif
                </div>

                {{-- Card Riwayat Testimoni --}}
                <div class="rounded-2xl border border-border bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <div class="flex items-center gap-2 text-ink dark:text-stone-300">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-surface dark:bg-stone-700">
                            <svg class="h-4 w-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 1016 0 8 8 0 10-16 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2 2" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold">Riwayat Testimoni</h3>
                    </div>

                    <div class="mt-4 space-y-5 border-l border-border pl-4 dark:border-stone-700">
                        @forelse ($testimonials as $t)
                            <div class="relative">
                                <span class="absolute -left-[9px] top-1 h-2 w-2 rounded-full bg-primary"></span>
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 overflow-hidden rounded-full border border-border bg-surface dark:bg-stone-800 dark:border-stone-700">
                                        @if ($t->customer && $t->customer->profile_photo_path)
                                            <img src="{{ asset('storage/' . $t->customer->profile_photo_path) }}" alt="avatar" class="h-full w-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-xs font-bold text-primary/60">{{ strtoupper(substr($t->customer->name ?? 'U', 0, 1)) }}</div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-ink dark:text-stone-100">{{ $t->order->service->name ?? 'Pesanan' }}</span>
                                            <span class="text-muted dark:text-stone-500">{{ $t->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="mt-1.5 flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-3.5 w-3.5 {{ $i <= ($t->rating ?? 0) ? 'text-amber-500' : 'text-amber-200' }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 2l2.9 6.1 6.7.6-5 4.4 1.5 6.6L12 16.8 5.9 19.7 7.4 13 2.4 8.7l6.7-.6L12 2z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="mt-1.5 text-xs italic text-muted dark:text-stone-400">"{{ $t->comment }}"</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-muted dark:text-stone-500">Anda belum mengirim testimoni.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

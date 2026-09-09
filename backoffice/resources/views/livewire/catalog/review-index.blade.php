<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/40">
                    FEEDBACK & REVIEWS
                </span>
                <span class="text-xs text-slate-500 font-mono">• Malega Quality Assurance</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-wide mt-1">
                Moderasi Rating & Ulasan Produk
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Kelola ulasan dari pembeli terverifikasi, tanggapi ulasan pelanggan, dan pastikan kualitas testimoni Storefront.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('catalog.products') }}"
                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition-colors flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Katalog Produk</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards (Compact 1-Row) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="p-3 sm:p-3.5 rounded-xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-md flex items-center justify-between gap-2 hover:border-[#CBAC70]/30 transition-colors">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400 truncate">Total Ulasan</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-lg font-black font-mono text-white">{{ number_format($totalReviews) }}</span>
                    <span class="text-[10px] text-slate-400 font-sans truncate">Ulasan</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-[#CBAC70] shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
        </div>

        <div class="p-3 sm:p-3.5 rounded-xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-md flex items-center justify-between gap-2 hover:border-[#CBAC70]/30 transition-colors">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400 truncate">Rating Toko</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-lg font-black font-mono text-[#CBAC70]">{{ $globalAvgRating }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">/ 5.0 ★</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-[#CBAC70]/10 border border-[#CBAC70]/20 flex items-center justify-center text-[#CBAC70] shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                </svg>
            </div>
        </div>

        <div class="p-3 sm:p-3.5 rounded-xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-md flex items-center justify-between gap-2 hover:border-emerald-500/30 transition-colors">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400 truncate">Terverifikasi</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-lg font-black font-mono text-emerald-400">{{ number_format($verifiedBuyerCount) }}</span>
                    <span class="text-[10px] text-emerald-400/80 font-sans truncate">Pembeli</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>

        <div class="p-3 sm:p-3.5 rounded-xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-md flex items-center justify-between gap-2 hover:border-rose-500/30 transition-colors">
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400 truncate">Disembunyikan</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-lg font-black font-mono text-rose-400">{{ number_format($hiddenCount) }}</span>
                    <span class="text-[10px] text-rose-400/80 font-sans truncate">Ulasan</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-4 rounded-2xl bg-[#0E1736] border border-white/10 space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-5 relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari ulasan, produk, atau nama pembeli..."
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl pl-10 pr-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                />
            </div>

            <!-- Rating Filter -->
            <div class="sm:col-span-3">
                <select
                    wire:model.live="ratingFilter"
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-[#CBAC70]"
                >
                    <option value="">Semua Rating Bintang</option>
                    <option value="5">★★★★★ (5 Bintang)</option>
                    <option value="4">★★★★☆ (4 Bintang)</option>
                    <option value="3">★★★☆☆ (3 Bintang)</option>
                    <option value="2">★★☆☆☆ (2 Bintang)</option>
                    <option value="1">★☆☆☆☆ (1 Bintang)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select
                    wire:model.live="statusFilter"
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-[#CBAC70]"
                >
                    <option value="">Semua Status</option>
                    <option value="approved">Disetujui (Approved)</option>
                    <option value="hidden">Disembunyikan (Hidden)</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="sm:col-span-2">
                <select
                    wire:model.live="sortBy"
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-[#CBAC70]"
                >
                    <option value="latest">Terbaru</option>
                    <option value="rating_desc">Rating Tertinggi</option>
                    <option value="rating_asc">Rating Terendah</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Review Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($reviews as $review)
            <div
                wire:key="review-card-{{ $review->id }}"
                class="rounded-3xl border {{ $review->status === 'approved' ? 'border-white/10 bg-[#0E1736]' : 'border-rose-500/30 bg-[#160B12]' }} p-5 space-y-4 shadow-xl flex flex-col justify-between"
            >
                <div class="space-y-3">
                    <!-- Header: Product Info & Review Status -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-white/5">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($review->product?->featured_image)
                                <img
                                    src="{{ $review->product->featured_image }}"
                                    alt="{{ $review->product->name }}"
                                    class="w-12 h-12 rounded-xl object-cover border border-white/10 shrink-0"
                                />
                            @else
                                <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500 text-xs shrink-0">
                                    IMG
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white truncate max-w-xs sm:max-w-sm">
                                    {{ $review->product?->name ?? 'Produk Dihapus' }}
                                </h4>
                                <span class="text-[10px] font-mono text-slate-400 block">
                                    Pesanan: #{{ $review->order?->order_number ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold shrink-0 {{ $review->status === 'approved' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                            {{ $review->status === 'approved' ? 'Disetujui' : 'Disembunyikan' }}
                        </span>
                    </div>

                    <!-- Customer Info & Stars Row -->
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white">
                                {{ $review->customer?->name ?? 'Pelanggan' }}
                            </span>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-mono font-bold bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30">
                                ★ {{ $review->customer?->membership_tier ?? 'Silver' }} Member
                            </span>
                            <span class="text-[10px] font-mono text-emerald-400 flex items-center gap-0.5">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Verified Buyer</span>
                            </span>
                        </div>

                        <div class="flex items-center gap-1 text-[#CBAC70]">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current text-[#CBAC70]' : 'text-slate-700' }}" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                </svg>
                            @endfor
                        </div>
                    </div>

                    <!-- Review Headline & Text -->
                    <div class="space-y-1">
                        @if($review->headline)
                            <h5 class="text-xs font-bold text-amber-200">
                                "{{ $review->headline }}"
                            </h5>
                        @endif
                        <p class="text-xs text-slate-300 leading-relaxed">
                            {{ $review->review }}
                        </p>
                    </div>

                    <!-- Tags: Fit & Date -->
                    <div class="flex items-center gap-2 pt-1 text-[11px] text-slate-400 font-mono">
                        @if($review->fitLabel())
                            <span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-slate-300">
                                Ukuran: {{ $review->fitLabel() }}
                            </span>
                        @endif
                        <span>•</span>
                        <span>{{ $review->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    <!-- Admin Reply Box (if exists) -->
                    @if($review->admin_reply)
                        <div class="p-3 rounded-2xl bg-[#070C1A] border-l-2 border-[#CBAC70] space-y-1 text-xs">
                            <div class="flex items-center justify-between text-[10px] font-mono text-[#CBAC70]">
                                <strong>Tanggapan Malega Apparel:</strong>
                                <span>{{ $review->admin_replied_at?->format('d M Y') }}</span>
                            </div>
                            <p class="text-[11px] text-slate-300 leading-relaxed">
                                {{ $review->admin_reply }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-white/5 flex items-center justify-between gap-2">
                    <button
                        type="button"
                        wire:click="toggleStatus({{ $review->id }})"
                        class="text-xs font-semibold px-3 py-1.5 rounded-xl border transition-colors cursor-pointer {{ $review->status === 'approved' ? 'border-amber-500/30 text-amber-300 hover:bg-amber-500/10' : 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' }}"
                    >
                        {{ $review->status === 'approved' ? 'Sembunyikan' : 'Setujui & Tampilkan' }}
                    </button>

                    <div class="flex items-center gap-1.5">
                        <button
                            type="button"
                            wire:click="openReplyModal({{ $review->id }})"
                            class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-white text-xs font-semibold border border-white/10 transition-colors cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5 text-[#CBAC70]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            <span>{{ $review->admin_reply ? 'Edit Balasan' : 'Balas Ulasan' }}</span>
                        </button>

                        <button
                            type="button"
                            wire:click="confirmDelete({{ $review->id }})"
                            class="p-1.5 rounded-xl border border-rose-500/30 text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                            title="Hapus Ulasan"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-16 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-2">
                <svg class="w-12 h-12 text-slate-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <p class="text-sm font-bold text-slate-300">Belum ada ulasan produk ditemukan</p>
                <p class="text-xs text-slate-500">Ulasan yang dikirimkan oleh pembeli terverifikasi akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $reviews->links() }}
    </div>

    <!-- Admin Reply Modal -->
    <x-modal
        id="reply-modal"
        name="reply-modal"
        title="Tanggapi Ulasan Pembeli"
        subtitle="Tuliskan balasan resmi atas ulasan pelanggan. Balasan ini akan tampil di Storefront di bawah ulasan pembeli."
        maxWidth="lg"
    >
        <form wire:submit.prevent="saveReply" class="space-y-4 text-xs">
            <div class="space-y-1.5">
                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-[#CBAC70]">
                    Isi Tanggapan Resmi Toko <span class="text-rose-400">*</span>
                </label>
                <textarea
                    wire:model="adminReplyText"
                    rows="4"
                    placeholder="Halo Kak, terima kasih banyak atas ulasan dan kepercayaannya pada Malega Apparel..."
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                ></textarea>
                @error('adminReplyText') <span class="text-[10px] text-rose-400 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-3 border-t border-white/10 flex items-center justify-end gap-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-reply-modal')"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl text-xs font-bold text-[#0B132B] bg-[#CBAC70] hover:bg-[#E3CD99] transition shadow cursor-pointer"
                >
                    Kirim Balasan
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal
        id="delete-review-modal"
        title="Konfirmasi Hapus Ulasan"
        message="Apakah Anda yakin ingin menghapus ulasan ini secara permanen? Rating produk akan otomatis dihitung ulang."
        confirmText="Hapus Ulasan"
        cancelText="Batal"
        type="danger"
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="deleteReview"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer bg-rose-600 hover:bg-rose-500 text-white"
                x-on:click="$dispatch('close-confirmation-delete-review-modal')"
            >
                Hapus Ulasan
            </button>
        </x-slot:action>
    </x-confirmation-modal>
</div>

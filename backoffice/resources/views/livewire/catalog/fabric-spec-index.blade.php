<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-[#0B132B] via-[#0E1A3D] to-[#0B132B] p-6 rounded-2xl border border-gold/20 shadow-xl">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gold/15 border border-gold/30 flex items-center justify-center text-gold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </span>
                <h1 class="text-xl font-bold font-display text-ivory tracking-wide">Spesifikasi Bahan & Konstruksi</h1>
            </div>
            <p class="text-xs text-slate-400">
                Kelola master spesifikasi kain, gramasi (GSM), potongan, dan petunjuk perawatan untuk diterapkan langsung ke busana storefront.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('catalog.products') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-semibold transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Katalog Produk</span>
            </a>

            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow-lg shadow-gold/10 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span> Spesifikasi Bahan</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#070C1A] p-4 rounded-xl border border-slate-800/80 shadow-md">
        <div class="relative w-full sm:w-80">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama bahan, gramasi, material..."
                class="w-full bg-[#0B132B] border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-gold"
            >
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <select
                wire:model.live="statusFilter"
                class="bg-[#0B132B] border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold cursor-pointer w-full sm:w-auto"
            >
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($specs as $spec)
            <div wire:key="spec-card-{{ $spec->id }}" class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-5 space-y-4 shadow-lg hover:border-gold/30 transition-all flex flex-col justify-between">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-white/5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider {{ $spec->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-700/30 text-slate-400' }}">
                                    {{ $spec->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="text-[11px] font-mono text-gold font-bold">
                                    {{ $spec->brand }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-ivory font-display tracking-wide">
                                {{ $spec->name }}
                            </h3>
                        </div>

                        <!-- Badge Jumlah Produk -->
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-gold/10 border border-gold/20 text-gold text-[11px] font-bold shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>{{ $spec->products_count }} Produk</span>
                        </span>
                    </div>

                    <!-- Specs Breakdown List -->
                    <div class="grid grid-cols-2 gap-2 pt-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] font-mono text-slate-400 uppercase block">Gramasi (GSM)</span>
                            <p class="font-bold text-slate-200 text-xs truncate">{{ $spec->gramasi }}</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] font-mono text-slate-400 uppercase block">Material Kain</span>
                            <p class="font-bold text-slate-200 text-xs truncate">{{ $spec->material }}</p>
                        </div>
                        @if($spec->fit_cutting)
                            <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                                <span class="text-[10px] font-mono text-slate-400 uppercase block">Fitting / Cutting</span>
                                <p class="font-medium text-slate-300 text-xs truncate">{{ $spec->fit_cutting }}</p>
                            </div>
                        @endif
                        @if($spec->collar_hood)
                            <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                                <span class="text-[10px] font-mono text-slate-400 uppercase block">Kerah / Hood / Detail</span>
                                <p class="font-medium text-slate-300 text-xs truncate">{{ $spec->collar_hood }}</p>
                            </div>
                        @endif
                    </div>

                    @if($spec->care_instructions)
                        <div class="mt-2.5 p-2.5 rounded-xl bg-[#070C1A] border border-slate-800 text-[11px] text-slate-400 flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 text-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="leading-relaxed">{{ $spec->care_instructions }}</span>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-slate-800/80 gap-2">
                    <button
                        type="button"
                        wire:click="openApplyModal({{ $spec->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gold/15 hover:bg-gold/25 border border-gold/30 text-gold text-xs font-bold transition-all cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terapkan ke Produk</span>
                    </button>

                    <div class="flex items-center gap-1">
                        <button
                            type="button"
                            wire:click="openEditModal({{ $spec->id }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-gold hover:bg-gold/10 transition-colors cursor-pointer"
                            title="Edit Spesifikasi"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            wire:click="openDeleteModal({{ $spec->id }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                            title="Hapus Spesifikasi"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl bg-[#070C1A] border border-slate-800 space-y-3">
                <svg class="w-10 h-10 mx-auto text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <p class="text-sm font-semibold text-slate-300">Belum ada spesifikasi bahan</p>
                <p class="text-xs text-slate-500">Klik tombol "+ Tambah Spesifikasi Bahan" untuk membuat template spesifikasi baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $specs->links() }}
    </div>

    <!-- MODAL 1: Create / Edit Specification -->
    <x-modal
        id="spec-modal"
        name="spec-modal"
        :title="$isEditing ? 'Edit Spesifikasi Bahan & Konstruksi' : 'Tambah Spesifikasi Bahan Baru'"
        :subtitle="$isEditing ? 'Perbarui detail spesifikasi material kain' : 'Buat master spesifikasi baru untuk dapat diterapkan ke produk katalog'"
        maxWidth="2xl"
    >
        <form wire:submit="saveSpec" class="space-y-4">
            <!-- Nama Template & Brand -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Nama Template Spesifikasi <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="misal: Heavyweight Cotton 300 GSM (T-Shirt)"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        required
                    >
                    @error('name') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Brand <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="brand"
                        placeholder="Malega Apparel"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        required
                    >
                </div>
            </div>

            <!-- Gramasi & Material -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Gramasi Kain (GSM / oz) <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="gramasi"
                        placeholder="misal: 300 GSM Heavyweight / 14oz"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        required
                    >
                    @error('gramasi') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Komposisi Material <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="material"
                        placeholder="misal: 100% Pure Combed Cotton"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        required
                    >
                    @error('material') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Fit/Cutting & Kerah/Hood -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Potongan / Fit / Cutting
                    </label>
                    <input
                        type="text"
                        wire:model="fit_cutting"
                        placeholder="misal: Boxy Fit / Drop Shoulder"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                    >
                </div>

                <div class="space-y-1">
                    <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Kerah / Hood / Hardware
                    </label>
                    <input
                        type="text"
                        wire:model="collar_hood"
                        placeholder="misal: 3.5cm Reinforced Rib Collar"
                        class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                    >
                </div>
            </div>

            <!-- Petunjuk Perawatan -->
            <div class="space-y-1">
                <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                    Instruksi & Petunjuk Perawatan
                </label>
                <input
                    type="text"
                    wire:model="care_instructions"
                    placeholder="misal: Cuci dengan air dingin, setrika temperatur sedang"
                    class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-gold"
                >
            </div>

            <!-- Status Aktif -->
            <div class="pt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="is_active"
                        class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-gold focus:ring-gold"
                    >
                    <span class="text-xs font-semibold text-slate-300">Status Aktif (Tersedia untuk dipilih di Produk)</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-800">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-spec-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700 bg-slate-800/80 text-slate-300 text-xs font-semibold hover:bg-slate-700 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow-md shadow-gold/10 transition-all cursor-pointer"
                >
                    {{ $isEditing ? 'Simpan Perubahan' : 'Buat Spesifikasi' }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL 2: Terapkan Spesifikasi ke Produk (Bulk Apply) -->
    <x-modal
        id="apply-modal"
        name="apply-modal"
        :title="'Terapkan Spesifikasi: ' . $applyingSpecName"
        subtitle="Pilih produk busana yang akan menggunakan spesifikasi bahan dan konstruksi ini"
        maxWidth="2xl"
    >
        <div class="space-y-4">
            <!-- Search in Apply Modal -->
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.200ms="productSearch"
                    placeholder="Filter nama produk..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-gold"
                >
            </div>

            <!-- Product Selection List -->
            <div class="max-h-72 overflow-y-auto space-y-1.5 pr-1 divide-y divide-slate-800/60 border border-slate-800 rounded-xl p-2 bg-[#070C1A]">
                @forelse($availableProducts as $prod)
                    <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-800/50 cursor-pointer transition-colors">
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                wire:model="selectedProductIds"
                                value="{{ $prod->id }}"
                                class="w-4 h-4 rounded bg-[#0B132B] border-slate-700 text-gold focus:ring-gold"
                            >
                            <div>
                                <p class="text-xs font-semibold text-ivory">{{ $prod->name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono">{{ $prod->category?->name ?? 'Tanpa Kategori' }}</p>
                            </div>
                        </div>

                        @if($prod->fabric_spec_id === $applyingSpecId)
                            <span class="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20 font-bold">
                                Terpasang
                            </span>
                        @endif
                    </label>
                @empty
                    <div class="p-4 text-center text-xs text-slate-500">
                        Tidak ada produk ditemukan.
                    </div>
                @endforelse
            </div>

            <!-- Footer Apply -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                <span class="text-xs text-slate-400 font-mono">
                    <strong class="text-gold">{{ count($selectedProductIds) }}</strong> produk dipilih
                </span>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="$dispatch('close-modal-apply-modal')"
                        class="px-3.5 py-1.5 rounded-xl border border-slate-700 bg-slate-800/80 text-slate-300 text-xs font-semibold hover:bg-slate-700 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="applyToProducts"
                        class="px-4 py-1.5 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow-md shadow-gold/10 transition-all cursor-pointer"
                    >
                        Terapkan Spesifikasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- MODAL 3: Delete Confirmation Modal -->
    <x-modal
        id="delete-spec-modal"
        name="delete-spec-modal"
        title="Hapus Spesifikasi Bahan"
        subtitle="Konfirmasi penghapusan master spesifikasi bahan"
        maxWidth="md"
    >
        <div class="space-y-4">
            <p class="text-xs text-slate-300 leading-relaxed">
                Apakah Anda yakin ingin menghapus spesifikasi <strong class="text-rose-400 font-mono">"{{ $deletingSpecName }}"</strong>?
                Spesifikasi ini akan dilepaskan dari semua produk terkait.
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-delete-spec-modal')"
                    class="px-3.5 py-1.5 rounded-xl border border-slate-700 bg-slate-800/80 text-slate-300 text-xs font-semibold hover:bg-slate-700 transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="deleteSpec"
                    class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md transition-all cursor-pointer"
                >
                    Hapus
                </button>
            </div>
        </div>
    </x-modal>
</div>

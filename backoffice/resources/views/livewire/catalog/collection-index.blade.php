<div class="space-y-6">
    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-[#0B132B] via-[#0E1A3D] to-[#0B132B] p-6 rounded-2xl border border-gold/20 shadow-xl">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gold/15 border border-gold/30 flex items-center justify-center text-gold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </span>
                <h1 class="text-xl font-bold font-display text-ivory tracking-wide">Master Koleksi & Lookbook</h1>
            </div>
            <p class="text-xs text-slate-400">
                Kelola seri kapsul musiman, lookbook tematik, storytelling filosofi busana, dan pengelompokan produk storefront.
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
                <span>Koleksi Baru</span>
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <div class="bg-[#070C1A] border border-slate-800/80 rounded-2xl p-4 flex items-center justify-between shadow-md">
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Koleksi</p>
                <h4 class="text-xl font-bold font-display text-ivory mt-0.5">{{ $totalCollections }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>

        <div class="bg-[#070C1A] border border-slate-800/80 rounded-2xl p-4 flex items-center justify-between shadow-md">
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Koleksi Aktif</p>
                <h4 class="text-xl font-bold font-display text-emerald-400 mt-0.5">{{ $activeCollections }}</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-[#070C1A] border border-slate-800/80 rounded-2xl p-4 flex items-center justify-between shadow-md">
            <div>
                <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Produk Terikat</p>
                <h4 class="text-xl font-bold font-display text-sky-400 mt-0.5">{{ $totalAttachedProducts }} Artikel</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
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
                placeholder="Cari nama koleksi, musim, badge, bahan..."
                class="w-full bg-[#0B132B] border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-gold"
            >
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <select
                wire:model.live="seasonFilter"
                class="bg-[#0B132B] border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold cursor-pointer w-full sm:w-auto"
            >
                <option value="all">Semua Musim / Season</option>
                @foreach($seasonsList as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>

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
        @forelse($collections as $col)
            <div wire:key="collection-card-{{ $col->id }}" class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] overflow-hidden shadow-lg hover:border-gold/30 transition-all flex flex-col justify-between">
                
                <!-- Card Cover Hero Preview -->
                <div class="relative h-40 bg-[#050914] overflow-hidden group">
                    <img
                        src="{{ $col->cover_image ? (str_starts_with($col->cover_image, 'http') ? $col->cover_image : asset('storage/'.$col->cover_image)) : 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80' }}"
                        alt="{{ $col->name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B132B] via-[#0B132B]/50 to-transparent"></div>

                    <!-- Top Badges -->
                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider {{ $col->is_active ? 'bg-emerald-500/90 text-white shadow' : 'bg-slate-800/90 text-slate-300' }}">
                                {{ $col->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            @if($col->badge)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-gold text-navy shadow">
                                    {{ $col->badge }}
                                </span>
                            @endif
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono text-ivory bg-black/60 backdrop-blur-md border border-white/10">
                            {{ $col->season }} • {{ $col->release_year }}
                        </span>
                    </div>

                    <!-- Bottom Title on Hero -->
                    <div class="absolute bottom-3 left-3 right-3">
                        <h3 class="text-base font-bold font-display text-ivory leading-tight drop-shadow">
                            {{ $col->name }}
                        </h3>
                        @if($col->subtitle)
                            <p class="text-xs text-gold/90 font-medium line-clamp-1 mt-0.5">
                                {{ $col->subtitle }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Card Details Body -->
                <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        
                        <!-- Material & DNA Palette -->
                        <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                            <div class="flex items-center gap-1.5 text-slate-300">
                                <span class="text-gold font-bold">Bahan:</span>
                                <span class="font-medium truncate max-w-[180px]">{{ $col->featured_material ?: 'Premium Streetwear' }}</span>
                                @if($col->gsm_weight)
                                    <span class="px-1.5 py-0.2 rounded bg-gold/15 text-gold text-[10px] font-mono font-bold">
                                        {{ $col->gsm_weight }}GSM
                                    </span>
                                @endif
                            </div>

                            <!-- Palette Swatches -->
                            @if(is_array($col->palette) && count($col->palette) > 0)
                                <div class="flex items-center gap-1 shrink-0">
                                    @foreach($col->palette as $hex)
                                        <span class="w-3.5 h-3.5 rounded-full border border-white/20 shadow-sm" style="background-color: {{ $hex }};" title="{{ $hex }}"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Storytelling Excerpt -->
                        @if($col->description || $col->storytelling)
                            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed italic bg-[#070C1A] p-2.5 rounded-xl border border-white/5">
                                "{{ $col->storytelling ?: $col->description }}"
                            </p>
                        @endif

                        <!-- Attached Products List Chips -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-400 font-medium">Produk Terikat dalam Lookbook:</span>
                                <span class="text-gold font-bold font-mono">{{ $col->products_count }} Artikel</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5 min-h-[30px] p-2 rounded-xl bg-[#070C1A] border border-white/5">
                                @forelse($col->products as $p)
                                    <span wire:key="col-prod-{{ $col->id }}-{{ $p->id }}" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-slate-800/90 border border-slate-700/80 text-[11px] text-slate-200">
                                        <span class="truncate max-w-[150px]">{{ $p->name }}</span>
                                        <button
                                            type="button"
                                            wire:click="detachProduct({{ $col->id }}, {{ $p->id }})"
                                            wire:confirm="Lepaskan '{{ $p->name }}' dari koleksi ini?"
                                            class="text-slate-400 hover:text-rose-400 transition-colors cursor-pointer"
                                            title="Lepaskan dari koleksi"
                                        >
                                            ✕
                                        </button>
                                    </span>
                                @empty
                                    <span class="text-[11px] text-slate-500 italic py-0.5">
                                        Belum ada produk yang dihubungkan ke koleksi ini.
                                    </span>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-white/5">
                        <button
                            type="button"
                            wire:click="openProductsModal({{ $col->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gold/15 hover:bg-gold/25 border border-gold/30 text-gold text-xs font-bold transition-all cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Atur Produk ({{ $col->products_count }})</span>
                        </button>

                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                wire:click="toggleStatus({{ $col->id }})"
                                class="p-1.5 rounded-xl border {{ $col->is_active ? 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' : 'border-slate-700 text-slate-400 hover:bg-slate-800' }} transition-colors cursor-pointer"
                                title="{{ $col->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="openEditModal({{ $col->id }})"
                                class="p-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-slate-700 text-slate-300 transition-colors cursor-pointer"
                                title="Edit Koleksi"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                wire:click="confirmDelete({{ $col->id }})"
                                class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 text-rose-400 transition-colors cursor-pointer"
                                title="Hapus Koleksi"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-800 bg-[#070C1A]/50 p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-ivory">Belum Ada Koleksi Lookbook</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Buat seri lookbook pertama Anda untuk mengelompokkan busana streetwear berdasarkan tema atau kampanye.
                </p>
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold text-navy font-bold text-xs shadow cursor-pointer"
                >
                    + Tambah Koleksi Baru
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($collections->hasPages())
        <div class="pt-2">
            {{ $collections->links() }}
        </div>
    @endif

    <!-- MODAL 1: Form Create / Edit Collection -->
    <x-modal id="collection-form-modal" maxWidth="2xl">
        <form wire:submit="save" class="p-6 space-y-5 bg-[#0B132B] text-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-gold/15 border border-gold/30 flex items-center justify-center text-gold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold font-display text-ivory">
                        {{ $isEditing ? 'Edit Koleksi Lookbook' : 'Buat Koleksi & Lookbook Baru' }}
                    </h3>
                </div>
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="text-slate-400 hover:text-white transition-colors cursor-pointer"
                >
                    ✕
                </button>
            </div>

            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                <!-- Name & Subtitle -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1 sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-300">
                            Nama Seri Koleksi <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="name"
                            placeholder="Contoh: Heavyweight Boxy Tees (300GSM)"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('name') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Subtitle / Highlight
                        </label>
                        <input
                            type="text"
                            wire:model="subtitle"
                            placeholder="Contoh: Siluet Boxy Drop-Shoulder & Kerah Rib"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('subtitle') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Slug URL <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="slug"
                            placeholder="heavyweight-boxy-tees-300gsm"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('slug') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Season, Release Year & Badge -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Musim / Season <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="season"
                            placeholder="Spring / Summer"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('season') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Tahun Rilis <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="release_year"
                            placeholder="2026"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('release_year') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Badge Ribbon
                        </label>
                        <input
                            type="text"
                            wire:model="badge"
                            placeholder="SS26 DROP / LIMITED"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                        @error('badge') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Material & GSM -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Karakter Bahan Unggulan
                        </label>
                        <input
                            type="text"
                            wire:model="featured_material"
                            placeholder="100% Combed Cotton Heavyweight"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Bobot Gramasi (GSM)
                        </label>
                        <input
                            type="number"
                            wire:model="gsm_weight"
                            placeholder="300"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>
                </div>

                <!-- Storytelling & Concept -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-300">
                        Storytelling & Filosofi Rilisan
                    </label>
                    <textarea
                        wire:model="storytelling"
                        rows="3"
                        placeholder="Tuliskan cerita di balik koleksi, proses kurasi bahan, atau inspirasi siluet potongan..."
                        class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold leading-relaxed"
                    ></textarea>
                </div>

                <!-- Description -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-300">
                        Deskripsi Singkat (Ringkasan)
                    </label>
                    <textarea
                        wire:model="description"
                        rows="2"
                        placeholder="Ringkasan koleksi untuk kartu pratinjau..."
                        class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold leading-relaxed"
                    ></textarea>
                </div>

                <!-- DNA Color Palette & Tags -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            DNA Palet Warna (Kode HEX Pisahkan Koma)
                        </label>
                        <input
                            type="text"
                            wire:model="paletteInput"
                            placeholder="#0B132B, #CBAC70, #1E293B"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Tags Koleksi (Pisahkan Koma)
                        </label>
                        <input
                            type="text"
                            wire:model="tagsInput"
                            placeholder="300GSM, Drop Shoulder, Rib 3.5cm"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>
                </div>

                <!-- Images URL -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Cover Image URL (Portrait / Card)
                        </label>
                        <input
                            type="text"
                            wire:model="cover_image"
                            placeholder="https://images.unsplash.com/..."
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-300">
                            Banner Image URL (Landscape Hero)
                        </label>
                        <input
                            type="text"
                            wire:model="banner_image"
                            placeholder="https://images.unsplash.com/..."
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold"
                        >
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="sr-only peer"
                        >
                        <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                        <span class="ml-2.5 text-xs font-medium text-slate-300">
                            Tampilkan Koleksi ini di Storefront (Status Aktif)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-white/10">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow-lg shadow-gold/10 transition-all cursor-pointer"
                >
                    {{ $isEditing ? 'Simpan Perubahan' : 'Buat Koleksi' }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL 2: Manage / Attach Products to Collection -->
    <x-modal id="manage-products-modal" maxWidth="2xl">
        <div class="p-6 space-y-5 bg-[#0B132B] text-slate-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-gold/15 border border-gold/30 flex items-center justify-center text-gold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold font-display text-ivory">
                            Hubungkan Produk ke Koleksi
                        </h3>
                        <p class="text-xs text-gold font-medium mt-0.5">
                            Koleksi: {{ $managingCollectionName }}
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="text-slate-400 hover:text-white transition-colors cursor-pointer"
                >
                    ✕
                </button>
            </div>

            <!-- Search & Filter Products within Modal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.250ms="productSearch"
                        placeholder="Cari nama produk..."
                        class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-gold"
                    >
                </div>

                <select
                    wire:model.live="productCategoryFilter"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-gold cursor-pointer"
                >
                    <option value="all">Semua Kategori Produk</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selected Summary Counter -->
            <div class="flex items-center justify-between text-xs px-1 text-slate-400">
                <span>Pilih produk yang akan ditampilkan di lookbook ini:</span>
                <span class="font-bold text-gold font-mono">{{ count($selectedProductIds) }} Dipilih</span>
            </div>

            <!-- Products List Checklist Grid -->
            <div class="max-h-80 overflow-y-auto space-y-2 pr-1">
                @forelse($availableProducts as $prod)
                    @php
                        $isSelected = in_array($prod->id, $selectedProductIds);
                    @endphp
                    <div
                        wire:key="modal-prod-item-{{ $prod->id }}"
                        wire:click="toggleProductSelection({{ $prod->id }})"
                        class="flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer {{ $isSelected ? 'bg-gold/10 border-gold/40 text-ivory' : 'bg-[#070C1A] border-white/5 hover:border-slate-700 text-slate-300' }}"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                checked="{{ $isSelected }}"
                                class="rounded border-slate-700 text-gold focus:ring-gold bg-[#0B132B] pointer-events-none"
                            >
                            <div>
                                <h5 class="text-xs font-bold leading-snug">{{ $prod->name }}</h5>
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                    <span>{{ $prod->category?->name ?? 'Streetwear' }}</span>
                                    @if($prod->material)
                                        <span>• {{ $prod->material }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <span class="text-xs font-mono font-bold text-gold">
                            {{ $prod->formatted_price_range }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-500 rounded-xl bg-[#070C1A]">
                        Tidak ada produk yang cocok dengan pencarian.
                    </div>
                @endforelse
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-white/10">
                <button
                    type="button"
                    wire:click="$set('selectedProductIds', [])"
                    class="text-xs text-slate-400 hover:text-rose-400 underline cursor-pointer"
                >
                    Hapus Semua Pilihan
                </button>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="saveCollectionProducts"
                        class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow transition-all cursor-pointer"
                    >
                        Simpan Hubungan Produk ({{ count($selectedProductIds) }})
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- MODAL 3: Delete Confirmation -->
    <x-modal id="delete-collection-modal" maxWidth="md">
        <div class="p-6 space-y-4 bg-[#0B132B] text-slate-200">
            <div class="flex items-center gap-3 text-rose-400">
                <span class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-base font-bold font-display text-ivory">Hapus Koleksi?</h3>
                    <p class="text-xs text-slate-400">Tindakan ini akan menghapus lookbook ini dari storefront.</p>
                </div>
            </div>

            <p class="text-xs text-slate-300 bg-[#070C1A] p-3 rounded-xl border border-white/5">
                Apakah Anda yakin ingin menghapus koleksi <strong class="text-gold font-bold">{{ $deletingName }}</strong>?
            </p>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="button"
                    wire:click="deleteCollection"
                    class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs shadow transition-all cursor-pointer"
                >
                    Ya, Hapus Koleksi
                </button>
            </div>
        </div>
    </x-modal>
</div>

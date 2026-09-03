<div class="space-y-6">
    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Katalog Produk & SKU"
        subtitle="Kelola lini koleksi busana, varian ukuran/warna, dan matriks harga Malega Apparel"
        :count="$totalCount"
    >
        <!-- Filter Controls Bar (Full-Width Single Row) -->
        <x-slot:controls>
            <!-- Search Bar -->
            <div class="relative w-full sm:w-60 lg:w-72">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk atau SKU..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 pl-8 pr-3 text-[11px] text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
            </div>

            <!-- Filters Group -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <!-- Category Filter Dropdown -->
                <select
                    wire:model.live="categoryFilter"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
                >
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter Dropdown -->
                <select
                    wire:model.live="statusFilter"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
                >
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="draft">Draft</option>
                    <option value="inactive">Nonaktif</option>
                    <option value="archived">Diarsipkan</option>
                </select>
            </div>
        </x-slot:controls>

        <x-slot:actions>
            <!-- Add Product Button (Opens Modal) -->
            <button
                type="button"
                wire:click="openCreateModal"
                class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-[11px] shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Produk</span>
            </button>
        </x-slot:actions>

        <!-- Product Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[10px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-4 py-3 font-medium">Informasi Produk</th>
                    <th class="px-4 py-3 font-medium hidden md:table-cell">Kategori</th>
                    <th class="px-4 py-3 font-medium">Varian & SKU</th>
                    <th class="px-4 py-3 font-medium">Rentang Harga</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($products as $product)
                    <tr wire:key="prod-row-{{ $product->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Product Info (Thumbnail + Name + Subtitle + Badge) -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <!-- Thumbnail Image or Fallback Initials -->
                                @if($product->featured_image_url)
                                    <img 
                                        src="{{ $product->featured_image_url }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-11 h-11 rounded-lg object-cover border border-[#CBAC70]/30 shrink-0 shadow-sm bg-[#0B132B]"
                                    >
                                @else
                                    <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-slate-800 to-[#0B132B] border border-[#CBAC70]/30 flex items-center justify-center text-[#CBAC70] font-bold text-xs shrink-0 shadow-sm">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($product->badge)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30">
                                                {{ $product->badge }}
                                            </span>
                                        @endif
                                        @if($product->gsm)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-medium bg-white/5 text-slate-300 border border-white/10">
                                                {{ $product->gsm }}GSM
                                            </span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-slate-100 group-hover:text-[#CBAC70] transition-colors text-xs leading-tight mt-0.5">
                                        {{ $product->name }}
                                    </p>
                                    @if($product->subtitle)
                                        <p class="text-slate-400 text-[10px] truncate max-w-sm">
                                            {{ $product->subtitle }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Category & Collections -->
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-800/80 text-slate-300 border border-slate-700/60">
                                {{ $product->category->name }}
                            </span>
                            @if($product->collections->isNotEmpty())
                                <div class="text-[10px] text-[#CBAC70]/80 font-mono mt-1 truncate max-w-[130px]" title="{{ $product->collections->pluck('name')->join(', ') }}">
                                    📁 {{ $product->collections->first()->name }}
                                </div>
                            @endif
                        </td>

                        <!-- Variants, Colors & Sizes -->
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <!-- Color Chips -->
                                @if(!empty($product->colors))
                                    <div class="flex items-center gap-1">
                                        @foreach(array_slice($product->colors, 0, 4) as $c)
                                            <span 
                                                class="w-3 h-3 rounded-full border border-white/20 shadow-xs inline-block" 
                                                style="background-color: {{ $c['hex'] }};"
                                                title="{{ $c['name'] }}"
                                            ></span>
                                        @endforeach
                                        @if(count($product->colors) > 4)
                                            <span class="text-[9px] text-slate-400 font-mono">+{{ count($product->colors) - 4 }}</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400 font-sans ml-1">({{ count($product->colors) }} warna)</span>
                                    </div>
                                @endif

                                <!-- SKUs & Size Badges -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach($product->variants->take(2) as $variant)
                                        <span class="font-mono text-[9px] px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-slate-300">
                                            {{ $variant->sku }}
                                        </span>
                                    @endforeach
                                    @if($product->variants->count() > 2)
                                        <span class="text-[9px] text-slate-400 font-mono">
                                            +{{ $product->variants->count() - 2 }} lagi
                                        </span>
                                    @endif
                                </div>

                                <div class="text-[10px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>{{ $product->variants->count() }} SKU</span>
                                    <span>•</span>
                                    <span class="font-semibold text-emerald-400">Stok {{ $product->available_stock }} pcs</span>
                                </div>
                            </div>
                        </td>

                        <!-- Price Range -->
                        <td class="px-4 py-3 font-mono font-bold text-xs text-[#CBAC70]">
                            {{ $product->formatted_price_range }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $product->status->badgeClasses() }}">
                                {{ $product->status->label() }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Edit Button -->
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $product->id }})"
                                    title="Edit Produk"
                                    class="p-1 rounded-lg text-slate-400 hover:text-[#CBAC70] hover:bg-white/5 transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $product->id }})"
                                    title="Hapus Produk"
                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-white/5 transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-200">Belum ada data produk</p>
                                <p class="text-xs text-slate-500 mt-1">Gunakan tombol "Tambah Produk" untuk membuat katalog baru beserta varian SKU-nya.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Custom Themed Pagination Slot -->
        <x-slot:pagination>
            {{ $products->links() }}
        </x-slot:pagination>
    </x-table-card>

    <!-- Reusable Create & Edit Product Modal -->
    <x-modal
        id="product-modal"
        :title="$isEditing ? 'Edit Informasi Produk & Varian' : 'Tambah Produk Busana Baru'"
        :subtitle="$isEditing ? 'Perbarui detail produk dan konfigurasi varian SKU' : 'Lengkapi detail busana dan tentukan matriks varian SKU beserta harganya'"
        maxWidth="5xl"
    >
        <form wire:submit="saveProduct" class="space-y-4">
            <!-- SECTION 1: Informasi Utama Produk -->
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 pb-1 border-b border-white/5">
                    <span class="w-4 h-4 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-[10px] flex items-center justify-center">1</span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Informasi Utama Produk</h3>
                </div>

                <!-- Row 1: Kategori, Status, Nama Produk -->
                <div class="grid grid-cols-12 gap-2.5">
                    <!-- Category (3 cols) -->
                    <div class="col-span-12 sm:col-span-3 space-y-1">
                        <label for="prod-category" class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Kategori <span class="text-rose-400">*</span>
                        </label>
                        <select
                            id="prod-category"
                            wire:model="category_id"
                            class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors"
                        >
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-[10px] text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status (3 cols) -->
                    <div class="col-span-12 sm:col-span-3 space-y-1">
                        <label for="prod-status" class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Status <span class="text-rose-400">*</span>
                        </label>
                        <select
                            id="prod-status"
                            wire:model="status"
                            class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors"
                        >
                            <option value="active">Aktif (Live)</option>
                            <option value="draft">Draft</option>
                            <option value="inactive">Nonaktif</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                    </div>

                    <!-- Product Name (6 cols) -->
                    <div class="col-span-12 sm:col-span-6 space-y-1">
                        <label for="prod-name" class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Nama Produk Busana <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            id="prod-name"
                            wire:model="name"
                            placeholder="misal: Obsidian Heavyweight Boxy Tee 300GSM"
                            class="w-full h-8.5 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"
                            required
                        >
                        @error('name')
                            <p class="text-[10px] text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Foto Utama & Deskripsi (Symmetric) -->
                <div class="grid grid-cols-12 gap-2.5 items-stretch">
                    <!-- Foto Utama (5 cols) -->
                    <div class="col-span-12 sm:col-span-5 p-2 rounded-xl bg-[#070C1A] border border-slate-800 flex items-center gap-2.5">
                        <!-- Preview Thumbnail -->
                        <div class="w-12 h-12 rounded-lg border border-white/20 bg-[#0B132B] overflow-hidden shrink-0 flex items-center justify-center">
                            @if($featured_image_file)
                                <img src="{{ $featured_image_file->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                            @elseif($featured_image)
                                <img src="{{ str_starts_with($featured_image, 'http') ? $featured_image : asset('storage/'.$featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                            @else
                                <span class="text-[8px] text-slate-500 font-mono">No Image</span>
                            @endif
                        </div>

                        <!-- Upload Actions -->
                        <div class="flex-1 space-y-1">
                            <label class="cursor-pointer block">
                                <input type="file" wire:model="featured_image_file" accept="image/*" class="hidden">
                                <span class="inline-flex items-center justify-center gap-1.5 px-2 py-1 rounded-md bg-[#CBAC70]/15 hover:bg-[#CBAC70]/25 border border-[#CBAC70]/40 text-[#CBAC70] text-[10px] font-bold transition-colors w-full">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    <span>Upload Foto Perangkat</span>
                                </span>
                            </label>
                            <input
                                type="text"
                                wire:model="featured_image"
                                placeholder="Atau link URL gambar..."
                                class="w-full h-6 bg-[#0B132B] border border-slate-700/80 rounded px-1.5 text-[9px] text-slate-300 focus:outline-none focus:border-[#CBAC70]"
                            >
                        </div>
                    </div>

                    <!-- Deskripsi Produk (7 cols) -->
                    <div class="col-span-12 sm:col-span-7 flex flex-col">
                        <textarea
                            id="prod-description"
                            wire:model="description"
                            rows="2"
                            placeholder="Deskripsi singkat busana (material kain, fitting, instruksi pencucian)..."
                            class="w-full flex-1 bg-[#070C1A] border border-slate-700/80 rounded-xl p-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] transition-colors resize-none"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Matriks Varian, Warna & Gambar -->
            <div class="space-y-2.5 pt-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1.5 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-[10px] flex items-center justify-center">2</span>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Matriks Varian SKU</h3>
                    </div>

                    <!-- Toolbar Actions in 1 Row -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Segmented Size Switcher -->
                        <div class="inline-flex p-0.5 rounded-lg bg-[#070C1A] border border-slate-800">
                            <button
                                type="button"
                                wire:click="$set('sizeType', 'letter')"
                                class="px-2.5 py-1 text-[11px] font-bold rounded-md transition-all cursor-pointer {{ $sizeType === 'letter' ? 'bg-[#CBAC70] text-[#0B132B] shadow-xs' : 'text-slate-400 hover:text-slate-200' }}"
                            >
                                Abjad (S-XL)
                            </button>
                            <button
                                type="button"
                                wire:click="$set('sizeType', 'numeric')"
                                class="px-2.5 py-1 text-[11px] font-bold rounded-md transition-all cursor-pointer {{ $sizeType === 'numeric' ? 'bg-[#CBAC70] text-[#0B132B] shadow-xs' : 'text-slate-400 hover:text-slate-200' }}"
                            >
                                Nomor (28-38)
                            </button>
                        </div>

                        <!-- Quick Generate Button -->
                        @if($sizeType === 'letter')
                            <button
                                type="button"
                                wire:click="generateStandardSizes"
                                class="px-2.5 py-1 rounded-lg border border-[#CBAC70]/40 bg-[#CBAC70]/10 hover:bg-[#CBAC70]/20 text-[#CBAC70] text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap"
                            >
                                + Quick S, M, L, XL
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="generateNumberSizes"
                                class="px-2.5 py-1 rounded-lg border border-blue-500/40 bg-blue-500/10 hover:bg-blue-500/20 text-blue-300 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap"
                            >
                                + Quick 28, 30, 32, 34
                            </button>
                        @endif

                        <!-- Add Single Variant Button -->
                        <button
                            type="button"
                            wire:click="addVariant"
                            class="px-2.5 py-1 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-bold transition-colors cursor-pointer whitespace-nowrap"
                        >
                            + Tambah Varian
                        </button>
                    </div>
                </div>

                <!-- Variants 2-Row Compact Cards (Rapi, Ringkas & Aligned) -->
                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @foreach($variants as $index => $variant)
                        <div wire:key="variant-card-{{ $index }}" class="rounded-xl border border-slate-800 bg-[#070D1F] p-2.5 space-y-2 transition-colors hover:border-[#CBAC70]/40 shadow-sm">
                            
                            <!-- BARIS 1: IDENTITAS & FOTO -->
                            <div class="grid grid-cols-12 gap-2.5 items-end">
                                <!-- SKU (3 cols) -->
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-[9px] font-mono text-[#CBAC70] font-bold uppercase mb-0.5">Kode SKU</label>
                                    <input
                                        type="text"
                                        wire:model="variants.{{ $index }}.sku"
                                        placeholder="MLG-TS-BLK-S"
                                        class="w-full h-8 uppercase font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2.5 text-xs text-[#CBAC70] font-bold focus:outline-none focus:border-[#CBAC70]"
                                        required
                                    >
                                </div>

                                <!-- Warna & Hex Swatch (3 cols) -->
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-[9px] font-mono text-slate-400 font-bold uppercase mb-0.5">Warna & Hex</label>
                                    <div class="flex items-center gap-1.5 h-8 bg-[#0B132B] border border-slate-700/80 rounded-lg px-2">
                                        <input
                                            type="color"
                                            wire:model="variants.{{ $index }}.color_hex"
                                            title="Pilih Warna Hex"
                                            class="w-5 h-5 rounded cursor-pointer bg-transparent border-0 p-0 shrink-0"
                                        >
                                        <input
                                            type="text"
                                            wire:model="variants.{{ $index }}.color_name"
                                            placeholder="misal: Onyx Black"
                                            class="w-full bg-transparent border-0 p-0 text-xs text-slate-200 focus:outline-none"
                                        >
                                    </div>
                                </div>

                                <!-- Ukuran Dropdown (2 cols) -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label class="block text-[9px] font-mono text-slate-400 font-bold uppercase mb-0.5">Ukuran</label>
                                    <select
                                        wire:model="variants.{{ $index }}.size"
                                        class="w-full h-8 bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] cursor-pointer"
                                    >
                                        @if($sizeType === 'letter')
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                            <option value="XXXL">XXXL</option>
                                            <option value="All Size">All Size</option>
                                        @else
                                            <option value="28">28</option>
                                            <option value="29">29</option>
                                            <option value="30">30</option>
                                            <option value="31">31</option>
                                            <option value="32">32</option>
                                            <option value="33">33</option>
                                            <option value="34">34</option>
                                            <option value="36">36</option>
                                            <option value="38">38</option>
                                        @endif
                                    </select>
                                </div>

                                <!-- Foto Varian Upload (4 cols) -->
                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block text-[9px] font-mono text-slate-400 font-bold uppercase mb-0.5">Foto Varian Warna</label>
                                    <div class="flex items-center gap-1.5 h-8">
                                        <!-- Thumbnail Box -->
                                        <div class="w-8 h-8 rounded-lg border border-white/20 bg-[#0B132B] overflow-hidden shrink-0 flex items-center justify-center">
                                            @if(isset($variant_image_files[$index]) && is_object($variant_image_files[$index]))
                                                <img src="{{ $variant_image_files[$index]->temporaryUrl() }}" alt="" class="w-full h-full object-cover">
                                            @elseif(!empty($variant['image_url']))
                                                <img src="{{ str_starts_with($variant['image_url'], 'http') ? $variant['image_url'] : asset('storage/'.$variant['image_url']) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[7px] text-slate-500 font-mono">No Pic</span>
                                            @endif
                                        </div>

                                        <!-- Upload File Button -->
                                        <label class="flex-1 cursor-pointer">
                                            <input type="file" wire:model="variant_image_files.{{ $index }}" accept="image/*" class="hidden">
                                            <span class="flex items-center justify-center gap-1 h-8 px-2 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 font-medium transition-colors w-full">
                                                <svg class="w-3.5 h-3.5 text-[#CBAC70]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span>Pilih Foto</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- BARIS 2: FINANSIAL & AKSI -->
                            <div class="grid grid-cols-12 gap-2.5 items-end pt-1.5 border-t border-slate-800/80">
                                <!-- Harga Jual (4 cols) -->
                                <div class="col-span-12 sm:col-span-4">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <label class="text-[9px] font-mono text-[#CBAC70] font-bold uppercase">Harga Jual</label>
                                        <span class="text-[9px] font-mono text-[#CBAC70] font-bold">
                                            Rp {{ number_format((int) ($variant['price'] ?? 0), 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-2 text-[11px] text-slate-400 font-mono font-bold">Rp</span>
                                        <input
                                            type="number"
                                            wire:model.live="variants.{{ $index }}.price"
                                            min="0"
                                            placeholder="299000"
                                            class="w-full h-8 font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg pl-8 pr-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"
                                            required
                                        >
                                    </div>
                                </div>

                                <!-- Harga Coret (3 cols) -->
                                <div class="col-span-12 sm:col-span-3">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <label class="text-[9px] font-mono text-slate-400 font-bold uppercase">Harga Coret</label>
                                        @if(!empty($variant['compare_at_price']))
                                            <span class="text-[9px] font-mono text-slate-400 line-through">
                                                Rp {{ number_format((int) $variant['compare_at_price'], 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-2 text-[11px] text-slate-500 font-mono font-bold">Rp</span>
                                        <input
                                            type="number"
                                            wire:model.live="variants.{{ $index }}.compare_at_price"
                                            min="0"
                                            placeholder="349000"
                                            class="w-full h-8 font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg pl-8 pr-2 text-xs text-slate-400 focus:outline-none focus:border-[#CBAC70]"
                                        >
                                    </div>
                                </div>

                                <!-- Berat (2 cols) -->
                                <div class="col-span-6 sm:col-span-2">
                                    <label class="block text-[9px] font-mono text-slate-400 font-bold uppercase mb-0.5">Berat (g)</label>
                                    <div class="relative">
                                        <input
                                            type="number"
                                            wire:model="variants.{{ $index }}.weight_grams"
                                            min="1"
                                            placeholder="250"
                                            class="w-full h-8 font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70]"
                                            required
                                        >
                                        <span class="absolute right-2 top-2 text-[10px] text-slate-500 font-mono">g</span>
                                    </div>
                                </div>

                                <!-- Status Aktif (2 cols) -->
                                <div class="col-span-4 sm:col-span-2 flex items-center h-8 px-1">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="variants.{{ $index }}.is_active"
                                            class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70]"
                                        >
                                        <span class="text-xs font-semibold {{ ($variant['is_active'] ?? true) ? 'text-emerald-400' : 'text-slate-500' }}">
                                            {{ ($variant['is_active'] ?? true) ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>

                                <!-- Tombol Hapus (1 col) -->
                                <div class="col-span-2 sm:col-span-1 flex justify-end h-8 items-center">
                                    <button
                                        type="button"
                                        wire:click="removeVariant({{ $index }})"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                                        title="Hapus Varian Ini"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Action Buttons Footer -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-product-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveProduct">
                        {{ $isEditing ? 'Simpan Perubahan Produk' : 'Buat Produk & Varian' }}
                    </span>
                    <span wire:loading.inline-flex wire:target="saveProduct" class="items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#0B132B]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Reusable Delete Confirmation Modal -->
    <x-confirmation-modal
        id="delete-product-modal"
        title="Konfirmasi Hapus Produk"
        message="Apakah Anda yakin ingin menghapus produk ini?"
        confirmText="Ya, Hapus Produk"
        cancelText="Batal"
        type="danger"
        icon="delete"
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="deleteProduct"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-950/30 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="deleteProduct">Ya, Hapus Sekarang</span>
                <span wire:loading.inline-flex wire:target="deleteProduct" class="items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Menghapus...</span>
                </span>
            </button>
        </x-slot:action>
    </x-confirmation-modal>
</div>

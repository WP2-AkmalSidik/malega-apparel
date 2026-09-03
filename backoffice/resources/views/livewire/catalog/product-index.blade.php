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
        maxWidth="4xl"
    >
        <form wire:submit="saveProduct" class="space-y-6">
            <!-- SECTION 1: Informasi Utama Produk -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-white/5">
                    <span class="w-5 h-5 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-xs flex items-center justify-center">1</span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Informasi Utama Produk</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Category Select -->
                    <div class="space-y-1.5">
                        <label for="prod-category" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Kategori Busana <span class="text-rose-400 font-mono">*</span>
                        </label>
                        <select
                            id="prod-category"
                            wire:model="category_id"
                            class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                        >
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Select -->
                    <div class="space-y-1.5">
                        <label for="prod-status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Status Publikasi <span class="text-rose-400 font-mono">*</span>
                        </label>
                        <select
                            id="prod-status"
                            wire:model="status"
                            class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                        >
                            <option value="active">Aktif (Live di Storefront)</option>
                            <option value="draft">Draft (Disimpan Sementara)</option>
                            <option value="inactive">Nonaktif</option>
                            <option value="archived">Diarsipkan</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Product Name -->
                    <div class="sm:col-span-2">
                        <x-input
                            wire:model="name"
                            label="Nama Produk"
                            name="name"
                            placeholder="misal: Obsidian Heavyweight Boxy Tee 300GSM"
                            required="true"
                        />
                    </div>

                    <!-- Slug (Optional) -->
                    <x-input
                        wire:model="slug"
                        label="Slug URL (Opsional)"
                        name="slug"
                        placeholder="misal: m-boxy-tee-heavyweight-300gsm"
                    />
                </div>

                <!-- Featured Image URL -->
                <div class="grid grid-cols-1 gap-4">
                    <x-input
                        wire:model="featured_image"
                        label="Foto Utama Produk (Featured Image URL)"
                        name="featured_image"
                        placeholder="https://images.unsplash.com/... atau storage/products/utama.jpg"
                    />
                </div>

                <!-- Description -->
                <div class="space-y-1.5">
                    <label for="prod-description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Deskripsi Produk
                    </label>
                    <textarea
                        id="prod-description"
                        wire:model="description"
                        rows="3"
                        placeholder="Jelaskan material kain, potongan fitting, instruksi pencucian, dan detail keunggulan busana..."
                        class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                    ></textarea>
                    @error('description')
                        <p class="text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- SECTION 2: Matriks Varian, Warna & Gambar (ADR-002 & ADR-005) -->
            <div class="space-y-4 pt-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-2 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-xs flex items-center justify-center">2</span>
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Matriks Varian, Warna & Gambar</h3>
                            <p class="text-[11px] text-slate-400">Atur kombinasi warna, foto varian, ukuran, dan harga khusus tiap SKU</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Quick Size Generator -->
                        <button
                            type="button"
                            wire:click="generateStandardSizes"
                            class="px-2.5 py-1 rounded-lg border border-[#CBAC70]/40 bg-[#CBAC70]/10 hover:bg-[#CBAC70]/20 text-[#CBAC70] text-[11px] font-semibold transition-colors cursor-pointer"
                        >
                            + Quick S, M, L, XL
                        </button>

                        <!-- Add Variant Row Button -->
                        <button
                            type="button"
                            wire:click="addVariant"
                            class="px-2.5 py-1 rounded-lg border border-slate-700 bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold transition-colors cursor-pointer"
                        >
                            + Tambah Varian
                        </button>
                    </div>
                </div>

                <!-- Variants Dynamic Table -->
                <div class="rounded-2xl border border-slate-800 bg-[#070C1A] overflow-hidden">
                    <div class="overflow-x-auto max-h-80">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-white/[0.02] border-b border-slate-800 font-mono text-[10px] text-slate-400 uppercase">
                                <tr>
                                    <th class="px-2.5 py-2.5">Kode SKU</th>
                                    <th class="px-2.5 py-2.5 min-w-[140px]">Warna & Hex</th>
                                    <th class="px-2.5 py-2.5 w-24">Ukuran</th>
                                    <th class="px-2.5 py-2.5 min-w-[160px]">Foto Varian (URL)</th>
                                    <th class="px-2.5 py-2.5 w-28">Harga (Rp)</th>
                                    <th class="px-2.5 py-2.5 hidden sm:table-cell w-28">Coret (Rp)</th>
                                    <th class="px-2.5 py-2.5 hidden md:table-cell w-20">Berat (g)</th>
                                    <th class="px-2.5 py-2.5 text-center w-12">Aktif</th>
                                    <th class="px-2.5 py-2.5 text-center w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @foreach($variants as $index => $variant)
                                    <tr wire:key="variant-row-{{ $index }}" class="hover:bg-white/[0.01]">
                                        <!-- SKU -->
                                        <td class="p-2">
                                            <input
                                                type="text"
                                                wire:model="variants.{{ $index }}.sku"
                                                placeholder="MLG-TS-BLK-M"
                                                class="w-full uppercase font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-[#CBAC70] focus:outline-none focus:border-[#CBAC70]"
                                                required
                                            >
                                        </td>

                                        <!-- Color & Hex -->
                                        <td class="p-2">
                                            <div class="flex items-center gap-1.5">
                                                <input
                                                    type="color"
                                                    wire:model="variants.{{ $index }}.color_hex"
                                                    title="Pilih Warna Hex"
                                                    class="w-6 h-6 rounded-md bg-transparent border-0 cursor-pointer p-0 shrink-0"
                                                >
                                                <input
                                                    type="text"
                                                    wire:model="variants.{{ $index }}.color_name"
                                                    placeholder="Onyx Black"
                                                    class="w-full bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"
                                                >
                                            </div>
                                        </td>

                                        <!-- Size -->
                                        <td class="p-2">
                                            <select
                                                wire:model="variants.{{ $index }}.size"
                                                class="w-full bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] cursor-pointer"
                                            >
                                                <option value="S">S</option>
                                                <option value="M">M</option>
                                                <option value="L">L</option>
                                                <option value="XL">XL</option>
                                                <option value="XXL">XXL</option>
                                                <option value="28">28</option>
                                                <option value="30">30</option>
                                                <option value="32">32</option>
                                                <option value="34">34</option>
                                                <option value="36">36</option>
                                                <option value="All Size">All Size</option>
                                            </select>
                                        </td>

                                        <!-- Image URL -->
                                        <td class="p-2">
                                            <div class="flex items-center gap-1.5">
                                                @if(!empty($variant['image_url']))
                                                    <img src="{{ $variant['image_url'] }}" alt="" class="w-6 h-6 rounded object-cover border border-white/20 shrink-0 bg-[#0B132B]">
                                                @endif
                                                <input
                                                    type="text"
                                                    wire:model.lazy="variants.{{ $index }}.image_url"
                                                    placeholder="URL Foto..."
                                                    class="w-full bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70]"
                                                >
                                            </div>
                                        </td>

                                        <!-- Price -->
                                        <td class="p-2">
                                            <input
                                                type="number"
                                                wire:model="variants.{{ $index }}.price"
                                                min="0"
                                                placeholder="299000"
                                                class="w-full font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"
                                                required
                                            >
                                        </td>

                                        <!-- Compare-at Price -->
                                        <td class="p-2 hidden sm:table-cell">
                                            <input
                                                type="number"
                                                wire:model="variants.{{ $index }}.compare_at_price"
                                                min="0"
                                                placeholder="349000"
                                                class="w-full font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-slate-400 focus:outline-none focus:border-[#CBAC70]"
                                            >
                                        </td>

                                        <!-- Weight -->
                                        <td class="p-2 hidden md:table-cell">
                                            <input
                                                type="number"
                                                wire:model="variants.{{ $index }}.weight_grams"
                                                min="1"
                                                placeholder="250"
                                                class="w-full font-mono bg-[#0B132B] border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70]"
                                                required
                                            >
                                        </td>

                                        <!-- Active -->
                                        <td class="p-2 text-center">
                                            <input
                                                type="checkbox"
                                                wire:model="variants.{{ $index }}.is_active"
                                                class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70]"
                                            >
                                        </td>

                                        <!-- Delete Variant Row -->
                                        <td class="p-2 text-center">
                                            <button
                                                type="button"
                                                wire:click="removeVariant({{ $index }})"
                                                class="text-slate-500 hover:text-rose-400 p-1 transition-colors cursor-pointer"
                                                title="Hapus Varian"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

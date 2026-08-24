<div class="flex min-h-screen bg-slate-950 text-slate-100 font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <!-- Brand -->
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 flex items-center justify-center font-extrabold text-black text-lg shadow-lg">
                    M
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-wider text-white font-['Space_Grotesk']">MALEGA</h1>
                    <p class="text-[10px] uppercase tracking-widest text-amber-400 font-semibold">Backoffice Admin</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5 text-sm font-medium">
                <button 
                    wire:click="setTab('overview')"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $activeTab === 'overview' ? 'bg-amber-500 text-black font-bold shadow-md shadow-amber-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard Overview
                </button>

                <button 
                    wire:click="setTab('products')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $activeTab === 'products' ? 'bg-amber-500 text-black font-bold shadow-md shadow-amber-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Product Catalog
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'products' ? 'bg-black text-amber-400' : 'bg-slate-800 text-slate-400' }}">
                        {{ count($products) }}
                    </span>
                </button>

                <button 
                    wire:click="setTab('orders')"
                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $activeTab === 'orders' ? 'bg-amber-500 text-black font-bold shadow-md shadow-amber-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Orders & Sales
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $activeTab === 'orders' ? 'bg-black text-amber-400' : 'bg-slate-800 text-slate-400' }}">
                        {{ count($recentOrders) }}
                    </span>
                </button>
            </nav>
        </div>

        <!-- Sidebar Footer Info -->
        <div class="p-4 border-t border-slate-800 space-y-3">
            <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-mono">Livewire</span>
                    <span class="text-emerald-400 font-semibold font-mono">v4.4 (Active)</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-mono">Laravel</span>
                    <span class="text-amber-400 font-semibold font-mono">13.26</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-mono">Database</span>
                    <span class="text-cyan-400 font-semibold font-mono">MySQL</span>
                </div>
            </div>

            <a 
                href="http://localhost:4321" 
                target="_blank" 
                class="flex items-center justify-center gap-2 w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-colors"
            >
                <span>Lihat Landing Page (Astro)</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Topbar -->
        <header class="h-16 bg-slate-900/80 backdrop-blur border-b border-slate-800 px-6 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4 flex-1 max-w-lg">
                <div class="relative w-full">
                    <input 
                        type="text" 
                        wire:model.live.debounce.250ms="searchQuery"
                        placeholder="Cari SKU, nama produk atau order..."
                        class="w-full bg-slate-950 border border-slate-700/80 rounded-xl pl-9 pr-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-colors"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    wire:click="openAddModal"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-xs font-bold transition-all shadow-md active:scale-95"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </button>

                <div class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-amber-400">
                    AD
                </div>
            </div>
        </header>

        <!-- Notification Toast -->
        @if ($notificationMessage)
            <div class="mx-6 mt-4 p-4 rounded-xl bg-slate-900 border border-amber-500/40 text-amber-300 text-xs flex items-center justify-between shadow-xl animate-fade-in">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span>{{ $notificationMessage }}</span>
                </div>
                <button wire:click="dismissNotification" class="text-slate-400 hover:text-white text-base leading-none">
                    ✕
                </button>
            </div>
        @endif

        <!-- Main Body -->
        <main class="p-6 space-y-6 flex-1 overflow-y-auto">
            
            <!-- Header Title -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-white font-['Space_Grotesk']">
                        @if ($activeTab === 'overview') Dashboard Analitik
                        @elseif ($activeTab === 'products') Manajemen Katalog & Stok
                        @else Daftar Pesanan Pelanggan
                        @endif
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Sistem Manajemen Terintegrasi Malega Apparel</p>
                </div>

                <!-- Tabs switcher for mobile -->
                <div class="flex md:hidden bg-slate-900 p-1 rounded-xl border border-slate-800">
                    <button wire:click="setTab('overview')" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $activeTab === 'overview' ? 'bg-amber-500 text-black font-bold' : 'text-slate-400' }}">Overview</button>
                    <button wire:click="setTab('products')" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $activeTab === 'products' ? 'bg-amber-500 text-black font-bold' : 'text-slate-400' }}">Products</button>
                    <button wire:click="setTab('orders')" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $activeTab === 'orders' ? 'bg-amber-500 text-black font-bold' : 'text-slate-400' }}">Orders</button>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Revenue Card -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">Estimasi Pendapatan</span>
                        <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">+18.4%</span>
                    </div>
                    <p class="text-2xl font-extrabold text-white font-['Space_Grotesk'] mt-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Berdasarkan total item terjual</p>
                </div>

                <!-- Total Stock Card -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">Total Stok Gudang</span>
                        <span class="text-xs font-bold text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-full">{{ count($products) }} SKUs</span>
                    </div>
                    <p class="text-2xl font-extrabold text-white font-['Space_Grotesk'] mt-2">
                        {{ $totalStock }} <span class="text-sm font-normal text-slate-400">pcs</span>
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Tersedia di inventory utama</p>
                </div>

                <!-- Low Stock Alert Card -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">Peringatan Stok Rendah</span>
                        <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full">Perlu Restock</span>
                    </div>
                    <p class="text-2xl font-extrabold text-amber-400 font-['Space_Grotesk'] mt-2">
                        {{ $lowStockCount }} <span class="text-sm font-normal text-slate-400">SKU</span>
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Stok &le; 10 pcs</p>
                </div>

                <!-- Recent Orders Count -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-medium">Pesanan Masuk</span>
                        <span class="text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full">Hari Ini</span>
                    </div>
                    <p class="text-2xl font-extrabold text-white font-['Space_Grotesk'] mt-2">
                        {{ count($recentOrders) }} <span class="text-sm font-normal text-slate-400">order</span>
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">100% terproses sistem</p>
                </div>

            </div>

            <!-- Tab: Overview or Products Table -->
            @if ($activeTab === 'overview' || $activeTab === 'products')
                <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-sm">
                    
                    <!-- Table Toolbar -->
                    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-white font-['Space_Grotesk']">Katalog Produk & Stok</h3>
                            <p class="text-xs text-slate-400">Kelola inventaris dan pantau performa penjualan produk secara langsung</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <select 
                                wire:model.live="selectedCategory" 
                                class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500"
                            >
                                <option value="all">Semua Kategori</option>
                                <option value="T-Shirt">T-Shirt</option>
                                <option value="Bottoms">Bottoms</option>
                                <option value="Outerwear">Outerwear</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider font-mono border-b border-slate-800">
                                <tr>
                                    <th class="px-6 py-3.5">SKU & Nama Produk</th>
                                    <th class="px-6 py-3.5">Kategori</th>
                                    <th class="px-6 py-3.5">Harga</th>
                                    <th class="px-6 py-3.5">Stok Saat Ini</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @forelse ($filteredProducts as $item)
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-white text-sm font-['Space_Grotesk']">{{ $item['name'] }}</div>
                                            <div class="text-[11px] font-mono text-amber-400/90">{{ $item['sku'] }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300">
                                                {{ $item['category'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-slate-200">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-sm {{ $item['stock'] <= 10 ? 'text-amber-400' : 'text-white' }}">
                                                    {{ $item['stock'] }}
                                                </span>
                                                <span class="text-[11px] text-slate-500">pcs</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($item['stock'] > 10)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> In Stock
                                                </span>
                                            @elseif ($item['stock'] > 0)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Low Stock
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Out of Stock
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex items-center gap-1 bg-slate-950 border border-slate-700 rounded-lg p-1">
                                                <button 
                                                    wire:click="adjustStock({{ $item['id'] }}, -1)"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-colors font-bold text-xs"
                                                    title="Kurangi 1 stok"
                                                >
                                                    -
                                                </button>
                                                <button 
                                                    wire:click="adjustStock({{ $item['id'] }}, 5)"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-amber-400 hover:bg-slate-800 transition-colors font-bold text-xs"
                                                    title="Tambah 5 stok"
                                                >
                                                    +5
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                            Tidak ada produk yang cocok dengan kata kunci pencarian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            @endif

            <!-- Tab: Orders / Recent Orders in Overview -->
            @if ($activeTab === 'overview' || $activeTab === 'orders')
                <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-sm">
                    <div class="p-5 border-b border-slate-800">
                        <h3 class="text-base font-bold text-white font-['Space_Grotesk']">Pesanan Terbaru</h3>
                        <p class="text-xs text-slate-400">Transaksi order apparel terkini dari landing page & marketplace</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider font-mono border-b border-slate-800">
                                <tr>
                                    <th class="px-6 py-3.5">ID Order</th>
                                    <th class="px-6 py-3.5">Nama Pelanggan</th>
                                    <th class="px-6 py-3.5">Rincian Item</th>
                                    <th class="px-6 py-3.5">Total Bayar</th>
                                    <th class="px-6 py-3.5">Status Order</th>
                                    <th class="px-6 py-3.5">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                @foreach ($recentOrders as $ord)
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-6 py-4 font-mono font-bold text-amber-400">
                                            {{ $ord['order_id'] }}
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-white">
                                            {{ $ord['customer'] }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-300">
                                            {{ $ord['items'] }}
                                        </td>
                                        <td class="px-6 py-4 font-bold text-white">
                                            Rp {{ number_format($ord['total'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($ord['status'] === 'Paid')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Paid</span>
                                            @elseif ($ord['status'] === 'Packing')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Packing</span>
                                            @elseif ($ord['status'] === 'Shipped')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Shipped</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">Delivered</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 text-[11px]">
                                            {{ $ord['created_at'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </main>
    </div>

    <!-- Livewire Modal: Tambah Produk Baru -->
    @if ($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-white font-['Space_Grotesk']">Tambah SKU Produk Baru</h3>
                    <button wire:click="closeAddModal" class="text-slate-400 hover:text-white text-base">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Produk Apparel</label>
                        <input 
                            type="text" 
                            wire:model="newProductName"
                            placeholder="Contoh: Heavyweight Acid Wash Hoodie"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-500"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Kategori</label>
                            <select 
                                wire:model="newProductCategory"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-500"
                            >
                                <option value="T-Shirt">T-Shirt</option>
                                <option value="Bottoms">Bottoms</option>
                                <option value="Outerwear">Outerwear</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Kode SKU</label>
                            <input 
                                type="text" 
                                wire:model="newProductSku"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white font-mono focus:outline-none focus:border-amber-500"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Harga (IDR)</label>
                            <input 
                                type="number" 
                                wire:model="newProductPrice"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-500"
                            >
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Stok Awal</label>
                            <input 
                                type="number" 
                                wire:model="newProductStock"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-amber-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button 
                        wire:click="closeAddModal"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        wire:click="saveProduct"
                        class="px-5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-black text-xs font-bold transition-all shadow-md"
                    >
                        Simpan ke Katalog
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

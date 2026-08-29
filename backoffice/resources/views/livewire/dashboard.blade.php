<div class="space-y-6">
    <!-- Page Header & Filter Controls -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl text-ivory tracking-tight">Dashboard Overview</h1>
            <p class="text-xs text-slate-400 font-mono mt-1">Data metrik real-time Malega Apparel Backoffice &bull; {{ date('d F Y') }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button 
                type="button"
                wire:click="exportReport"
                class="flex items-center gap-2 bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] transition-all text-[#0B132B] text-xs font-bold px-4 py-2 rounded-xl cursor-pointer shadow-md shadow-[#CBAC70]/10"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                <span>Ekspor Laporan</span>
            </button>
        </div>
    </div>

    <!-- Stat Cards (4-Column Metric Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($this->stats as $key => $stat)
            <div wire:key="stat-card-{{ $key }}" class="bg-[#0B132B]/80 border border-[#CBAC70]/20 rounded-2xl p-5 shadow-lg shadow-black/40 hover:border-[#CBAC70]/40 transition-colors">
                <p class="text-[11px] font-semibold tracking-[0.15em] text-[#CBAC70] uppercase font-mono">{{ $stat['label'] }}</p>
                <p class="font-display text-2xl lg:text-3xl text-ivory mt-2 tracking-tight">{{ $stat['value'] }}</p>
                <div class="flex items-center gap-2 mt-3">
                    @if($stat['badgeType'] === 'emerald')
                        <span class="inline-flex items-center text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 text-[10px] px-2 py-0.5 rounded-full font-mono font-semibold">{{ $stat['badge'] }}</span>
                    @else
                        <span class="inline-flex items-center text-rose-400 bg-rose-500/10 border border-rose-500/30 text-[10px] px-2 py-0.5 rounded-full font-mono font-semibold">{{ $stat['badge'] }}</span>
                    @endif
                    <span class="text-[11px] text-slate-400 truncate">{{ $stat['comparison'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chart + Top Products Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend SVG Chart (Col-Span 2) -->
        <div class="lg:col-span-2 bg-[#0B132B]/80 border border-[#CBAC70]/20 rounded-2xl p-6 shadow-lg shadow-black/40">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.15em] text-[#CBAC70] uppercase font-mono">
                        Performa Penjualan
                    </p>
                    <h2 class="font-display text-xl text-ivory mt-1">Grafik Tren Omzet</h2>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-mono">
                    <span class="w-2 h-2 rounded-full bg-[#CBAC70]"></span> Pendapatan Terverifikasi
                </div>
            </div>

            <div class="w-full overflow-hidden">
                <svg viewBox="0 0 560 200" class="w-full h-48 sm:h-56">
                    <defs>
                        <linearGradient id="salesFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#CBAC70" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#CBAC70" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <g stroke="#FDFCFF" stroke-opacity="0.06">
                        <line x1="0" y1="20" x2="560" y2="20"/>
                        <line x1="0" y1="70" x2="560" y2="70"/>
                        <line x1="0" y1="120" x2="560" y2="120"/>
                        <line x1="0" y1="170" x2="560" y2="170"/>
                    </g>
                    <path d="M0,140 L80,110 L160,125 L240,80 L320,95 L400,50 L480,65 L560,30 L560,200 L0,200 Z" fill="url(#salesFill)"/>
                    <path d="M0,140 L80,110 L160,125 L240,80 L320,95 L400,50 L480,65 L560,30" fill="none" stroke="#CBAC70" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <g fill="#CBAC70">
                        <circle cx="0" cy="140" r="3.5"/>
                        <circle cx="80" cy="110" r="3.5"/>
                        <circle cx="160" cy="125" r="3.5"/>
                        <circle cx="240" cy="80" r="3.5"/>
                        <circle cx="320" cy="95" r="3.5"/>
                        <circle cx="400" cy="50" r="3.5"/>
                        <circle cx="480" cy="65" r="3.5"/>
                        <circle cx="560" cy="30" r="4" stroke="#0B132B" stroke-width="2"/>
                    </g>
                </svg>
            </div>

            <div class="flex justify-between text-[11px] text-slate-400 mt-2 px-1 font-mono">
                <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
            </div>
        </div>

        <!-- Best Sellers / Featured Products (Col-Span 1) -->
        <div class="bg-[#0B132B]/80 border border-[#CBAC70]/20 rounded-2xl p-6 shadow-lg shadow-black/40 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.15em] text-[#CBAC70] uppercase font-mono">Produk Populer</p>
                <h2 class="font-display text-xl text-ivory mt-1 mb-4">Terlaris Bulan Ini</h2>

                <div class="space-y-3.5">
                    @foreach($this->topProducts as $product)
                        <div wire:key="top-prod-{{ $loop->index }}" class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#CBAC70] to-[#BD9B58] flex items-center justify-center text-[#0B132B] text-xs font-bold shrink-0 shadow-xs">
                                {{ strtoupper(substr($product->product_name ?? 'MLG', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-slate-200 truncate font-semibold">{{ $product->product_name }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $product->sku }} &bull; {{ $product->total_sold }} terjual</p>
                            </div>
                            <p class="text-xs font-mono text-[#CBAC70] font-bold shrink-0">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-800 text-center">
                <a href="{{ route('catalog.products') }}" class="text-xs text-[#CBAC70] hover:text-[#DFB67A] transition-colors font-medium">
                    Lihat Seluruh Katalog &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Feeds: Recent Orders & Low Stock Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders Feed (Col-Span 2) -->
        <div class="lg:col-span-2 bg-[#0B132B]/80 border border-[#CBAC70]/20 rounded-2xl p-6 shadow-lg shadow-black/40">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.15em] text-[#CBAC70] uppercase font-mono">Transaksi Terkini</p>
                    <h2 class="font-display text-lg text-ivory mt-0.5">Pesanan Masuk Terbaru</h2>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs text-[#CBAC70] hover:text-[#DFB67A] font-semibold transition-colors">
                    Semua Pesanan &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-mono text-slate-400 uppercase border-b border-slate-800">
                            <th class="py-2.5">No. Pesanan</th>
                            <th class="py-2.5">Customer</th>
                            <th class="py-2.5 text-right">Total</th>
                            <th class="py-2.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($this->recentOrders as $ro)
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-3 font-mono font-bold text-[#CBAC70]">{{ $ro->order_number }}</td>
                                <td class="py-3 text-slate-200">{{ $ro->customer?->name ?? 'Customer' }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-100">{{ $ro->formatted_grand_total }}</td>
                                <td class="py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $ro->order_status->badgeClasses() }}">
                                        {{ $ro->order_status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-500 text-xs">Belum ada pesanan terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alerts Feed (Col-Span 1) -->
        <div class="bg-[#0B132B]/80 border border-[#CBAC70]/20 rounded-2xl p-6 shadow-lg shadow-black/40 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-[11px] font-semibold tracking-[0.15em] text-[#CBAC70] uppercase font-mono">Peringatan Gudang</p>
                        <h2 class="font-display text-lg text-ivory mt-0.5">Stok Menipis</h2>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                </div>

                <div class="space-y-3">
                    @forelse($this->lowStockItems as $lsi)
                        <div class="p-2.5 rounded-xl bg-[#070C1A] border border-slate-800/80 flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="font-mono font-bold text-xs text-[#CBAC70]">{{ $lsi->variant->sku }}</p>
                                <p class="text-[11px] text-slate-300 truncate">{{ $lsi->variant->product->name }}</p>
                            </div>
                            <span class="font-mono font-bold text-xs {{ $lsi->available <= 0 ? 'text-rose-400' : 'text-amber-400' }}">
                                Sisa {{ $lsi->available }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-slate-500 text-xs py-4">Semua stok varian dalam kondisi aman.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-800 text-center">
                <a href="{{ route('inventory.index') }}" class="text-xs text-[#CBAC70] hover:text-[#DFB67A] transition-colors font-medium">
                    Buka Manajemen Inventori &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

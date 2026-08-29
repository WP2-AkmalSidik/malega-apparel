<div class="space-y-6">
    <!-- Page Header & Filter Controls -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl text-ivory tracking-tight">Dashboard</h1>
            <p class="text-sm text-ivory/40 mt-1">Ringkasan performa toko — {{ date('d F Y') }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center bg-white/5 border border-white/10 rounded-lg p-1 text-xs">
                <button 
                    type="button"
                    wire:click="setTimeRange('7d')"
                    class="px-3 py-1.5 rounded-md font-semibold transition-all cursor-pointer {{ $timeRange === '7d' ? 'bg-gold text-navy shadow-sm' : 'text-ivory/50 hover:text-ivory' }}"
                >
                    7 Hari
                </button>
                <button 
                    type="button"
                    wire:click="setTimeRange('30d')"
                    class="px-3 py-1.5 rounded-md font-semibold transition-all cursor-pointer {{ $timeRange === '30d' ? 'bg-gold text-navy shadow-sm' : 'text-ivory/50 hover:text-ivory' }}"
                >
                    30 Hari
                </button>
                <button 
                    type="button"
                    wire:click="setTimeRange('1y')"
                    class="px-3 py-1.5 rounded-md font-semibold transition-all cursor-pointer {{ $timeRange === '1y' ? 'bg-gold text-navy shadow-sm' : 'text-ivory/50 hover:text-ivory' }}"
                >
                    1 Tahun
                </button>
            </div>

            <button 
                type="button"
                wire:click="exportReport"
                class="flex items-center gap-2 bg-gold hover:bg-gold-dark transition-colors text-navy text-sm font-semibold px-4 py-2 rounded-lg cursor-pointer shadow-sm active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Ekspor
            </button>
        </div>
    </div>

    <!-- Stat Cards (4-Column Metric Grid) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($this->stats as $key => $stat)
            <div wire:key="stat-card-{{ $key }}" class="bg-navy-light border border-gold/15 rounded-xl p-5 shadow-lg shadow-black/20 hover:border-gold/30 transition-colors">
                <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase font-mono">{{ $stat['label'] }}</p>
                <p class="font-display text-2xl lg:text-3xl text-ivory mt-2 tracking-tight">{{ $stat['value'] }}</p>
                <div class="flex items-center gap-2 mt-3">
                    @if($stat['badgeType'] === 'emerald')
                        <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full font-mono">{{ $stat['badge'] }}</span>
                    @else
                        <span class="tag-badge text-red-400 border border-red-400/40 text-[11px] pl-4 pr-2 py-0.5 rounded-full font-mono">{{ $stat['badge'] }}</span>
                    @endif
                    <span class="text-[11px] text-ivory/30">{{ $stat['comparison'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chart + Top Products Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend SVG Chart (Col-Span 2) -->
        <div class="lg:col-span-2 bg-navy-light border border-gold/15 rounded-xl p-6 shadow-lg shadow-black/20">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase font-mono">
                        Performa {{ $timeRange === '7d' ? '7 Hari Terakhir' : ($timeRange === '30d' ? '30 Hari Terakhir' : '1 Tahun Terakhir') }}
                    </p>
                    <h2 class="font-display text-xl text-ivory mt-1">Tren Penjualan</h2>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-ivory/40 font-mono">
                    <span class="w-2 h-2 rounded-full bg-gold"></span> Pendapatan (IDR)
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

            <div class="flex justify-between text-[11px] text-ivory/30 mt-2 px-1 font-mono">
                <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
            </div>
        </div>

        <!-- Best Sellers / Featured Products (Col-Span 1) -->
        <div class="bg-navy-light border border-gold/15 rounded-xl p-6 shadow-lg shadow-black/20 flex flex-col justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase font-mono">Terlaris Minggu Ini</p>
                <h2 class="font-display text-xl text-ivory mt-1 mb-4">Produk Unggulan</h2>

                <div class="space-y-4">
                    @foreach($this->topProducts as $product)
                        <div wire:key="top-prod-{{ $product['code'] }}" class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy text-xs font-bold shrink-0 shadow-sm">
                                {{ $product['code'] }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-ivory truncate font-medium">{{ $product['name'] }}</p>
                                <p class="text-[11px] text-ivory/40">{{ $product['sold'] }}</p>
                            </div>
                            <p class="text-sm font-mono text-gold font-semibold shrink-0">{{ $product['price'] }}</p>
                        </div>
                        @if(!$loop->last)
                            <div class="stitch"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-white/5 text-center">
                <a href="#" class="text-xs text-gold/80 hover:text-gold transition-colors font-medium">
                    Lihat Seluruh Katalog &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-navy-light border border-gold/15 rounded-xl overflow-hidden shadow-lg shadow-black/20">
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <p class="text-[11px] font-semibold tracking-[0.15em] text-gold/70 uppercase font-mono">Aktivitas Terkini</p>
                <h2 class="font-display text-xl text-ivory mt-1">Pesanan Terbaru</h2>
            </div>
            <a href="#" class="text-sm text-gold hover:text-gold-dark transition-colors font-semibold">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] text-ivory/40 uppercase tracking-wide border-y border-white/5 font-mono">
                        <th class="px-6 py-3 font-medium">ID Pesanan</th>
                        <th class="px-6 py-3 font-medium">Pelanggan</th>
                        <th class="px-6 py-3 font-medium hidden md:table-cell">Produk</th>
                        <th class="px-6 py-3 font-medium hidden sm:table-cell">Tanggal</th>
                        <th class="px-6 py-3 font-medium">Total</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($this->recentOrders as $order)
                        <tr wire:key="order-row-{{ $order['id'] }}" class="hover:bg-white/[.03] transition-colors">
                            <!-- Order ID -->
                            <td class="px-6 py-4 font-mono text-xs text-ivory/70 font-semibold">
                                {{ $order['id'] }}
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-[10px] font-semibold text-ivory">
                                        {{ $order['initials'] }}
                                    </div>
                                    <span class="text-ivory font-medium">{{ $order['customer'] }}</span>
                                </div>
                            </td>

                            <!-- Products -->
                            <td class="px-6 py-4 text-ivory/50 hidden md:table-cell">
                                {{ $order['products'] }}
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-ivory/50 hidden sm:table-cell font-mono text-xs">
                                {{ $order['date'] }}
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 font-mono text-gold font-semibold">
                                {{ $order['total'] }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($order['statusType'] === 'gold')
                                    <span class="tag-badge text-gold border border-gold/40 text-[11px] pl-4 pr-2 py-1 rounded-full font-medium">
                                        {{ $order['status'] }}
                                    </span>
                                @elseif($order['statusType'] === 'amber')
                                    <span class="tag-badge text-amber-300 border border-amber-300/40 text-[11px] pl-4 pr-2 py-1 rounded-full font-medium">
                                        {{ $order['status'] }}
                                    </span>
                                @elseif($order['statusType'] === 'emerald')
                                    <span class="tag-badge text-emerald-400 border border-emerald-400/40 text-[11px] pl-4 pr-2 py-1 rounded-full font-medium">
                                        {{ $order['status'] }}
                                    </span>
                                @else
                                    <span class="tag-badge text-red-400 border border-red-400/40 text-[11px] pl-4 pr-2 py-1 rounded-full font-medium">
                                        {{ $order['status'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-ivory/40 text-xs">
                                Tidak ada pesanan yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

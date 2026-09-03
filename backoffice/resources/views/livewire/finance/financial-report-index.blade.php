<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gold/15 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gold font-mono uppercase tracking-widest mb-1">
                <span>KEUANGAN & TREASURY</span>
                <span>•</span>
                <span>LAPORAN PENDAPATAN & SETTLEMENT</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-display font-semibold text-ivory tracking-wide flex items-center gap-2.5">
                <span>Laporan Keuangan & Laba Bersih</span>
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            <button 
                onclick="window.print()"
                class="px-4 py-2 rounded-lg bg-gold hover:bg-gold-light text-navy font-bold text-xs shadow-sm transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Laporan Keuangan</span>
            </button>
        </div>
    </div>

    <!-- Period Filter Pills -->
    <div class="flex flex-wrap items-center gap-2">
        <button 
            wire:click="$set('period', 'this_month')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'this_month' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Bulan Ini
        </button>
        <button 
            wire:click="$set('period', 'last_month')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'last_month' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Bulan Lalu
        </button>
        <button 
            wire:click="$set('period', 'this_quarter')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'this_quarter' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Kuartal Ini
        </button>
        <button 
            wire:click="$set('period', 'this_year')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'this_year' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Tahun Ini
        </button>
    </div>

    <!-- Income Statement / Laporan Laba Bersih Card -->
    <div class="bg-navy-light/60 border border-gold/20 rounded-2xl p-5 sm:p-6 shadow-sm space-y-6">
        <div class="border-b border-gold/15 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-display font-semibold text-lg text-ivory">Ringkasan Laporan Pendapatan (Income Statement)</h3>
                <p class="text-xs text-ivory/50">Laporan kalkulasi pendapatan kotor, potongan biaya gateway, dan realisasi kas bersih.</p>
            </div>
            <span class="font-mono text-xs text-gold px-3 py-1 rounded bg-gold/10 border border-gold/20">
                Total Transaksi: {{ $metrics['paid_count'] }} Order
            </span>
        </div>

        <div class="space-y-3 font-sans text-sm">
            <!-- Row 1: Penjualan Produk -->
            <div class="flex justify-between items-center py-1 text-ivory/80">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-gold/50"></span>
                    <span>Penjualan Subtotal Produk</span>
                </span>
                <span class="font-mono font-semibold text-ivory">
                    Rp {{ number_format($metrics['product_sales'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Row 2: Ongkos Kirim Terkumpul -->
            <div class="flex justify-between items-center py-1 text-ivory/80">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-gold/50"></span>
                    <span>Ongkos Kirim Terkumpul (Biteship Logistics)</span>
                </span>
                <span class="font-mono font-semibold text-ivory">
                    Rp {{ number_format($metrics['shipping_collected'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Row 3: Diskon Diberikan -->
            @if($metrics['discounts_given'] > 0)
                <div class="flex justify-between items-center py-1 text-amber-400">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span>Potongan Diskon Promo Malega</span>
                    </span>
                    <span class="font-mono font-semibold">
                        -Rp {{ number_format($metrics['discounts_given'], 0, ',', '.') }}
                    </span>
                </div>
            @endif

            <!-- Subtotal Line: Gross Revenue -->
            <div class="flex justify-between items-center py-2.5 border-t border-b border-white/10 font-bold text-ivory bg-white/5 px-3 rounded-lg">
                <span class="uppercase tracking-wider text-xs">Total Penerimaan Kotor (Gross Revenue)</span>
                <span class="font-mono text-base text-ivory">
                    Rp {{ number_format($metrics['gross_sales'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Row 4: Biaya Gateway Duitku -->
            <div class="flex justify-between items-center py-2 text-red-400">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    <span>Potongan Biaya Admin Gateway Duitku (MDR & VA Fees)</span>
                </span>
                <span class="font-mono font-bold">
                    -Rp {{ number_format($metrics['total_gateway_fee'], 0, ',', '.') }}
                </span>
            </div>

            <!-- Final Grand Line: Net Realized Revenue -->
            <div class="flex justify-between items-center py-4 border-2 border-gold rounded-xl bg-gradient-to-r from-gold/15 via-gold/5 to-transparent px-4 mt-4">
                <div>
                    <span class="font-bold text-gold uppercase tracking-widest text-xs block font-mono">PENDAPATAN BERSIH TOKO (NET REALIZED REVENUE)</span>
                    <span class="text-[11px] text-ivory/60">Uang riil yang siap ditarik / masuk rekening kas utama</span>
                </div>
                <span class="font-mono font-black text-2xl text-gold gold-gradient-pure">
                    Rp {{ number_format($metrics['net_revenue'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Payment Channel Breakdown Table -->
    <div class="bg-navy-light/60 border border-gold/15 rounded-xl overflow-hidden shadow-sm space-y-3 p-5">
        <h3 class="font-display font-semibold text-base text-ivory">Rincian Performa Saluran Pembayaran (Payment Channels)</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-ivory/80">
                <thead class="bg-navy border-b border-gold/15 text-[11px] font-mono uppercase tracking-wider text-ivory/60">
                    <tr>
                        <th class="py-3 px-4">Channel Pembayaran</th>
                        <th class="py-3 px-4 text-center">Jumlah Transaksi</th>
                        <th class="py-3 px-4 text-right">Pemasukan Kotor</th>
                        <th class="py-3 px-4 text-right text-red-400">Total Admin Fee</th>
                        <th class="py-3 px-4 text-right text-gold">Kas Bersih (Net)</th>
                        <th class="py-3 px-4 text-right">Porsi Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-sans">
                    @forelse($channels as $c)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-ivory font-mono">{{ $c->payment_method_name ?: $c->payment_method }}</span>
                                <span class="text-[10px] text-ivory/40 font-mono ml-1">[{{ $c->payment_method }}]</span>
                            </td>

                            <td class="py-3 px-4 text-center font-mono text-ivory whitespace-nowrap">
                                {{ $c->tx_count }}
                            </td>

                            <td class="py-3 px-4 text-right font-mono font-semibold text-ivory whitespace-nowrap">
                                Rp {{ number_format($c->total_gross, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-4 text-right font-mono text-red-400 whitespace-nowrap">
                                -Rp {{ number_format($c->total_fee, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-4 text-right font-mono font-bold text-gold whitespace-nowrap">
                                Rp {{ number_format($c->total_net, 0, ',', '.') }}
                            </td>

                            <td class="py-3 px-4 text-right font-mono text-ivory/70 whitespace-nowrap">
                                {{ $metrics['gross_sales'] > 0 ? number_format(($c->total_gross / $metrics['gross_sales']) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-ivory/40 text-xs">
                                Belum ada data transaksi lunas pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

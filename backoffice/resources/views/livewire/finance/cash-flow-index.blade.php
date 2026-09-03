<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gold/15 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gold font-mono uppercase tracking-widest mb-1">
                <span>KEUANGAN & TREASURY</span>
                <span>•</span>
                <span>LEDGER ARUS KAS & POTONGAN GATEWAY</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-display font-semibold text-ivory tracking-wide flex items-center gap-2.5">
                <span>Arus Kas & Pendapatan Bersih</span>
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a 
                href="{{ route('finance.reports') }}" 
                class="px-3.5 py-2 rounded-lg bg-gold/15 hover:bg-gold text-gold hover:text-navy border border-gold/30 text-xs font-bold transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Lihat Laporan Keuangan</span>
            </a>
        </div>
    </div>

    <!-- Period Filter Pills -->
    <div class="flex flex-wrap items-center gap-2">
        <button 
            wire:click="$set('period', 'today')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'today' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Hari Ini
        </button>
        <button 
            wire:click="$set('period', '7days')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === '7days' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            7 Hari Terakhir
        </button>
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
            wire:click="$set('period', 'all')"
            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $period === 'all' ? 'bg-gold text-navy shadow-sm' : 'bg-navy-light/60 text-ivory/70 hover:bg-white/5 border border-gold/15' }}"
        >
            Semua Periode
        </button>
    </div>

    <!-- Summary KPI Financial Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Pendapatan Kotor (Gross) -->
        <div class="bg-navy-light/60 border border-gold/20 rounded-xl p-4 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-ivory/60">Pemasukan Kotor (Gross)</p>
                <span class="text-xs text-ivory/40 font-mono">{{ $summary['transaction_count'] }} Transaksi</span>
            </div>
            <p class="text-xl font-bold font-sans text-ivory mt-1">
                Rp {{ number_format($summary['gross_income'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-ivory/50 mt-1">Total tagihan lunas pelanggan</p>
        </div>

        <!-- Total Potongan Admin Fee Gateway -->
        <div class="bg-navy-light/60 border border-red-500/20 rounded-xl p-4 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-red-400">Potongan Fee Gateway</p>
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
            </div>
            <p class="text-xl font-bold font-sans text-red-400 mt-1">
                -Rp {{ number_format($summary['gateway_fee'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-red-400/70 mt-1">Biaya transaksi Duitku (VA/QRIS/CC)</p>
        </div>

        <!-- PENDAPATAN BERSIH REAL (NET REVENUE) -->
        <div class="bg-gradient-to-br from-gold/20 via-navy-light to-navy border-2 border-gold rounded-xl p-4 shadow-md relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-20 h-20 bg-gold/10 rounded-full blur-xl"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-gold font-bold">KAS BERSIH DITERIMA (NET)</p>
                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-gold text-navy">REAL</span>
            </div>
            <p class="text-2xl font-bold font-sans text-gold mt-1">
                Rp {{ number_format($summary['net_revenue'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-gold/80 mt-1 font-medium">Uang bersih masuk rekening toko</p>
        </div>

        <!-- Rata-rata Nilai Transaksi (AOV) -->
        <div class="bg-navy-light/60 border border-gold/20 rounded-xl p-4 shadow-sm">
            <p class="text-[10px] uppercase font-mono tracking-wider text-ivory/60">Rata-rata Pesanan (AOV)</p>
            <p class="text-xl font-bold font-sans text-ivory mt-1">
                Rp {{ number_format($summary['avg_order_value'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-ivory/50 mt-1">Per transaksi lunas</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-navy-light/60 border border-gold/15 rounded-xl p-3 flex items-center justify-between">
        <div class="relative w-full max-w-sm">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari transaksi arus kas..."
                class="w-full bg-navy border border-gold/20 rounded-lg pl-9 pr-3 py-1.5 text-xs text-ivory placeholder-ivory/40 focus:outline-none focus:border-gold"
            >
            <svg class="w-4 h-4 text-ivory/40 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
    </div>

    <!-- Cash Flow Table -->
    <div class="bg-navy-light/60 border border-gold/15 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-ivory/80">
                <thead class="bg-navy border-b border-gold/15 text-[11px] font-mono uppercase tracking-wider text-ivory/60">
                    <tr>
                        <th class="py-3 px-4">Waktu Lunas</th>
                        <th class="py-3 px-4">No. Invoice & Ref</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Saluran Bayar</th>
                        <th class="py-3 px-4 text-right">Uang Masuk (Gross)</th>
                        <th class="py-3 px-4 text-right text-red-400">Potongan Admin</th>
                        <th class="py-3 px-4 text-right text-gold">Kas Bersih (Net)</th>
                        <th class="py-3 px-4 text-center">Status Kas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-sans">
                    @forelse($entries as $e)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-ivory/60 whitespace-nowrap">
                                {{ $e->paid_at ? $e->paid_at->format('d M Y, H:i') : $e->created_at->format('d M Y, H:i') }} WIB
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-ivory font-mono text-xs">#{{ $e->merchant_order_id }}</span>
                                @if($e->reference)
                                    <div class="text-[10px] font-mono text-gold/80 truncate max-w-[140px]" title="{{ $e->reference }}">
                                        Ref: {{ $e->reference }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-medium text-ivory truncate max-w-[150px]">
                                    {{ $e->order?->address?->recipient_name ?? $e->order?->customer?->name ?? 'Pelanggan Malega' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-white/5 border border-white/10 text-ivory">
                                    {{ $e->payment_method_name ?? $e->payment_method }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-ivory whitespace-nowrap">
                                Rp {{ number_format($e->amount, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-red-400 whitespace-nowrap">
                                -Rp {{ number_format($e->admin_fee, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gold whitespace-nowrap">
                                Rp {{ number_format($e->net_amount, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    ✓ TERVERIFIKASI
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-ivory/40">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-xs">Belum ada data arus kas lunas pada periode ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="p-4 border-t border-gold/15 bg-navy">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>

<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gold/15 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gold font-mono uppercase tracking-widest mb-1">
                <span>KEUANGAN & TREASURY</span>
                <span>•</span>
                <span>REAL-TIME GATEWAY AUDIT</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-display font-semibold text-ivory tracking-wide flex items-center gap-2.5">
                <span>Logs Pembayaran & Gateway</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    DUITKU API v2
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a 
                href="{{ route('finance.cash-flow') }}" 
                class="px-3.5 py-2 rounded-lg bg-white/5 hover:bg-gold/10 text-ivory/80 hover:text-gold border border-gold/20 text-xs font-semibold transition-all flex items-center gap-2"
            >
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Buka Arus Kas</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Gross & Net Success -->
        <div class="bg-navy-light/60 backdrop-blur border border-gold/20 rounded-xl p-4 shadow-sm relative overflow-hidden">
            <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-gold/5 rounded-full blur-xl"></div>
            <p class="text-[10px] uppercase font-mono tracking-wider text-ivory/60">Kas Bersih Diterima (Net)</p>
            <p class="text-xl font-bold font-sans text-gold mt-1">
                Rp {{ number_format($kpi['net_success'], 0, ',', '.') }}
            </p>
            <div class="flex items-center justify-between text-[11px] text-ivory/50 mt-2 pt-2 border-t border-white/5">
                <span>Gross: Rp {{ number_format($kpi['gross_success'], 0, ',', '.') }}</span>
                <span class="text-red-400 font-mono">-Rp {{ number_format($kpi['fee_success'], 0, ',', '.') }} Fee</span>
            </div>
        </div>

        <!-- Card 2: Sukses (Paid) -->
        <div class="bg-navy-light/60 backdrop-blur border border-emerald-500/20 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-emerald-400">Pembayaran Berhasil</p>
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            </div>
            <p class="text-2xl font-bold font-sans text-ivory mt-1">{{ $kpi['success_count'] }} <span class="text-xs text-ivory/40 font-normal">Transaksi</span></p>
            <p class="text-[11px] text-emerald-400/80 mt-1">Terverifikasi Webhook / Duitku</p>
        </div>

        <!-- Card 3: Pending / Nyangkut -->
        <div class="bg-navy-light/60 backdrop-blur border border-amber-500/20 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-amber-400">Menunggu / Pending</p>
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            </div>
            <p class="text-2xl font-bold font-sans text-ivory mt-1">{{ $kpi['pending_count'] }} <span class="text-xs text-ivory/40 font-normal">Invoice</span></p>
            <p class="text-[11px] text-amber-400/80 mt-1">Belum ditransfer / VA aktif</p>
        </div>

        <!-- Card 4: Gagal / Expired -->
        <div class="bg-navy-light/60 backdrop-blur border border-red-500/20 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] uppercase font-mono tracking-wider text-red-400">Gagal / Kedaluwarsa</p>
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
            </div>
            <p class="text-2xl font-bold font-sans text-ivory mt-1">{{ $kpi['failed_count'] }} <span class="text-xs text-ivory/40 font-normal">Transaksi</span></p>
            <p class="text-[11px] text-red-400/80 mt-1">Expired batas 24 jam</p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-navy-light/60 border border-gold/15 rounded-xl p-3.5 flex flex-col md:flex-row gap-3 items-center justify-between">
        <div class="relative w-full md:w-80">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Cari No. Pesanan, Ref Duitku, VA, Pelanggan..."
                class="w-full bg-navy border border-gold/20 rounded-lg pl-9 pr-3 py-1.5 text-xs text-ivory placeholder-ivory/40 focus:outline-none focus:border-gold"
            >
            <svg class="w-4 h-4 text-ivory/40 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Filter Status -->
            <select wire:model.live="statusFilter" class="bg-navy border border-gold/20 rounded-lg px-3 py-1.5 text-xs text-ivory focus:outline-none focus:border-gold">
                <option value="all">Semua Status</option>
                <option value="success">Berhasil (Paid)</option>
                <option value="pending">Menunggu (Pending)</option>
                <option value="failed">Gagal / Expired</option>
            </select>

            <!-- Filter Metode -->
            <select wire:model.live="methodFilter" class="bg-navy border border-gold/20 rounded-lg px-3 py-1.5 text-xs text-ivory focus:outline-none focus:border-gold">
                <option value="all">Semua Channel</option>
                <option value="BC">BCA Virtual Account</option>
                <option value="M2">Mandiri VA</option>
                <option value="BR">BRI VA</option>
                <option value="I1">BNI VA</option>
                <option value="BT">Permata VA</option>
                <option value="SP">QRIS ShopeePay</option>
                <option value="QR">QRIS Standard</option>
                <option value="VC">Credit Card 3DS</option>
                <option value="COD">COD Tunai</option>
            </select>

            <!-- Filter Waktu -->
            <select wire:model.live="dateFilter" class="bg-navy border border-gold/20 rounded-lg px-3 py-1.5 text-xs text-ivory focus:outline-none focus:border-gold">
                <option value="all">Semua Waktu</option>
                <option value="today">Hari Ini</option>
                <option value="7days">7 Hari Terakhir</option>
                <option value="30days">30 Hari Terakhir</option>
            </select>
        </div>
    </div>

    <!-- Payments Log Table -->
    <div class="bg-navy-light/60 border border-gold/15 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-ivory/80">
                <thead class="bg-navy border-b border-gold/15 text-[11px] font-mono uppercase tracking-wider text-ivory/60">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">No. Pesanan & Ref Gateway</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Tagihan (Gross)</th>
                        <th class="py-3 px-4 text-right text-red-400">Admin Fee</th>
                        <th class="py-3 px-4 text-right text-gold">Kas Bersih (Net)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 font-sans">
                    @forelse($payments as $p)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-ivory/60 whitespace-nowrap">
                                {{ $p->created_at->format('d/m/Y') }}<br>
                                <span class="text-[10px] text-ivory/40">{{ $p->created_at->format('H:i:s') }} WIB</span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-ivory font-mono text-xs">#{{ $p->merchant_order_id }}</span>
                                @if($p->reference)
                                    <div class="text-[10px] font-mono text-gold/80 truncate max-w-[150px]" title="{{ $p->reference }}">
                                        Ref: {{ $p->reference }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-medium text-ivory truncate max-w-[140px]">
                                    {{ $p->order?->address?->recipient_name ?? $p->order?->customer?->name ?? 'Pelanggan Malega' }}
                                </div>
                                <div class="text-[10px] text-ivory/50 font-mono">
                                    {{ $p->order?->address?->phone ?? $p->order?->customer?->phone ?? '-' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-white/5 border border-white/10 text-ivory">
                                    {{ $p->payment_method_name ?? $p->payment_method ?? 'Duitku' }}
                                </span>
                                @if($p->va_number)
                                    <div class="text-[10px] font-mono text-ivory/50 mt-0.5">
                                        VA: {{ $p->va_number }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-ivory whitespace-nowrap">
                                Rp {{ number_format($p->amount, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-red-400 whitespace-nowrap">
                                -Rp {{ number_format($p->admin_fee, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gold whitespace-nowrap">
                                Rp {{ number_format($p->net_amount, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($p->status === 'success')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        ✓ LUNAS (PAID)
                                    </span>
                                @elseif($p->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30 animate-pulse">
                                        ⏳ MENUNGGU
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/15 text-red-400 border border-red-500/30">
                                        ✕ GAGAL/EXPIRED
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center whitespace-nowrap space-x-1">
                                <!-- Tombol Live Sync Duitku -->
                                @if($p->status === 'pending')
                                    <button 
                                        wire:click="syncLiveStatus({{ $p->id }})"
                                        wire:loading.attr="disabled"
                                        title="Sinkronisasi status terkini dengan Duitku"
                                        class="p-1.5 rounded bg-gold/15 hover:bg-gold text-gold hover:text-navy transition-colors inline-flex items-center"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                @endif

                                <!-- Tombol Detail Audit Modal -->
                                <button 
                                    wire:click="viewDetails({{ $p->id }})"
                                    class="p-1.5 rounded bg-white/5 hover:bg-white/15 text-ivory/80 hover:text-ivory transition-colors inline-flex items-center"
                                    title="Lihat Detail Transaksi & Webhook Payload"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-ivory/40">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-xs">Tidak ada data transaksi pembayaran yang sesuai filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-gold/15 bg-navy">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Transaction Detail Modal -->
    @if($showDetailModal && $selectedPayment)
        <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-navy border border-gold/30 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 text-ivory">
                <div class="flex items-center justify-between border-b border-gold/15 pb-3">
                    <div>
                        <span class="text-[10px] font-mono text-gold uppercase tracking-widest block font-bold">DETAIL LOG TRANSAKSI</span>
                        <h3 class="font-bold text-base text-ivory">Pesanan #{{ $selectedPayment->merchant_order_id }}</h3>
                    </div>
                    <button wire:click="closeDetails" class="text-ivory/50 hover:text-ivory text-sm p-1">✕</button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Status Pembayaran</span>
                        <span class="font-bold font-mono text-{{ $selectedPayment->status === 'success' ? 'emerald-400' : ($selectedPayment->status === 'pending' ? 'amber-400' : 'red-400') }}">
                            {{ strtoupper($selectedPayment->status) }}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Duitku Reference</span>
                        <span class="font-mono text-[11px] text-gold truncate block" title="{{ $selectedPayment->reference ?? '-' }}">
                            {{ $selectedPayment->reference ?? '-' }}
                        </span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Channel Metode</span>
                        <span class="font-semibold text-ivory block">{{ $selectedPayment->payment_method_name ?? $selectedPayment->payment_method }}</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Nominal Tagihan</span>
                        <span class="font-mono font-bold text-ivory block">Rp {{ number_format($selectedPayment->amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Potongan Admin Fee</span>
                        <span class="font-mono font-bold text-red-400 block">-Rp {{ number_format($selectedPayment->admin_fee, 0, ',', '.') }}</span>
                    </div>

                    <div class="p-2.5 rounded-lg bg-navy-light/60 border border-white/5">
                        <span class="text-[10px] text-ivory/50 uppercase block">Kas Bersih Toko</span>
                        <span class="font-mono font-bold text-gold block">Rp {{ number_format($selectedPayment->net_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($selectedPayment->payment_url)
                    <div class="p-3 rounded-xl bg-navy-light/80 border border-gold/20 text-xs space-y-1">
                        <span class="text-[10px] text-gold uppercase font-mono block font-bold">URL Gateway Duitku:</span>
                        <a href="{{ $selectedPayment->payment_url }}" target="_blank" class="text-gold hover:underline font-mono text-[11px] break-all">
                            {{ $selectedPayment->payment_url }} ↗
                        </a>
                    </div>
                @endif

                <!-- Audit Webhook Payload JSON -->
                <div class="space-y-1.5">
                    <span class="text-[10px] font-mono text-ivory/60 uppercase block">Payload Notifikasi Webhook (Audit Trail):</span>
                    <pre class="bg-black/50 p-3 rounded-lg text-[10px] font-mono text-emerald-400 overflow-x-auto max-h-40 border border-white/10">{{ json_encode($selectedPayment->callback_payload ?: $selectedPayment->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gold/15">
                    <button wire:click="closeDetails" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-semibold text-ivory">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

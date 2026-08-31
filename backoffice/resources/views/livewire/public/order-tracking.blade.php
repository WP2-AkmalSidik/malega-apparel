<div class="space-y-8">
    <!-- Hero Header & Search Section -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] text-xs font-mono font-semibold">
            <span class="w-2 h-2 rounded-full bg-[#CBAC70] animate-ping"></span>
            <span>Live Courier Tracking System &bull; Biteship Engine</span>
        </div>
        <h1 class="font-display text-3xl sm:text-4xl text-slate-100 tracking-tight font-bold">
            Lacak Perjalanan Paket Anda
        </h1>
        <p class="text-slate-400 text-sm">
            Pantau status pesanan busana Malega Apparel secara langsung, mulai dari pemrosesan di gudang pusat hingga paket tiba di depan pintu Anda.
        </p>

        <!-- Search Input Box -->
        <form wire:submit="search" class="pt-2">
            <div class="relative max-w-xl mx-auto">
                <div class="relative flex items-center rounded-2xl bg-[#0B132B] border border-slate-700/80 shadow-2xl shadow-black/50 overflow-hidden focus-within:border-[#CBAC70] focus-within:ring-2 focus-within:ring-[#CBAC70]/20 transition-all">
                    <div class="pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model="searchQuery"
                        placeholder="Ketik Nomor Pesanan (MLG-...) atau No. Resi (WYB-...)..."
                        class="w-full bg-transparent border-0 py-3.5 pl-3 pr-28 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none font-mono"
                    >
                    <div class="absolute right-2 top-1/2 -translate-y-1/2">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/20 transition-all cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="search">Lacak</span>
                            <span wire:loading.inline-flex wire:target="search" class="items-center gap-1">
                                <svg class="animate-spin h-3.5 w-3.5 text-[#0B132B]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                @error('searchQuery')
                    <p class="text-rose-400 text-xs mt-1.5 text-left pl-2">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    <!-- Active Search Results Section -->
    @if($order)
        <div class="space-y-6 animate-fade-in">
            <!-- 1. Top Order Summary Banner -->
            <div class="relative rounded-3xl bg-[#0B132B] border border-[#CBAC70]/30 p-5 sm:p-6 shadow-2xl shadow-black/60 overflow-hidden">
                <!-- Top Gold Stitch Accent -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-80"></div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="font-mono font-bold text-xl sm:text-2xl text-[#CBAC70] tracking-tight">
                                {{ $order->order_number }}
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->order_status->badgeClasses() }}">
                                {{ $order->order_status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                                {{ $order->payment_status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                                ⏱ Estimasi: {{ $this->estimatedDelivery }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">
                            Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB &bull; Pemesan: <span class="text-slate-200 font-semibold">{{ $order->customer?->name ?? $order->address?->recipient_name }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="refreshStatus"
                            wire:loading.attr="disabled"
                            class="px-3.5 py-2 rounded-xl border border-slate-700 bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50"
                        >
                            <svg wire:loading.remove wire:target="refreshStatus" class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg wire:loading.inline-flex wire:target="refreshStatus" class="animate-spin w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Perbarui Live Status</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Interactive 5-Stage Stepper Progress Bar -->
                @if($order->order_status->value !== 'cancelled')
                    <div class="mt-8 pt-6 border-t border-slate-800/80">
                        <div class="grid grid-cols-5 gap-2 relative">
                            <!-- Stage 1 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 1 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    1
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 1 ? 'text-slate-100' : 'text-slate-500' }}">Dipesan</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Pesanan Masuk</p>
                                </div>
                            </div>

                            <!-- Stage 2 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 2 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    2
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 2 ? 'text-slate-100' : 'text-slate-500' }}">Diproses</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Packing Busana</p>
                                </div>
                            </div>

                            <!-- Stage 3 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 3 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    3
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 3 ? 'text-slate-100' : 'text-slate-500' }}">Resi Terbit</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Menunggu Pickup</p>
                                </div>
                            </div>

                            <!-- Stage 4 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 4 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    4
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 4 ? 'text-slate-100' : 'text-slate-500' }}">Dikirim</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Dalam Perjalanan</p>
                                </div>
                            </div>

                            <!-- Stage 5 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 5 ? 'bg-emerald-500 text-white shadow-[0_0_15px_rgba(16,185,129,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    ✓
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 5 ? 'text-emerald-400' : 'text-slate-500' }}">Terkirim</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Paket Diterima</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-center">
                        <p class="text-sm font-bold text-rose-400">Pesanan ini telah Dibatalkan</p>
                        <p class="text-xs text-slate-400 mt-1">Silakan hubungi customer service kami jika Anda membutuhkan bantuan lebih lanjut.</p>
                    </div>
                @endif
            </div>

            <!-- 3. Visual Interactive Route Path Map Graphic -->
            <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 shadow-xl overflow-hidden relative">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider flex items-center gap-1.5">
                        <span>🗺️</span>
                        <span>Rute & Hub Logistik Ekspedisi</span>
                    </p>
                    <span class="text-[11px] font-mono text-slate-400">
                        {{ $order->shipment?->courier_company ?? $order->address?->courier_name ?? 'Kurir' }} &bull; {{ $order->shipment?->courier_service_name ?? 'REG' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-[#070C1A] p-4 rounded-2xl border border-slate-800/80">
                    <!-- Origin Node -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase text-amber-400 font-bold">Asal Pengiriman</span>
                            <p class="text-xs font-bold text-slate-200 truncate">Gudang Pusat Malega</p>
                            <p class="text-[10px] text-slate-400 truncate">Jakarta Pusat, DKI Jakarta</p>
                        </div>
                    </div>

                    <!-- Transit / Courier Node -->
                    <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l md:border-r border-slate-800 pt-3 md:pt-0 md:px-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase text-sky-400 font-bold">Hub Transit Ekspedisi</span>
                            <p class="text-xs font-bold text-slate-200 truncate">
                                {{ $order->shipment?->courier_company ?? 'Kurir' }} Sortir Gateway
                            </p>
                            <p class="text-[10px] text-slate-400 truncate">
                                Status: {{ $order->shipment?->status_label ?? 'Confirmed' }}
                            </p>
                        </div>
                    </div>

                    <!-- Destination Node -->
                    <div class="flex items-center gap-3 border-t md:border-t-0 border-slate-800 pt-3 md:pt-0">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] font-mono uppercase text-emerald-400 font-bold">Tujuan Penerima</span>
                            <p class="text-xs font-bold text-slate-200 truncate">{{ $order->address?->recipient_name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ $order->address?->city }}, {{ $order->address?->postal_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Interactive Tabs Selection -->
            <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
                <button
                    type="button"
                    wire:click="$set('activeTab', 'timeline')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'timeline' ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow-lg shadow-[#CBAC70]/20' : 'bg-slate-900/60 text-slate-400 hover:text-slate-200 border border-slate-800' }}"
                >
                    <span>🚚 Riwayat Perjalanan Kurir (Live Timeline)</span>
                </button>
                <button
                    type="button"
                    wire:click="$set('activeTab', 'package')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'package' ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow-lg shadow-[#CBAC70]/20' : 'bg-slate-900/60 text-slate-400 hover:text-slate-200 border border-slate-800' }}"
                >
                    <span>📦 Spesifikasi Paket & Busana</span>
                </button>
                <button
                    type="button"
                    wire:click="$set('activeTab', 'invoice')"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $activeTab === 'invoice' ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow-lg shadow-[#CBAC70]/20' : 'bg-slate-900/60 text-slate-400 hover:text-slate-200 border border-slate-800' }}"
                >
                    <span>💳 Faktur & Pembayaran</span>
                </button>
            </div>

            <!-- 5. Tab Content: TIMELINE -->
            @if($activeTab === 'timeline')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <!-- Left 2 Columns: Full Rich Milestones Timeline -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Digital Barcode & Waybill Card -->
                        <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-slate-100 text-sm">
                                            {{ $order->shipment?->courier_company ?? $order->address?->courier_name ?? 'Kurir Ekspedisi' }}
                                        </p>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-[#CBAC70] border border-slate-700">
                                            {{ $order->shipment?->courier_service_name ?? 'REG' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Status: <span class="text-slate-200 font-semibold">{{ $order->shipment?->status_label ?? $order->fulfillment_status->label() }}</span>
                                    </p>
                                </div>

                                <!-- Waybill ID with Copy & Actions -->
                                @if($order->shipment?->waybill_id || $order->address?->tracking_number)
                                    @php
                                        $waybill = $order->shipment?->waybill_id ?? $order->address?->tracking_number;
                                    @endphp
                                    <div class="flex items-center gap-2 bg-[#070C1A] border border-slate-700/80 px-3.5 py-2 rounded-2xl" x-data="{ copied: false }">
                                        <div>
                                            <p class="text-[9px] uppercase font-mono text-slate-500 font-bold">Nomor Resi (AWB)</p>
                                            <p class="font-mono font-bold text-sm text-sky-400 select-all">{{ $waybill }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            x-on:click="navigator.clipboard.writeText('{{ $waybill }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer"
                                            title="Salin Nomor Resi"
                                        >
                                            <span x-show="!copied">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                            <span x-show="copied" x-cloak class="text-emerald-400 text-xs font-bold font-mono">
                                                ✓ Tersalin
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Decorative SVG 1D Barcode Graphic -->
                            @if($order->shipment?->waybill_id)
                                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-0.5 h-6 bg-slate-800 px-2 py-1 rounded font-mono text-[9px] text-slate-300">
                                            <span>||| | |||| | || ||| || ||| |</span>
                                        </div>
                                        <span class="font-mono text-[10px] text-slate-400">Barcode Terotentikasi Ekspedisi</span>
                                    </div>
                                    <button
                                        type="button"
                                        onclick="window.print()"
                                        class="text-[11px] text-[#CBAC70] hover:text-[#DFB67A] font-semibold flex items-center gap-1 cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        <span>Cetak Resi</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Vertical Milestone Timeline Feed -->
                        <div class="p-6 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="font-mono text-xs text-[#CBAC70] uppercase font-bold tracking-wider flex items-center gap-2">
                                    <span>⏱️</span>
                                    <span>Riwayat Kronologis Perjalanan Paket</span>
                                </h3>
                                <span class="text-[10px] text-slate-500 font-mono">Real-Time Event Stream</span>
                            </div>

                            <div class="relative pl-8 space-y-6 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                                @foreach($this->comprehensiveMilestones as $index => $event)
                                    <div class="relative group">
                                        <!-- Indicator Beacon Dot -->
                                        @if($event['is_active'])
                                            <div class="absolute -left-8 top-1.5 w-5 h-5 rounded-full bg-sky-500 flex items-center justify-center shadow-[0_0_15px_rgba(56,189,248,1)]">
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                            </div>
                                        @else
                                            <div class="absolute -left-7 top-2 w-3.5 h-3.5 rounded-full bg-slate-900 border-2 border-slate-700"></div>
                                        @endif

                                        <div class="p-4 rounded-2xl {{ $event['is_active'] ? 'bg-sky-950/30 border border-sky-500/40 shadow-lg shadow-sky-500/5' : 'bg-[#070C1A] border border-slate-800/80' }} space-y-2">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-xs uppercase tracking-wider {{ $event['is_active'] ? 'text-sky-400' : 'text-slate-200' }}">
                                                        {{ $event['title'] }}
                                                    </span>
                                                    @if($event['is_active'])
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-sky-500/20 text-sky-300 border border-sky-500/30">
                                                            STATUS TERKINI
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="font-mono text-[11px] text-slate-400">
                                                    {{ $event['timestamp'] }} WIB
                                                </span>
                                            </div>

                                            <p class="text-xs text-slate-300 leading-relaxed">{{ $event['note'] }}</p>

                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-1 border-t border-slate-800/50">
                                                <svg class="w-3 h-3 text-[#CBAC70] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="font-medium text-slate-300">{{ $event['location'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Destination, Package Specs & Support -->
                    <div class="space-y-6">
                        <!-- Delivery Destination Address -->
                        <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-3">
                            <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Tujuan Pengiriman</p>
                            <div class="text-xs space-y-1">
                                <p class="text-slate-100 font-bold text-sm">{{ $order->address?->recipient_name }}</p>
                                <p class="text-slate-400 font-mono">{{ $order->address?->phone }}</p>
                                <p class="text-slate-300 pt-1 leading-relaxed">{{ $order->address?->address_line1 }}</p>
                                @if($order->address?->address_line2)
                                    <p class="text-slate-400">{{ $order->address?->address_line2 }}</p>
                                @endif
                                <p class="text-slate-400">{{ $order->address?->city }}, {{ $order->address?->province }} {{ $order->address?->postal_code }}</p>
                            </div>
                        </div>

                        <!-- Quick Package Specs -->
                        <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-3">
                            <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Spesifikasi Kemasan</p>
                            <div class="text-xs space-y-2">
                                <div class="flex justify-between text-slate-400">
                                    <span>Total Kuantitas</span>
                                    <span class="font-bold text-slate-200">{{ $order->items->sum('quantity') }} Item Busana</span>
                                </div>
                                <div class="flex justify-between text-slate-400">
                                    <span>Estimasi Berat</span>
                                    <span class="font-mono text-slate-200">~{{ $order->items->sum('quantity') * 350 }} gram</span>
                                </div>
                                <div class="flex justify-between text-slate-400">
                                    <span>Proteksi Asuransi</span>
                                    <span class="text-emerald-400 font-semibold">🛡️ Aktif (Asuransi Pengiriman)</span>
                                </div>
                                <div class="flex justify-between text-slate-400">
                                    <span>Kemasan Eksklusif</span>
                                    <span class="text-[#CBAC70]">Luxury Box + Dust Bag</span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Support Card -->
                        <div class="p-5 rounded-3xl bg-gradient-to-br from-emerald-950/30 to-[#0B132B] border border-emerald-500/30 space-y-3">
                            <div class="flex items-center gap-2 text-emerald-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="font-bold text-xs uppercase tracking-wider">Butuh Bantuan Pengiriman?</p>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Jika ada perubahan jadwal penjemputan, salah alamat, atau pertanyaan seputar busana Malega, Customer Concierge siap membantu.
                            </p>
                            <a
                                href="https://wa.me/6281234567890?text=Halo%20CS%20Malega%20Apparel,%20saya%20ingin%20menanyakan%20status%20pesanan%20nomor%20{{ $order->order_number }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2"
                            >
                                <span>Hubungi Concierge WhatsApp</span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 6. Tab Content: PACKAGE & GARMENTS -->
            @if($activeTab === 'package')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <div class="lg:col-span-2 space-y-4">
                        <div class="p-6 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-4">
                            <h3 class="font-mono text-xs text-[#CBAC70] uppercase font-bold tracking-wider">
                                Daftar Busana Dalam Paket Pengiriman (Snapshot ADR-006)
                            </h3>

                            <div class="divide-y divide-slate-800/80">
                                @foreach($order->items as $item)
                                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-12 h-12 rounded-2xl bg-[#070C1A] border border-slate-700 flex items-center justify-center text-[#CBAC70] font-bold text-base">
                                                👕
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-100 text-sm">{{ $item->product_name }}</p>
                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    Varian: <span class="text-slate-200 font-medium">{{ $item->variant_title }}</span> &bull; SKU: <span class="font-mono text-[#CBAC70]">{{ $item->sku }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between sm:justify-end gap-6 text-xs">
                                            <div class="text-right">
                                                <p class="text-slate-500 text-[10px] uppercase">Jumlah</p>
                                                <p class="font-bold text-slate-200 font-mono">{{ $item->quantity }} Pcs</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-slate-500 text-[10px] uppercase">Subtotal</p>
                                                <p class="font-bold text-slate-100 font-mono">{{ $item->formatted_subtotal }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Quality Guarantee Card -->
                        <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-3">
                            <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Standar Kualitas & QC</p>
                            <div class="text-xs text-slate-300 space-y-2 leading-relaxed">
                                <p>✓ 100% Produk Asli Malega Apparel Signature</p>
                                <p>✓ Melewati Quality Control Jahitan & Kancing Presisi</p>
                                <p>✓ Garansi Penukaran Ukuran dalam 7 Hari sejak paket diterima</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 7. Tab Content: INVOICE & FINANCIALS -->
            @if($activeTab === 'invoice')
                <div class="max-w-2xl mx-auto p-6 sm:p-8 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-6 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div>
                            <p class="font-display font-bold text-lg text-slate-100">Faktur Pesanan #{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Tanggal Transaksi: {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>

                    <!-- Financial Summary Breakdown -->
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Produk</span>
                            <span class="font-mono text-slate-200">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Ongkos Kirim ({{ $order->shipment?->courier_company ?? 'Kurir' }})</span>
                            <span class="font-mono text-slate-200">Rp {{ number_format($order->shipping_total, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_total > 0)
                            <div class="flex justify-between text-rose-400">
                                <span>Potongan Diskon Promo</span>
                                <span class="font-mono">-Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-sm">
                            <span class="font-bold text-slate-100">Total Pembayaran</span>
                            <span class="font-mono font-bold text-[#CBAC70] text-base">{{ $order->formatted_grand_total }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end">
                        <button
                            type="button"
                            onclick="window.print()"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs transition-colors flex items-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-[#CBAC70]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Faktur Pesanan</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @elseif($hasSearched)
        <!-- Zero Search State -->
        <div class="max-w-md mx-auto p-8 rounded-3xl bg-[#0B132B] border border-slate-800 text-center space-y-3 animate-fade-in">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-100 text-base">Pesanan Tidak Ditemukan</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                Kami tidak dapat menemukan pesanan dengan kata kunci <span class="font-mono text-slate-200 font-bold">"{{ $searchQuery }}"</span>. Pastikan format nomor pesanan (contoh: <span class="font-mono text-[#CBAC70]">MLG-20260831-0710</span>) atau nomor resi kurir sudah benar.
            </p>
        </div>
    @endif
</div>

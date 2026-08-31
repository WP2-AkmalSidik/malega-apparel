<div class="space-y-8">
    <!-- Hero Header & Search Section -->
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#CBAC70]/10 border border-[#CBAC70]/30 text-[#CBAC70] text-xs font-mono font-semibold">
            <span class="w-2 h-2 rounded-full bg-[#CBAC70] animate-ping"></span>
            <span>Live Courier Tracking System</span>
        </div>
        <h1 class="font-display text-3xl sm:text-4xl text-slate-100 tracking-tight font-bold">
            Lacak Perjalanan Paket Anda
        </h1>
        <p class="text-slate-400 text-sm">
            Pantau status pesanan pakaian Malega Apparel secara langsung, mulai dari gudang hingga sampai ke tangan Anda.
        </p>

        <!-- Search Input Box -->
        <form wire:submit="search" class="pt-3">
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
                        placeholder="Ketik Nomor Pesanan (MLG-...) atau No. Resi (AWB)..."
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
                        <div class="flex items-center gap-3">
                            <h2 class="font-mono font-bold text-xl sm:text-2xl text-[#CBAC70] tracking-tight">
                                {{ $order->order_number }}
                            </h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->order_status->badgeClasses() }}">
                                {{ $order->order_status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->payment_status->badgeClasses() }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
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
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Pesanan Diterima</p>
                                </div>
                            </div>

                            <!-- Stage 2 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 2 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    2
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 2 ? 'text-slate-100' : 'text-slate-500' }}">Diproses</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Packing Barang</p>
                                </div>
                            </div>

                            <!-- Stage 3 -->
                            <div class="text-center space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $this->progressStep >= 3 ? 'bg-[#CBAC70] text-[#0B132B] shadow-[0_0_15px_rgba(203,172,112,0.6)]' : 'bg-slate-800 text-slate-500' }}">
                                    3
                                </div>
                                <div>
                                    <p class="text-xs font-bold {{ $this->progressStep >= 3 ? 'text-slate-100' : 'text-slate-500' }}">Resi Terbit</p>
                                    <p class="text-[10px] text-slate-400 hidden sm:block">Menunggu Kurir</p>
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

            <!-- 2-Column Detailed View -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Left 2 Columns: Live Courier Timeline & Logistics Card -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Courier Details Card -->
                    <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-slate-100 text-sm">
                                            {{ $order->shipment?->courier_company ?? $order->address?->courier_name ?? 'Kurir Ekspedisi' }}
                                        </p>
                                        @if($order->shipment?->courier_service_name)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                                {{ $order->shipment->courier_service_name }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Status: <span class="text-slate-200 font-semibold">{{ $order->shipment?->status_label ?? $order->fulfillment_status->label() }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Waybill ID with Copy Button -->
                            @if($order->shipment?->waybill_id || $order->address?->tracking_number)
                                @php
                                    $waybill = $order->shipment?->waybill_id ?? $order->address?->tracking_number;
                                @endphp
                                <div class="flex items-center gap-2 bg-[#070C1A] border border-slate-700/80 px-3 py-2 rounded-xl" x-data="{ copied: false }">
                                    <div>
                                        <p class="text-[9px] uppercase font-mono text-slate-500 font-bold">Nomor Resi (AWB)</p>
                                        <p class="font-mono font-bold text-sm text-sky-400 select-all">{{ $waybill }}</p>
                                    </div>
                                    <button
                                        type="button"
                                        x-on:click="navigator.clipboard.writeText('{{ $waybill }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer"
                                        title="Salin Nomor Resi"
                                    >
                                        <span x-show="!copied">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </span>
                                        <span x-show="copied" x-cloak class="text-emerald-400 text-xs font-bold font-mono">
                                            ✓
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Vertical Milestone Timeline -->
                    <div class="p-6 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-5">
                        <div class="flex items-center justify-between">
                            <h3 class="font-mono text-xs text-[#CBAC70] uppercase font-bold tracking-wider">
                                Riwayat Perjalanan Paket Real-Time
                            </h3>
                            <span class="text-[10px] text-slate-500 font-mono">Biteship Logistics Live Stream</span>
                        </div>

                        @php
                            $milestones = $liveTrackingData['history'] ?? $order->shipment?->tracking_history ?? [];
                        @endphp

                        @if(!empty($milestones))
                            <div class="relative pl-8 space-y-6 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                                @foreach(array_reverse($milestones) as $index => $event)
                                    <div class="relative group">
                                        <!-- Animated Beacon Dot -->
                                        @if($index === 0)
                                            <div class="absolute -left-8 top-1 w-5 h-5 rounded-full bg-sky-500 flex items-center justify-center shadow-[0_0_12px_rgba(56,189,248,0.9)]">
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                            </div>
                                        @else
                                            <div class="absolute -left-7 top-1.5 w-3.5 h-3.5 rounded-full bg-slate-900 border-2 border-slate-700"></div>
                                        @endif

                                        <div class="p-4 rounded-2xl {{ $index === 0 ? 'bg-sky-950/30 border border-sky-500/40 shadow-lg shadow-sky-500/5' : 'bg-[#070C1A] border border-slate-800/80' }} space-y-1.5">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <span class="font-bold text-xs uppercase tracking-wider {{ $index === 0 ? 'text-sky-400' : 'text-slate-200' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $event['status'] ?? 'Pembaruan Logistik')) }}
                                                </span>
                                                <span class="font-mono text-[11px] text-slate-500">
                                                    {{ !empty($event['updated_at']) ? \Carbon\Carbon::parse($event['updated_at'])->format('d M Y, H:i') : '-' }} WIB
                                                </span>
                                            </div>
                                            <p class="text-xs text-slate-300 leading-relaxed">{{ $event['note'] ?? '-' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center rounded-2xl bg-[#070C1A] border border-slate-800/80 text-slate-400 space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-full bg-slate-800/60 flex items-center justify-center text-slate-500">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-200">Menunggu Pemindaian Barcode Kurir</p>
                                <p class="text-[11px] text-slate-500">Status perjalanan paket akan otomatis terupdate saat kurir melakukan scanning paket di gudang sortir.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Shipping Destination & Order Snapshot -->
                <div class="space-y-6">
                    <!-- Delivery Destination Address -->
                    <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-3">
                        <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Tujuan Pengiriman</p>
                        <div class="text-xs space-y-1">
                            <p class="text-slate-100 font-bold text-sm">{{ $order->address?->recipient_name }}</p>
                            <p class="text-slate-400 font-mono">{{ $order->address?->phone }}</p>
                            <p class="text-slate-300 pt-1 leading-relaxed">{{ $order->address?->address_line1 }}</p>
                            <p class="text-slate-400">{{ $order->address?->city }}, {{ $order->address?->province }} {{ $order->address?->postal_code }}</p>
                        </div>
                    </div>

                    <!-- Order Items Snapshot -->
                    <div class="p-5 rounded-3xl bg-[#0B132B] border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Item Pesanan</p>
                            <span class="text-slate-500 text-[10px] font-mono">{{ $order->items->sum('quantity') }} Item</span>
                        </div>
                        <div class="divide-y divide-slate-800/60 max-h-60 overflow-y-auto">
                            @foreach($order->items as $it)
                                <div class="py-2.5 flex items-start justify-between gap-3 text-xs">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-slate-200 truncate">{{ $it->product_name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $it->variant_title }} &bull; Qty: {{ $it->quantity }}</p>
                                    </div>
                                    <span class="font-mono text-slate-300 text-right">{{ $it->formatted_subtotal }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-xs">
                            <span class="text-slate-400">Total Pembayaran</span>
                            <span class="font-mono font-bold text-sm text-[#CBAC70]">{{ $order->formatted_grand_total }}</span>
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
                            Jika kurir terlambat atau ada kendala alamat, tim Customer Service Malega Apparel siap membantu Anda.
                        </p>
                        <a
                            href="https://wa.me/6281234567890?text=Halo%20CS%20Malega%20Apparel,%20saya%20ingin%20menanyakan%20status%20pesanan%20nomor%20{{ $order->order_number }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2"
                        >
                            <span>Hubungi CS via WhatsApp</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
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
                Kami tidak dapat menemukan pesanan dengan kata kunci <span class="font-mono text-slate-200 font-bold">"{{ $searchQuery }}"</span>. Pastikan format nomor pesanan (contoh: <span class="font-mono text-[#CBAC70]">MLG-20260831-0001</span>) atau nomor resi kurir sudah benar.
            </p>
        </div>
    @endif
</div>

<div class="space-y-6">
    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Pesanan & Transaksi"
        subtitle="Kelola seluruh siklus pesanan, status pembayaran, dan pengiriman kurir secara real-time"
        :count="$totalOrdersCount"
    >
        <!-- Primary Header Action -->
        <x-slot:actions>
            <button
                type="button"
                wire:click="openCreateModal"
                class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-[11px] shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Buat Pesanan</span>
            </button>
        </x-slot:actions>

        <!-- Filter & Search Toolbar (Full-Width Single Row) -->
        <x-slot:controls>
            <!-- Search Bar -->
            <div class="relative w-full sm:w-60 lg:w-72">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nomor pesanan, pelanggan..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 pl-8 pr-3 text-[11px] text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
            </div>

            <!-- Filters Group -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <!-- Status Filter -->
                <select
                    wire:model.live="statusFilter"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
                >
                    <option value="all">Semua Status ({{ $totalOrdersCount }})</option>
                    <option value="pending">Menunggu ({{ $pendingCount }})</option>
                    <option value="processing">Diproses ({{ $processingCount }})</option>
                    <option value="completed">Selesai ({{ $completedCount }})</option>
                    <option value="cancelled">Batal ({{ $cancelledCount }})</option>
                </select>

                <!-- Payment Filter -->
                <select
                    wire:model.live="paymentFilter"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
                >
                    <option value="all">Semua Pembayaran</option>
                    <option value="unpaid">Belum Lunas</option>
                    <option value="paid">Lunas</option>
                </select>

                <!-- Sort By Dropdown -->
                <select
                    wire:model.live="sortBy"
                    class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
                >
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="highest_total">Nominal Tertinggi</option>
                    <option value="lowest_total">Nominal Terendah</option>
                </select>
            </div>
        </x-slot:controls>

        <!-- Orders Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[11px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-4 py-3 font-medium">No. Pesanan</th>
                    <th class="px-4 py-3 font-medium">Customer</th>
                    <th class="px-4 py-3 font-medium text-right">Total Transaksi</th>
                    <th class="px-4 py-3 font-medium text-center">Status Pesanan</th>
                    <th class="px-4 py-3 font-medium text-center">Pembayaran</th>
                    <th class="px-4 py-3 font-medium text-center hidden md:table-cell">Pengiriman</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($orders as $order)
                    <tr wire:key="order-row-{{ $order->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Order Number & Date -->
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                wire:click="openDetailModal({{ $order->id }})"
                                class="text-left group/link cursor-pointer"
                            >
                                <p class="font-mono font-bold text-xs text-[#CBAC70] group-hover/link:text-[#DFB67A] transition-colors">
                                    {{ $order->order_number }}
                                </p>
                                <p class="text-slate-500 text-[10px] font-mono mt-0.5">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </p>
                            </button>
                        </td>

                        <!-- Customer Details -->
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-200 text-xs">{{ $order->customer?->name ?? 'Guest Buyer' }}</p>
                            <p class="text-slate-400 text-[11px] mt-0.5">{{ $order->customer?->phone ?? '-' }}</p>
                            <p class="text-slate-500 text-[10px]">{{ $order->address?->city ?? '' }}</p>
                        </td>

                        <!-- Grand Total & Items Count -->
                        <td class="px-4 py-3 text-right">
                            <p class="font-mono font-bold text-slate-100 text-sm">
                                {{ $order->formatted_grand_total }}
                            </p>
                            <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                                {{ $order->items->sum('quantity') }} item produk
                            </p>
                        </td>

                        <!-- Order Status Badge -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $order->order_status->badgeClasses() }}">
                                {{ $order->order_status->label() }}
                            </span>
                        </td>

                        <!-- Payment Status Badge -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $order->payment_status->badgeClasses() }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>

                        <!-- Fulfillment Status & Tracking -->
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $order->fulfillment_status->badgeClasses() }}">
                                {{ $order->fulfillment_status->label() }}
                            </span>
                            @if($order->address?->tracking_number)
                                <p class="text-[10px] font-mono text-sky-400 mt-1 truncate max-w-[130px] mx-auto">
                                    {{ $order->address->courier_name }}: {{ $order->address->tracking_number }}
                                </p>
                            @endif
                        </td>

                        <!-- Action Button -->
                        <td class="px-4 py-3 text-right">
                            <button
                                type="button"
                                wire:click="openDetailModal({{ $order->id }})"
                                class="px-3 py-1.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer inline-flex items-center gap-1.5"
                            >
                                <span>Detail</span>
                                <svg class="w-3.5 h-3.5 text-[#CBAC70]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.881-4.804 2.231-7.454a1.125 1.125 0 00-1.12-1.296H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-200">Belum ada pesanan ditemukan</p>
                                <p class="text-xs text-slate-500 mt-1">Coba ubah filter status atau buat pesanan baru secara manual.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Custom Themed Pagination Slot -->
        <x-slot:pagination>
            {{ $orders->links() }}
        </x-slot:pagination>
    </x-table-card>

    <!-- Reusable Manual Create Order Modal -->
    <x-modal
        id="create-order-modal"
        title="Buat Pesanan Baru (Manual Entry)"
        subtitle="Input pesanan customer baru dengan kalkulasi harga server-authoritative dan reservasi stok atomik"
        maxWidth="4xl"
    >
        <form wire:submit="saveOrder" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Customer & Shipping Details -->
                <div class="space-y-4">
                    <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">1. Informasi Pemesan & Alamat</p>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Nama Customer <span class="text-rose-400">*</span></label>
                        <input type="text" wire:model="customerName" placeholder="Nama Lengkap Customer" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        @error('customerName') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">Email <span class="text-rose-400">*</span></label>
                            <input type="email" wire:model="customerEmail" placeholder="customer@mail.com" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                            @error('customerEmail') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">WhatsApp / Telp <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="customerPhone" placeholder="08123456789" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                            @error('customerPhone') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">Nama Penerima <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="recipientName" placeholder="Nama Penerima Paket" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">Telp Penerima <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="recipientPhone" placeholder="08123456789" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300">Alamat Lengkap <span class="text-rose-400">*</span></label>
                        <textarea wire:model="addressLine1" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan..." required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2.5">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-300">Kota / Kab <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="city" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-300">Provinsi <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="province" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-semibold text-slate-300">Kode Pos <span class="text-rose-400">*</span></label>
                            <input type="text" wire:model="postalCode" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                        </div>
                    </div>
                </div>

                <!-- Right Column: Products Picker & Financial Summary -->
                <div class="space-y-4">
                    <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">2. Item Produk & Ringkasan</p>

                    <!-- Add Variant Selector -->
                    <div class="flex items-center gap-2">
                        <select wire:model="selectedVariantId" class="flex-1 bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70] cursor-pointer">
                            <option value="">-- Pilih Produk & Varian (SKU) --</option>
                            @foreach($availableVariants as $v)
                                <option value="{{ $v->id }}">
                                    [{{ $v->sku }}] {{ $v->product->name }} — {{ $v->title }} ({{ $v->formatted_price }} | Stok: {{ $v->inventoryItem?->available ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                        <button
                            type="button"
                            wire:click="addVariantItem"
                            class="px-3 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-[#CBAC70] border border-[#CBAC70]/30 transition-colors cursor-pointer"
                        >
                            + Tambah
                        </button>
                    </div>

                    <!-- Selected Items List -->
                    <div class="rounded-xl border border-slate-800 bg-[#070C1A] p-3 max-h-48 overflow-y-auto space-y-2.5">
                        @forelse($orderItems as $idx => $it)
                            <div class="flex items-center justify-between gap-3 text-xs p-2 rounded-lg bg-white/[0.02] border border-slate-800/80">
                                <div class="min-w-0 flex-1">
                                    <p class="font-mono font-bold text-[#CBAC70]">{{ $it['sku'] }}</p>
                                    <p class="text-slate-200 truncate">{{ $it['product_name'] }} ({{ $it['title'] }})</p>
                                    <p class="text-slate-400 font-mono text-[11px]">Rp {{ number_format($it['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-slate-800/80 overflow-hidden">
                                        <button type="button" wire:click="decrementQuantity({{ $idx }})" class="px-2 py-1 text-slate-300 hover:bg-slate-700 cursor-pointer">-</button>
                                        <span class="px-2.5 py-1 font-mono font-bold text-slate-100 text-xs">{{ $it['quantity'] }}</span>
                                        <button type="button" wire:click="incrementQuantity({{ $idx }})" class="px-2 py-1 text-slate-300 hover:bg-slate-700 cursor-pointer">+</button>
                                    </div>
                                    <p class="font-mono font-bold text-slate-100 text-xs w-24 text-right">
                                        Rp {{ number_format($it['price'] * $it['quantity'], 0, ',', '.') }}
                                    </p>
                                    <button type="button" wire:click="removeVariantItem({{ $idx }})" class="text-rose-400 hover:text-rose-300 p-1 cursor-pointer">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 text-xs py-4">Belum ada item produk dipilih.</p>
                        @endforelse
                    </div>

                    <!-- Costs & Live Totals -->
                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Produk</span>
                            <span class="font-mono font-semibold text-slate-200">Rp {{ number_format($this->estimatedSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-slate-400">
                            <span>Ongkos Kirim (IDR)</span>
                            <input type="number" wire:model.live="shippingTotal" min="0" class="w-28 bg-[#070C1A] border border-slate-700 rounded-lg p-1 text-right font-mono text-slate-200 text-xs">
                        </div>
                        <div class="flex items-center justify-between gap-4 text-slate-400">
                            <span>Diskon Potongan (IDR)</span>
                            <input type="number" wire:model.live="discountTotal" min="0" class="w-28 bg-[#070C1A] border border-slate-700 rounded-lg p-1 text-right font-mono text-slate-200 text-xs">
                        </div>
                        <div class="pt-2 border-t border-slate-800 flex justify-between items-center">
                            <span class="font-bold text-slate-100 uppercase tracking-wider text-xs">Grand Total</span>
                            <span class="font-mono font-bold text-[#CBAC70] text-base">Rp {{ number_format($this->estimatedGrandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-create-order-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveOrder">
                        Konfirmasi & Buat Pesanan
                    </span>
                    <span wire:loading.inline-flex wire:target="saveOrder" class="items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#0B132B]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memproses Pesanan...</span>
                    </span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Reusable Order Detail & Action Modal -->
    <x-modal
        id="order-detail-modal"
        :title="'Detail Pesanan #' . ($activeOrder?->order_number ?? '')"
        subtitle="Snapshot item produk historis, alamat pengiriman, dan kontrol transisi status"
        maxWidth="3xl"
    >
        @if($activeOrder)
            <div class="space-y-6">
                <!-- Status Badges Summary Card -->
                <div class="p-4 rounded-2xl bg-[#070C1A] border border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-mono font-bold text-base text-[#CBAC70]">{{ $activeOrder->order_number }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Dibuat pada {{ $activeOrder->created_at->format('d M Y H:i:s') }} via {{ strtoupper($activeOrder->source) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $activeOrder->order_status->badgeClasses() }}">
                            {{ $activeOrder->order_status->label() }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $activeOrder->payment_status->badgeClasses() }}">
                            {{ $activeOrder->payment_status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Snapshot Line Items -->
                <div class="space-y-2">
                    <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Snapshot Produk Pesanan (ADR-006)</p>
                    <div class="rounded-xl border border-slate-800 bg-[#070C1A] overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-white/[0.02] border-b border-slate-800 text-[10px] font-mono text-slate-400 uppercase">
                                <tr>
                                    <th class="px-4 py-2.5">Produk & SKU</th>
                                    <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                                    <th class="px-4 py-2.5 text-center">Qty</th>
                                    <th class="px-4 py-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @foreach($activeOrder->items as $it)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-mono font-bold text-[#CBAC70] text-xs">{{ $it->sku }}</p>
                                            <p class="text-slate-200 font-medium">{{ $it->product_name }}</p>
                                            <p class="text-slate-400 text-[11px]">{{ $it->variant_title }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono text-slate-300">
                                            {{ $it->formatted_unit_price }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono font-bold text-slate-200">
                                            {{ $it->quantity }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-100">
                                            {{ $it->formatted_subtotal }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financial Calculation Breakdown -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Shipping Address Info -->
                    <div class="p-3.5 rounded-xl bg-[#070C1A] border border-slate-800 text-xs space-y-1.5">
                        <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Alamat Pengiriman</p>
                        <p class="text-slate-100 font-semibold">{{ $activeOrder->address?->recipient_name }} ({{ $activeOrder->address?->phone }})</p>
                        <p class="text-slate-300 leading-relaxed">{{ $activeOrder->address?->address_line1 }}</p>
                        <p class="text-slate-400">{{ $activeOrder->address?->city }}, {{ $activeOrder->address?->province }} {{ $activeOrder->address?->postal_code }}</p>
                    </div>

                    <!-- Payment Summary Breakdown -->
                    <div class="p-3.5 rounded-xl bg-[#070C1A] border border-slate-800 text-xs space-y-2">
                        <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Rincian Finansial (IDR)</p>
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Produk</span>
                            <span class="font-mono text-slate-200">{{ $activeOrder->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Ongkos Kirim</span>
                            <span class="font-mono text-slate-200">Rp {{ number_format($activeOrder->shipping_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Potongan Diskon</span>
                            <span class="font-mono text-rose-400">-Rp {{ number_format($activeOrder->discount_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-800 flex justify-between items-center">
                            <span class="font-bold text-slate-100">Total Tagihan</span>
                            <span class="font-mono font-bold text-[#CBAC70] text-sm">{{ $activeOrder->formatted_grand_total }}</span>
                        </div>
                    </div>
                </div>

                <!-- Courier Fulfillment Section -->
                @if($activeOrder->order_status->value !== 'cancelled')
                    <div class="p-4 rounded-xl bg-[#070C1A] border border-slate-800 space-y-3">
                        <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Input Kurir & Nomor Resi Pengiriman</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                            <div class="space-y-1">
                                <label class="block text-[11px] text-slate-300">Ekspedisi Kurir</label>
                                <input type="text" wire:model="fulfillmentCourier" placeholder="misal: JNE REG / SiCepat" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-slate-200">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[11px] text-slate-300">Nomor Resi</label>
                                <input type="text" wire:model="fulfillmentTrackingNumber" placeholder="No. Resi Pengiriman" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-xs text-slate-200 font-mono">
                            </div>
                            <div>
                                <button
                                    type="button"
                                    wire:click="submitFulfillment"
                                    class="w-full py-2 px-3 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs transition-colors cursor-pointer"
                                >
                                    Update Resi & Potong Stok
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Audit Timeline History -->
                <div class="space-y-2">
                    <p class="text-[11px] font-mono text-[#CBAC70] uppercase font-bold tracking-wider">Timeline Riwayat Status</p>
                    <div class="space-y-2 max-h-36 overflow-y-auto">
                        @foreach($activeOrder->statusHistories as $h)
                            <div class="flex items-start gap-3 text-xs p-2 rounded-lg bg-white/[0.02] border border-slate-800/80">
                                <span class="font-mono text-[10px] text-slate-500 whitespace-nowrap">{{ $h->created_at->format('d/m H:i') }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-slate-200 font-medium">{{ $h->notes }}</p>
                                    <p class="text-slate-500 font-mono text-[10px]">Oleh: {{ $h->user?->name ?? 'System Process' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Action Buttons Footer -->
                <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        @if($activeOrder->order_status->value !== 'cancelled' && $activeOrder->order_status->value !== 'completed')
                            <button
                                type="button"
                                wire:click="cancelOrder"
                                class="px-3.5 py-2 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-semibold text-xs transition-colors cursor-pointer"
                            >
                                Batalkan Pesanan & Lepas Stok
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if($activeOrder->payment_status->value === 'unpaid')
                            <button
                                type="button"
                                wire:click="markAsPaid"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-colors cursor-pointer"
                            >
                                Tandai Lunas
                            </button>
                        @endif

                        @if($activeOrder->order_status->value === 'processing')
                            <button
                                type="button"
                                wire:click="markAsCompleted"
                                class="px-4 py-2 rounded-xl bg-[#CBAC70] hover:bg-[#DFB67A] text-[#0B132B] font-bold text-xs transition-colors cursor-pointer"
                            >
                                Selesaikan Pesanan
                            </button>
                        @endif

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal-order-detail-modal')"
                            class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</div>

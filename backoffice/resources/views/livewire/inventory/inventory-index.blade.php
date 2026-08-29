<div class="space-y-6">
    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Inventori & Buku Besar Mutasi"
        subtitle="Pantau saldo stok fisik (on hand), stok tertahan pesanan (reserved), dan buku besar mutasi per SKU"
        :count="$totalItemsCount"
    >
        <!-- Filter & Control Bar (Full-Width Single Row) -->
        <x-slot:controls>
            <!-- Left: Quick Filter Tabs (Pill Style) -->
            <div class="flex items-center bg-[#070C1A] border border-slate-800 p-1 rounded-xl text-xs">
                <button
                    type="button"
                    wire:click="setTab('all')"
                    class="px-2.5 py-1.5 text-[11px] font-medium transition-colors border-r border-slate-700/80 last:border-r-0 {{ $tabFilter === 'all' ? 'bg-[#CBAC70]/10 text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.02]' }}"
                >
                    Semua ({{ $totalItemsCount }})
                </button>
                <button
                    type="button"
                    wire:click="setTab('low')"
                    class="px-2.5 py-1.5 text-[11px] font-medium transition-colors border-r border-slate-700/80 flex items-center gap-1.5 {{ $tabFilter === 'low' ? 'bg-[#CBAC70]/10 text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.02]' }}"
                >
                    <span>Stok Menipis</span>
                    @if($lowStockCount > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    @endif
                </button>
                <button
                    type="button"
                    wire:click="setTab('out')"
                    class="px-2.5 py-1.5 text-[11px] font-medium transition-colors flex items-center gap-1.5 {{ $tabFilter === 'out' ? 'bg-rose-500/10 text-rose-400' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.02]' }}"
                >
                    <span>Stok Habis</span>
                    @if($outOfStockCount > 0)
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    @endif
                </button>
            </div>

            <!-- Right: Search Bar & Category Filter Group -->
            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <!-- Search Bar -->
                <div class="relative w-full sm:w-60 lg:w-64">
                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari SKU atau produk..."
                        class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 pl-8 pr-3 text-[11px] text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                    >
                </div>

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
            </div>
        </x-slot:controls>

        <!-- Inventory Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[10px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-4 py-3 font-medium">Varian & Kode SKU</th>
                    <th class="px-4 py-3 font-medium text-center">Kategori</th>
                    <th class="px-4 py-3 font-medium text-center" title="Stok fisik yang ada di gudang (Belum dikurangi pesanan yang belum diproses)">Fisik (On-Hand)</th>
                    <th class="px-4 py-3 font-medium text-center" title="Stok yang sudah dipesan tapi belum dikirim">Tertahan (Reserved)</th>
                    <th class="px-4 py-3 font-medium text-center" title="Stok yang siap dijual">Tersedia (Available)</th>
                    <th class="px-4 py-3 font-medium text-center">Status Stok</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($inventoryItems as $item)
                    <tr wire:key="inv-row-{{ $item->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Variant Info -->
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-mono font-bold text-sm text-[#CBAC70] group-hover:text-[#DFB67A] transition-colors">
                                    {{ $item->variant->sku }}
                                </p>
                                <p class="text-slate-200 font-medium text-xs mt-0.5">
                                    {{ $item->variant->product->name }}
                                </p>
                                <p class="text-slate-400 text-[11px] mt-0.5">
                                    {{ $item->variant->title }}
                                </p>
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-medium bg-slate-800/80 text-slate-300 border border-slate-700/60">
                                {{ $item->variant->product->category->name }}
                            </span>
                        </td>

                        <!-- Physical Stock (On-Hand) -->
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono font-bold text-slate-200 text-[13px]">{{ number_format($item->on_hand) }}</span>
                        </td>

                        <!-- Reserved Stock -->
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono font-medium text-[13px] {{ $item->reserved > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                                {{ number_format($item->reserved) }}
                            </span>
                        </td>

                        <!-- Available Stock -->
                        <td class="px-4 py-3 text-center">
                            <span class="font-mono font-bold text-[13px] {{ $item->available > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ number_format($item->available) }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-4 py-3 text-center">
                            @if($item->is_out_of_stock)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                    Habis
                                </span>
                            @elseif($item->is_low_stock)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    Menipis (&le; {{ $item->low_stock_threshold }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]"></span>
                                    Aman
                                </span>
                            @endif
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Stock Opname Action Button -->
                                <button
                                    type="button"
                                    wire:click="openAdjustmentModal({{ $item->id }})"
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-[#CBAC70]/20 hover:border-[#CBAC70]/50 border border-slate-700/80 transition-colors text-[10px] font-medium flex items-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Opname
                                </button>

                                <!-- Audit Ledger Movement History Button -->
                                <button
                                    type="button"
                                    wire:click="openLedgerModal({{ $item->id }})"
                                    class="p-1 rounded-lg text-slate-400 hover:text-[#CBAC70] hover:bg-[#CBAC70]/10 transition-colors"
                                    title="Lihat Buku Besar"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 3.75c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-200">Tidak ada data stok ditemukan</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan filter atau tambahkan produk baru pada modul Katalog.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Custom Themed Pagination Slot -->
        <x-slot:pagination>
            {{ $inventoryItems->links() }}
        </x-slot:pagination>
    </x-table-card>

    <!-- Reusable Stock Adjustment / Opname Modal -->
    <x-modal
        id="stock-adjustment-modal"
        title="Penyesuaian Stok Fisik (Stock Opname)"
        subtitle="Lakukan audit penyesuaian stok fisik secara atomik dengan pencatatan buku besar"
        maxWidth="lg"
    >
        <form wire:submit="saveAdjustment" class="space-y-4">
            <!-- Target SKU Summary Card -->
            <div class="p-3.5 rounded-2xl bg-[#070C1A] border border-slate-800 flex items-center justify-between">
                <div>
                    <p class="font-mono font-bold text-xs text-[#CBAC70]">{{ $adjustingItemSku }}</p>
                    <p class="text-xs text-slate-200 font-medium">{{ $adjustingProductName }} — {{ $adjustingItemTitle }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 font-mono">Fisik Saat Ini</p>
                    <p class="font-mono font-bold text-slate-100 text-sm">{{ number_format($currentOnHand) }} unit</p>
                </div>
            </div>

            <!-- New On-Hand Input -->
            <div class="space-y-1.5">
                <label for="newOnHand" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Jumlah Stok Fisik Baru (Hasil Hitung Opname) <span class="text-rose-400 font-mono">*</span>
                </label>
                <input
                    id="newOnHand"
                    type="number"
                    wire:model="newOnHand"
                    min="0"
                    required
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-sm font-mono font-bold text-slate-100 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
                @error('newOnHand')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
                @if($currentReserved > 0)
                    <p class="text-[11px] text-amber-400 flex items-center gap-1 mt-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Terdapat {{ $currentReserved }} unit tertahan pesanan. Nilai baru minimal &ge; {{ $currentReserved }}.</span>
                    </p>
                @endif
            </div>

            <!-- Low Stock Threshold Input -->
            <div class="space-y-1.5">
                <label for="lowStockThreshold" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Ambang Batas Peringatan Stok Menipis <span class="text-rose-400 font-mono">*</span>
                </label>
                <input
                    id="lowStockThreshold"
                    type="number"
                    wire:model="lowStockThreshold"
                    min="1"
                    required
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs font-mono text-slate-200 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
                @error('lowStockThreshold')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Note Input -->
            <div class="space-y-1.5">
                <label for="referenceNote" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Catatan / Alasan Penyesuaian <span class="text-rose-400 font-mono">*</span>
                </label>
                <input
                    id="referenceNote"
                    type="text"
                    wire:model="referenceNote"
                    placeholder="misal: Stock opname akhir bulan / penemuan barang cacat"
                    required
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
                @error('referenceNote')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Modal Action Buttons Footer -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-stock-adjustment-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveAdjustment">
                        Simpan Penyesuaian
                    </span>
                    <span wire:loading.inline-flex wire:target="saveAdjustment" class="items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#0B132B]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memproses...</span>
                    </span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Reusable Stock Movement Ledger History Modal -->
    <x-modal
        id="stock-ledger-modal"
        :title="'Buku Besar Mutasi — ' . $viewingItemSku"
        :subtitle="'Riwayat mutasi stok lengkap untuk ' . $viewingProductName . ' (Append-Only Audit Ledger ADR-003)'"
        maxWidth="3xl"
    >
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-800 bg-[#070C1A] overflow-hidden">
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-white/[0.02] border-b border-slate-800 font-mono text-[10px] text-slate-400 uppercase">
                            <tr>
                                <th class="px-4 py-3">Waktu Mutasi</th>
                                <th class="px-4 py-3">Jenis Mutasi</th>
                                <th class="px-4 py-3 text-center">Perubahan Qty</th>
                                <th class="px-4 py-3 text-center">Saldo Akhir</th>
                                <th class="px-4 py-3">Staf / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($movements as $m)
                                <tr class="hover:bg-white/[0.01]">
                                    <!-- Timestamp -->
                                    <td class="px-4 py-3 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                        {{ $m->created_at->format('d M Y H:i') }}
                                    </td>

                                    <!-- Movement Type Badge -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $m->type->badgeClasses() }}">
                                            {{ $m->type->label() }}
                                        </span>
                                    </td>

                                    <!-- Quantity Change -->
                                    <td class="px-4 py-3 text-center font-mono font-bold text-xs whitespace-nowrap {{ $m->quantity_change > 0 ? 'text-emerald-400' : ($m->quantity_change < 0 ? 'text-rose-400' : 'text-slate-400') }}">
                                        {{ $m->quantity_change > 0 ? '+'.$m->quantity_change : $m->quantity_change }}
                                    </td>

                                    <!-- Balance After (On-Hand) -->
                                    <td class="px-4 py-3 text-center font-mono text-xs text-slate-200 whitespace-nowrap">
                                        {{ $m->on_hand_after }} fisik
                                    </td>

                                    <!-- User / Reference Note -->
                                    <td class="px-4 py-3">
                                        <p class="text-slate-200 text-xs truncate max-w-xs">{{ $m->reference_note ?: '-' }}</p>
                                        <p class="text-slate-500 font-mono text-[10px] mt-0.5">
                                            Oleh: {{ $m->user?->name ?? 'System Process' }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs">
                                        Belum ada riwayat mutasi tercatat untuk SKU ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Close Button -->
            <div class="pt-2 flex justify-end">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-stock-ledger-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Tutup Riwayat
                </button>
            </div>
        </div>
    </x-modal>
</div>

<div class="space-y-6" x-data="{
    showVoucherModal: false,
    showUsagesModal: false,
    showDeleteModal: false
}"
x-on:open-voucher-modal.window="showVoucherModal = true"
x-on:close-voucher-modal.window="showVoucherModal = false"
x-on:open-usages-modal.window="showUsagesModal = true"
x-on:close-usages-modal.window="showUsagesModal = false"
x-on:open-delete-modal.window="showDeleteModal = true"
x-on:close-delete-modal.window="showDeleteModal = false">

    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-[#0B132B] via-[#0E1A3D] to-[#0B132B] p-6 rounded-2xl border border-gold/20 shadow-xl">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gold/15 border border-gold/30 flex items-center justify-center text-gold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </span>
                <h1 class="text-xl font-bold font-display text-ivory tracking-wide">Voucher & Kupon Promosi</h1>
            </div>
            <p class="text-xs text-slate-400">
                Kelola master kode promo, diskon persentase, potongan nominal, subsidi ongkir, serta batasan kuota untuk Storefront.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold text-xs shadow-lg shadow-gold/10 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Voucher Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Message -->
    @if(session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400">✕</button>
        </div>
    @endif

    <!-- Top KPI Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-4 shadow-lg space-y-1">
            <span class="text-[11px] text-slate-400 font-semibold block">Total Voucher</span>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-black font-display text-ivory">{{ $totalVouchers }}</span>
                <span class="text-[10px] text-gold font-mono uppercase tracking-wider">Kupon</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-4 shadow-lg space-y-1">
            <span class="text-[11px] text-slate-400 font-semibold block">Voucher Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-black font-display text-emerald-400">{{ $activeVouchers }}</span>
                <span class="text-[10px] text-emerald-400 font-mono">Live</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-4 shadow-lg space-y-1">
            <span class="text-[11px] text-slate-400 font-semibold block">Total Pemakaian</span>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-black font-display text-ivory">{{ number_format($totalUsages) }}</span>
                <span class="text-[10px] text-slate-400 font-mono">Transaksi</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-4 shadow-lg space-y-1">
            <span class="text-[11px] text-slate-400 font-semibold block">Diskon Diberikan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-xl font-black font-display text-gold">Rp {{ number_format($totalDiscountGiven, 0, ',', '.') }}</span>
                <span class="text-[10px] text-gold font-mono">Saved</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#070C1A] p-4 rounded-xl border border-slate-800/80 shadow-md">
        <div class="relative w-full sm:w-80">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kode kupon, nama promo..."
                class="w-full bg-[#0B132B] border border-slate-700/80 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-gold"
            >
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <select
                wire:model.live="typeFilter"
                class="bg-[#0B132B] border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold cursor-pointer w-full sm:w-auto"
            >
                <option value="all">Semua Tipe Diskon</option>
                <option value="percentage">Diskon Persentase (%)</option>
                <option value="fixed_amount">Potongan Nominal (Rp)</option>
                <option value="free_shipping">Gratis / Subsidi Ongkir</option>
            </select>

            <select
                wire:model.live="statusFilter"
                class="bg-[#0B132B] border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-gold cursor-pointer w-full sm:w-auto"
            >
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
                <option value="expired">Kedaluwarsa</option>
                <option value="exhausted">Kuota Habis</option>
            </select>
        </div>
    </div>

    <!-- Vouchers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($vouchers as $voucher)
            <div wire:key="voucher-card-{{ $voucher->id }}" class="rounded-2xl border border-slate-800/80 bg-gradient-to-b from-[#0B132B] to-[#070C1A] p-5 space-y-4 shadow-lg hover:border-gold/30 transition-all flex flex-col justify-between">
                <div>
                    <!-- Header Card -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-white/5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider {{ $voucher->type->badgeColor() }}">
                                    {{ $voucher->type->label() }}
                                </span>
                                @if($voucher->is_public)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-purple-500/10 text-purple-400 border border-purple-500/30">
                                        Publik
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $voucher->isValid() ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $voucher->isValid() ? 'Aktif' : 'Nonaktif / Kedaluwarsa' }}
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-ivory font-display tracking-wide flex items-center gap-2">
                                <span>{{ $voucher->name }}</span>
                            </h3>
                        </div>

                        <!-- Voucher Code Chip with Copy -->
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gold/10 border border-gold/30 text-gold font-mono font-bold text-xs shrink-0 select-all">
                            <span>{{ $voucher->code }}</span>
                        </div>
                    </div>

                    @if($voucher->description)
                        <p class="text-xs text-slate-400 pt-2 leading-relaxed">
                            {{ $voucher->description }}
                        </p>
                    @endif

                    <!-- Details Matrix -->
                    <div class="grid grid-cols-2 gap-2 pt-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Nilai Diskon</span>
                            <span class="text-gold font-bold font-mono">{{ $voucher->formattedDiscount() }}</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Min. Belanja</span>
                            <span class="text-slate-200 font-bold font-mono">Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Kuota Pemakaian</span>
                            <span class="text-slate-200 font-bold font-mono">
                                {{ $voucher->used_count }} / {{ $voucher->usage_limit_total ? number_format($voucher->usage_limit_total) : '∞ Tak Terbatas' }}
                            </span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Masa Berlaku</span>
                            <span class="text-slate-300 font-mono text-[11px]">
                                {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'Selamanya' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Actions -->
                <div class="flex items-center justify-between pt-3 border-t border-white/5 gap-2">
                    <button
                        type="button"
                        wire:click="openUsagesModal({{ $voucher->id }})"
                        class="text-[11px] font-semibold text-slate-400 hover:text-gold flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Riwayat ({{ $voucher->usages_count }})</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <!-- Toggle Active -->
                        <button
                            type="button"
                            wire:click="toggleStatus({{ $voucher->id }})"
                            title="{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            class="p-1.5 rounded-lg border {{ $voucher->is_active ? 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' : 'border-slate-700 text-slate-500 hover:bg-slate-700/30' }} transition-colors cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M12 9v4" />
                            </svg>
                        </button>

                        <!-- Edit -->
                        <button
                            type="button"
                            wire:click="openEditModal({{ $voucher->id }})"
                            class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors cursor-pointer"
                        >
                            Edit
                        </button>

                        <!-- Delete -->
                        <button
                            type="button"
                            wire:click="confirmDelete({{ $voucher->id }})"
                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs transition-colors cursor-pointer"
                            title="Hapus"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center rounded-2xl border border-dashed border-slate-800 bg-[#070C1A] space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/30 text-gold flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-300">Belum ada data voucher promosi</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Buat kode promo pertama untuk meningkatkan konversi checkout di Storefront Malega Apparel.</p>
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gold text-navy font-bold text-xs shadow hover:bg-gold-dark transition-all cursor-pointer"
                >
                    + Buat Voucher Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $vouchers->links() }}
    </div>

    <!-- Modal Form Create / Edit Voucher -->
    <div
        x-show="showVoucherModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;"
    >
        <div
            @click.outside="showVoucherModal = false"
            class="bg-[#0B132B] border border-gold/30 rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl text-slate-200 animate-in zoom-in-95"
        >
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-ivory font-display tracking-wide">
                    {{ $isEditing ? 'Edit Master Voucher' : 'Buat Master Voucher Promosi' }}
                </h3>
                <button @click="showVoucherModal = false" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <form wire:submit.prevent="saveVoucher" class="space-y-3.5 text-xs">
                <!-- Kode Promo & Generator -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="font-semibold text-slate-300">Kode Kupon / Voucher <span class="text-rose-400">*</span></label>
                        <button
                            type="button"
                            wire:click="generateRandomCode"
                            class="text-[10px] text-gold font-bold hover:underline cursor-pointer flex items-center gap-1"
                        >
                            ⚡ Generate Acak
                        </button>
                    </div>
                    <input
                        type="text"
                        wire:model="code"
                        placeholder="Contoh: MALEGAVIP15"
                        class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 font-mono uppercase text-gold font-bold tracking-wider focus:outline-none focus:border-gold"
                        required
                    />
                    @error('code') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <!-- Nama Promo -->
                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Nama / Judul Promo <span class="text-rose-400">*</span></label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Contoh: VIP Gold Member 15% OFF"
                        class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 focus:outline-none focus:border-gold"
                        required
                    />
                    @error('name') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <!-- Deskripsi Syarat & Ketentuan -->
                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Deskripsi / Syarat Ketentuan</label>
                    <textarea
                        wire:model="description"
                        rows="2"
                        placeholder="Berikan catatan singkat syarat promo untuk pembeli..."
                        class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-gold resize-none"
                    ></textarea>
                </div>

                <!-- Tipe Diskon & Nilai -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Tipe Diskon <span class="text-rose-400">*</span></label>
                        <select
                            wire:model.live="type"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 focus:outline-none focus:border-gold cursor-pointer"
                        >
                            <option value="percentage">Diskon Persentase (%)</option>
                            <option value="fixed_amount">Potongan Nominal (Rp)</option>
                            <option value="free_shipping">Gratis / Subsidi Ongkir</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">
                            {{ $type === 'percentage' ? 'Persentase Diskon (%)' : 'Nominal Potongan (Rp)' }} <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model="amount"
                            min="1"
                            placeholder="{{ $type === 'percentage' ? '15' : '50000' }}"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 font-mono focus:outline-none focus:border-gold"
                            required
                        />
                        @error('amount') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Batas Maksimal Diskon & Minimal Belanja -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Maks. Potongan Diskon (Rp)</label>
                        <input
                            type="number"
                            wire:model="max_discount_amount"
                            placeholder="50000 (Kosongkan jika tanpa batas)"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 font-mono focus:outline-none focus:border-gold"
                        />
                        <span class="text-[9px] text-slate-500 block">Khusus untuk diskon persentase</span>
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Min. Subtotal Belanja (Rp) <span class="text-rose-400">*</span></label>
                        <input
                            type="number"
                            wire:model="min_order_amount"
                            min="0"
                            placeholder="200000"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 font-mono focus:outline-none focus:border-gold"
                            required
                        />
                    </div>
                </div>

                <!-- Batasan Kuota Total & Kuota per User -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Total Kuota Penggunaan</label>
                        <input
                            type="number"
                            wire:model="usage_limit_total"
                            placeholder="1000 (Kosongkan jika tak terbatas)"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 font-mono focus:outline-none focus:border-gold"
                        />
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Batas Pakai per Email <span class="text-rose-400">*</span></label>
                        <input
                            type="number"
                            wire:model="usage_limit_per_user"
                            min="1"
                            placeholder="1"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2.5 text-slate-200 font-mono focus:outline-none focus:border-gold"
                            required
                        />
                    </div>
                </div>

                <!-- Periode Tanggal Berlaku -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Tanggal Mulai Berlaku <span class="text-rose-400">*</span></label>
                        <input
                            type="datetime-local"
                            wire:model="valid_from"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-gold"
                            required
                        />
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Tanggal Berakhir Promo</label>
                        <input
                            type="datetime-local"
                            wire:model="valid_until"
                            class="w-full bg-[#070C1A] border border-slate-700 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-gold"
                        />
                        <span class="text-[9px] text-slate-500 block">Kosongkan jika promo permanen</span>
                    </div>
                </div>

                <!-- Status & Public Toggles -->
                <div class="flex items-center gap-6 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-gold focus:ring-0 cursor-pointer"
                        />
                        <span class="font-semibold text-slate-300 text-xs">Voucher Aktif</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="is_public"
                            class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-gold focus:ring-0 cursor-pointer"
                        />
                        <span class="font-semibold text-slate-300 text-xs">Tampilkan Publik di Storefront</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-white/10">
                    <button
                        type="button"
                        @click="showVoucherModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-dark text-navy font-bold shadow-lg shadow-gold/20 transition-all cursor-pointer"
                    >
                        {{ $isEditing ? 'Simpan Perubahan' : 'Terbitkan Voucher' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Riwayat Penggunaan Voucher -->
    <div
        x-show="showUsagesModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;"
    >
        <div
            @click.outside="showUsagesModal = false"
            class="bg-[#0B132B] border border-gold/30 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl text-slate-200 animate-in zoom-in-95"
        >
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div>
                    <span class="text-[10px] font-mono text-gold uppercase tracking-wider block font-bold">LOG RIWAYAT PENGGUNAAN</span>
                    <h3 class="text-sm font-bold text-ivory">
                        Voucher #{{ $viewingVoucher?->code }} — {{ $viewingVoucher?->name }}
                    </h3>
                </div>
                <button @click="showUsagesModal = false" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <div class="max-h-80 overflow-y-auto divide-y divide-white/5 space-y-2 text-xs">
                @if($viewingVoucher && $viewingVoucher->usages->isNotEmpty())
                    @foreach($viewingVoucher->usages as $usage)
                        <div class="pt-2 flex items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-ivory">{{ $usage->customer_email }}</span>
                                    @if($usage->order)
                                        <span class="font-mono text-[10px] text-gold bg-[#070C1A] px-1.5 py-0.5 rounded border border-white/10">
                                            #{{ $usage->order->order_number }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-500 block">
                                    {{ $usage->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <span class="font-mono font-bold text-emerald-400">
                                -Rp {{ number_format($usage->discount_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="py-8 text-center text-slate-500">
                        Belum ada pelanggan yang menggunakan voucher ini.
                    </div>
                @endif
            </div>

            <div class="flex justify-end pt-3 border-t border-white/10">
                <button
                    type="button"
                    @click="showUsagesModal = false"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div
        x-show="showDeleteModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        style="display: none;"
    >
        <div
            @click.outside="showDeleteModal = false"
            class="bg-[#0B132B] border border-rose-500/40 rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl text-slate-200 animate-in zoom-in-95 text-center"
        >
            <div class="w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>

            <div class="space-y-1">
                <h3 class="text-sm font-bold text-ivory">Hapus Master Voucher?</h3>
                <p class="text-xs text-slate-400">
                    Voucher <strong class="text-rose-400 font-mono">#{{ $deletingVoucherCode }}</strong> akan dihapus dan tidak dapat digunakan lagi oleh pembeli di Storefront.
                </p>
            </div>

            <div class="flex items-center justify-center gap-2.5 pt-2">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="deleteVoucher"
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg transition-colors cursor-pointer"
                >
                    Ya, Hapus Voucher
                </button>
            </div>
        </div>
    </div>

</div>

<div class="space-y-6">

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
                                @if($voucher->allow_guest)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/10 text-blue-400 border border-blue-500/30" title="Bisa digunakan pembeli tamu tanpa harus login">
                                        Tamu & Member
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30" title="Wajib login untuk menggunakan voucher ini">
                                        Member Only
                                    </span>
                                @endif
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
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Nilai Diskon</span>
                            <span class="text-gold font-bold font-mono">{{ $voucher->formattedDiscount() }}</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Min. Belanja</span>
                            <span class="text-slate-200 font-bold font-mono">Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Batas per User</span>
                            <span class="text-slate-200 font-bold font-mono">
                                {{ $voucher->usage_limit_per_user === 1 ? '1x (Sekali Pakai)' : $voucher->usage_limit_per_user . 'x per Akun' }}
                            </span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Kuota Global</span>
                            <span class="text-slate-200 font-bold font-mono">
                                {{ $voucher->used_count }} / {{ $voucher->usage_limit_total ? number_format($voucher->usage_limit_total) : '∞ Tak Terbatas' }}
                            </span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-[#0B132B]/80 border border-white/5 space-y-0.5 col-span-2 sm:col-span-2">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase tracking-wider">Masa Berlaku</span>
                            <span class="text-slate-300 font-mono text-[11px]">
                                {{ $voucher->valid_from ? $voucher->valid_from->format('d M Y') : 'Sekarang' }} s.d. {{ $voucher->valid_until ? $voucher->valid_until->format('d M Y') : 'Selamanya' }}
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

    <!-- Reusable Create / Edit Voucher Modal -->
    <x-modal
        id="voucher-modal"
        name="voucher-modal"
        :title="$isEditing ? 'Edit Informasi Master Voucher' : 'Buat Master Voucher Promosi Baru'"
        :subtitle="$isEditing ? 'Perbarui detail diskon, kuota pemakaian, dan masa aktif voucher promosi' : 'Buat master voucher dan kupon diskon baru untuk kampanye promosi di Storefront'"
        maxWidth="3xl"
    >
        <form wire:submit.prevent="saveVoucher" class="space-y-5">
            <!-- SECTION 1: Identitas & Kode Promo -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 pb-1 border-b border-white/5">
                    <span class="w-4 h-4 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-[10px] flex items-center justify-center">1</span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Identitas & Kode Promo</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <!-- Kode Voucher & Generator -->
                    <div class="sm:col-span-6 space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                                Kode Kupon / Voucher <span class="text-rose-400">*</span>
                            </label>
                            <button
                                type="button"
                                wire:click="generateRandomCode"
                                class="text-[10px] text-[#CBAC70] font-bold hover:underline cursor-pointer flex items-center gap-1"
                            >
                                ⚡ Generate Acak
                            </button>
                        </div>
                        <input
                            type="text"
                            wire:model="code"
                            placeholder="Contoh: MALEGAVIP15"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 font-mono uppercase text-[#CBAC70] font-bold tracking-wider text-xs focus:outline-none focus:border-[#CBAC70] transition-colors"
                            required
                        />
                        @error('code') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nama Promo -->
                    <div class="sm:col-span-6 space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Nama / Judul Promo <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: VIP Gold Member 15% OFF"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors"
                            required
                        />
                        @error('name') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Deskripsi Syarat & Ketentuan -->
                    <div class="sm:col-span-12 space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Deskripsi / Syarat & Ketentuan Promo (Opsional)
                        </label>
                        <textarea
                            wire:model="description"
                            rows="2"
                            placeholder="Berikan catatan singkat syarat promo atau pesan untuk pembeli..."
                            class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-2.5 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] transition-colors resize-none"
                        ></textarea>
                        @error('description') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Skema Diskon & Ketentuan Belanja -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 pb-1 border-b border-white/5">
                    <span class="w-4 h-4 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-[10px] flex items-center justify-center">2</span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Skema Diskon & Ketentuan Belanja</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Tipe Diskon -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Tipe Diskon <span class="text-rose-400">*</span>
                        </label>
                        <select
                            wire:model.live="type"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors cursor-pointer"
                        >
                            <option value="percentage">Diskon Persentase (%)</option>
                            <option value="fixed_amount">Potongan Nominal (Rp)</option>
                            <option value="free_shipping">Gratis / Subsidi Ongkir</option>
                        </select>
                    </div>

                    <!-- Nilai / Nominal Diskon -->
                    @if($type === 'percentage')
                        <div class="space-y-1">
                            <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                                Persentase Diskon (%) <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="amount"
                                    min="1"
                                    max="100"
                                    placeholder="15"
                                    class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 pr-8 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#CBAC70] transition-colors"
                                    required
                                />
                                <span class="absolute right-2.5 top-2.5 text-[11px] text-[#CBAC70] font-mono font-bold select-none">%</span>
                            </div>
                            @error('amount') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    @elseif($type === 'free_shipping')
                        <x-currency-input
                            wire:model="amount"
                            label="Maks. Subsidi Ongkir"
                            placeholder="15.000"
                            required="true"
                            :allowNull="false"
                        />
                    @else
                        <x-currency-input
                            wire:model="amount"
                            label="Nominal Potongan"
                            placeholder="50.000"
                            required="true"
                            :allowNull="false"
                        />
                    @endif

                    <!-- Maksimal Potongan Diskon -->
                    <x-currency-input
                        wire:model="max_discount_amount"
                        label="Maks. Diskon (Cap)"
                        placeholder="50.000"
                        hint="Khusus untuk diskon %"
                    />

                    <!-- Min. Belanja -->
                    <x-currency-input
                        wire:model="min_order_amount"
                        label="Min. Subtotal Belanja"
                        placeholder="200.000"
                        required="true"
                        :allowNull="false"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Kuota Total -->
                    <x-currency-input
                        wire:model="usage_limit_total"
                        label="Batas Kuota Penggunaan Global"
                        placeholder="500 (Kosongkan jika tak terbatas)"
                        prefix=""
                        hint="Total klaim kupon (misal 500 pemakai pertama)"
                    />

                    <!-- Kuota per User -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Batas Pemakaian per Pelanggan <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model="usage_limit_per_user"
                            min="1"
                            placeholder="1"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 font-mono focus:outline-none focus:border-[#CBAC70] transition-colors"
                            required
                        />
                        <span class="text-[10px] text-emerald-400 font-semibold block">Set 1 untuk voucher sekali pakai per akun/email/no. HP</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Masa Berlaku & Hak Akses -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 pb-1 border-b border-white/5">
                    <span class="w-4 h-4 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] font-bold text-[10px] flex items-center justify-center">3</span>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-200">Masa Berlaku & Akses Pengguna</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Tanggal Mulai Berlaku <span class="text-rose-400">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            wire:model="valid_from"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors"
                            required
                        />
                        @error('valid_from') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
                            Tanggal Berakhir Promo (Opsional)
                        </label>
                        <input
                            type="datetime-local"
                            wire:model="valid_until"
                            class="w-full h-9 bg-[#070C1A] border border-slate-700/80 rounded-lg px-2.5 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70] transition-colors"
                        />
                        <span class="text-[10px] text-slate-500 block">Kosongkan jika promo permanen</span>
                        @error('valid_until') <p class="text-[10px] text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- 3 Toggle Access Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                    <label class="flex items-start gap-3 p-3 rounded-xl bg-[#070C1A] border border-slate-800 hover:border-[#CBAC70]/40 transition-colors cursor-pointer select-none">
                        <input
                            type="checkbox"
                            wire:model="allow_guest"
                            class="w-4 h-4 mt-0.5 rounded bg-[#0B132B] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70] focus:ring-offset-0 transition-colors"
                        />
                        <div>
                            <span class="text-xs font-semibold text-slate-200 block">Guest / Tamu</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Dapat digunakan tanpa login</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 rounded-xl bg-[#070C1A] border border-slate-800 hover:border-[#CBAC70]/40 transition-colors cursor-pointer select-none">
                        <input
                            type="checkbox"
                            wire:model="is_public"
                            class="w-4 h-4 mt-0.5 rounded bg-[#0B132B] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70] focus:ring-offset-0 transition-colors"
                        />
                        <div>
                            <span class="text-xs font-semibold text-slate-200 block">Kupon Publik</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Tampil di popup Storefront</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3 rounded-xl bg-[#070C1A] border border-slate-800 hover:border-[#CBAC70]/40 transition-colors cursor-pointer select-none">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="w-4 h-4 mt-0.5 rounded bg-[#0B132B] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70] focus:ring-offset-0 transition-colors"
                        />
                        <div>
                            <span class="text-xs font-semibold text-slate-200 block">Status Aktif</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Siap divalidasi pembeli</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Modal Action Buttons Footer -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-voucher-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveVoucher">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Terbitkan Voucher' }}
                    </span>
                    <span wire:loading.inline-flex wire:target="saveVoucher" class="items-center gap-1.5">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#0B132B]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Reusable Log Usages Modal -->
    <x-modal
        id="usages-modal"
        name="usages-modal"
        :title="'Log Riwayat Penggunaan: #' . ($viewingVoucher?->code ?? '')"
        :subtitle="'Daftar transaksi pesanan yang telah menukarkan voucher ' . ($viewingVoucher?->name ?? '')"
        maxWidth="2xl"
    >
        <div class="space-y-4">
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-800/60 pr-1">
                @if($viewingVoucher && $viewingVoucher->usages->isNotEmpty())
                    @foreach($viewingVoucher->usages as $usage)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-200">{{ $usage->customer_email ?? ($usage->customer?->email ?? 'Pelanggan') }}</span>
                                    @if($usage->customer_phone)
                                        <span class="text-[10px] font-mono text-slate-400">({{ $usage->customer_phone }})</span>
                                    @endif
                                    @if($usage->order)
                                        <span class="font-mono text-[10px] text-[#CBAC70] bg-[#070C1A] px-2 py-0.5 rounded border border-[#CBAC70]/20 font-bold">
                                            #{{ $usage->order->order_number }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-500 block">
                                    {{ $usage->created_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-emerald-400 text-xs block">
                                    -Rp {{ number_format($usage->discount_amount, 0, ',', '.') }}
                                </span>
                                <span class="text-[9px] text-slate-500 block uppercase font-mono">Diskon Dipakai</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-12 text-center rounded-xl bg-[#070C1A] border border-slate-800/80 space-y-2">
                        <svg class="w-8 h-8 mx-auto text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs font-semibold text-slate-400">Belum ada riwayat penggunaan</p>
                        <p class="text-[10px] text-slate-500">Kupon ini belum pernah ditukarkan pada transaksi pesanan apapun.</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-800/80 flex items-center justify-end">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-usages-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Reusable Delete Confirmation Modal -->
    <x-confirmation-modal
        id="delete-voucher-modal"
        title="Konfirmasi Hapus Voucher"
        message="Apakah Anda yakin ingin menghapus voucher ini? Kupon yang dihapus tidak dapat digunakan lagi di Storefront."
        confirmText="Hapus Voucher"
        cancelText="Batal"
        type="danger"
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="deleteVoucher"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer bg-rose-600 hover:bg-rose-500 text-white"
                x-on:click="$dispatch('close-confirmation-delete-voucher-modal')"
            >
                Hapus Voucher
            </button>
        </x-slot:action>
    </x-confirmation-modal>

</div>

<div class="space-y-6">
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/40">
                    LOYALTY & CRM
                </span>
                <span class="text-xs text-slate-500 font-mono">• Malega Privilege Club</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-wide mt-1">
                Master Tingkatan & Voucher Member
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Konfigurasi syarat akumulasi belanja seumur hidup, tempelkan voucher diskon eksklusif, dan atur keuntungan khusus pelanggan.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('customers.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition-colors flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Daftar Pelanggan</span>
            </a>

            <button
                type="button"
                wire:click="openCreateModal"
                class="px-4 py-2 rounded-xl text-xs font-bold text-[#0B132B] bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] hover:from-[#E3CD99] hover:to-[#CBAC70] transition-all shadow-lg active:scale-95 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Tingkatan Member</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs (Pelanggan vs Tingkatan Member) -->
    <div class="flex items-center gap-2 border-b border-white/10 pb-2">
        <a
            href="{{ route('customers.index') }}"
            class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-white/5 transition-colors"
        >
            Database Pelanggan & CRM
        </a>
        <a
            href="{{ route('customers.tiers') }}"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-[#CBAC70] text-[#0B132B] shadow-md flex items-center gap-1.5"
        >
            <span>Master Tingkatan Member</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-black/20 font-mono">{{ $tiers->count() }}</span>
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="p-4 rounded-2xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Total Tingkatan Aktif</p>
                <p class="text-xl font-black font-mono text-[#CBAC70] mt-1">{{ $tiers->where('is_active', true)->count() }} Tier</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Dari total {{ $tiers->count() }} tingkatan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/20 flex items-center justify-center text-[#CBAC70]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Total Pelanggan Tersegmentasi</p>
                <p class="text-xl font-black font-mono text-emerald-400 mt-1">{{ number_format($totalCustomers) }} Pelanggan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Otomatis berdasar lifetime spend</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-b from-[#0E1736] to-[#080E20] border border-white/10 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Voucher Eksklusif Terpasang</p>
                <p class="text-xl font-black font-mono text-amber-300 mt-1">{{ $tiers->whereNotNull('voucher_id')->count() }} Kupon</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Terkunci untuk member berhak</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Tiers Grid Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @forelse($tiers as $tier)
            <div
                wire:key="tier-card-{{ $tier->id }}"
                class="rounded-3xl border {{ $tier->is_active ? 'border-[#CBAC70]/30 bg-gradient-to-b from-[#0E1736] via-[#0B132B] to-[#070C1A]' : 'border-slate-800 bg-[#070C1A] opacity-75' }} p-5 space-y-4 shadow-xl flex flex-col justify-between transition-all hover:border-[#CBAC70]/60"
            >
                <div class="space-y-4">
                    <!-- Card Header: Badge & Status -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider {{ $tier->badgeClasses() }}">
                            {{ $tier->badge_text ?? "★ {$tier->name} Member" }}
                        </span>

                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-mono text-slate-400">Level #{{ $tier->order }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tier->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                {{ $tier->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Min Spend & Customers Count Box -->
                    <div class="p-3.5 rounded-2xl bg-black/40 border border-white/5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Syarat Min. Belanja</span>
                            <span class="text-sm font-black font-mono text-emerald-400">
                                {{ $tier->formatted_min_spend }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-white/5">
                            <span class="text-[10px] font-mono text-slate-400">Jumlah Pelanggan</span>
                            <span class="text-xs font-mono font-bold text-white">
                                {{ number_format($tier->customers_count) }} Orang
                            </span>
                        </div>

                        @if($tier->discount_label)
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] font-mono text-slate-400">Highlight Benefit</span>
                                <span class="text-xs font-bold text-[#CBAC70]">
                                    {{ $tier->discount_label }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    @if($tier->description)
                        <p class="text-xs text-slate-300 leading-relaxed">
                            {{ $tier->description }}
                        </p>
                    @endif

                    <!-- Linked Voucher Section -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-[#CBAC70] font-bold block flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Voucher Khusus Terpasang</span>
                        </span>

                        @if($tier->voucher)
                            <div class="p-3 rounded-xl bg-[#14204A]/60 border border-[#CBAC70]/30 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs font-mono font-black text-[#CBAC70]">
                                            {{ $tier->voucher->code }}
                                        </code>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono font-bold">
                                            {{ $tier->voucher->type->value === 'percentage' ? $tier->voucher->amount . '% OFF' : 'Rp ' . number_format($tier->voucher->amount, 0, ',', '.') . ' OFF' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 truncate max-w-[180px] mt-0.5">
                                        {{ $tier->voucher->name }}
                                    </p>
                                </div>

                                <span class="text-[9px] font-mono text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                                    Terkunci Tier
                                </span>
                            </div>
                        @else
                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-dashed border-slate-700 text-center">
                                <p class="text-[11px] text-slate-400">
                                    Belum ada kupon ditempelkan.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Perks List -->
                    @if(!empty($tier->perks))
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold block">
                                Daftar Keuntungan ({{ count($tier->perks) }})
                            </span>
                            <div class="space-y-1">
                                @foreach($tier->perks as $perk)
                                    <div class="flex items-start gap-1.5 text-xs text-slate-300 bg-white/[0.02] p-2 rounded-lg border border-white/5">
                                        <span class="text-[#CBAC70] mt-0.5">✔</span>
                                        <div>
                                            <strong class="text-white text-[11px] block">{{ $perk['title'] ?? '' }}</strong>
                                            @if(!empty($perk['desc']))
                                                <span class="text-[10px] text-slate-400 block leading-tight">{{ $perk['desc'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Card Actions -->
                <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-2">
                    <button
                        type="button"
                        wire:click="toggleStatus({{ $tier->id }})"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors cursor-pointer {{ $tier->is_active ? 'border-amber-500/30 text-amber-300 hover:bg-amber-500/10' : 'border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/10' }}"
                    >
                        {{ $tier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>

                    <div class="flex items-center gap-1.5">
                        <button
                            type="button"
                            wire:click="openEditModal({{ $tier->id }})"
                            class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white text-xs font-semibold border border-white/10 transition-colors cursor-pointer flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Edit</span>
                        </button>

                        <button
                            type="button"
                            wire:click="confirmDelete({{ $tier->id }})"
                            class="p-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10 transition-colors cursor-pointer"
                            title="Hapus Tier"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-12 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-3">
                <p class="text-sm font-bold text-slate-300">Belum ada master tingkatan member</p>
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-[#0B132B] bg-[#CBAC70] hover:bg-[#E3CD99] transition cursor-pointer"
                >
                    + Buat Tingkatan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Tier Modal -->
    <x-modal
        id="tier-modal"
        name="tier-modal"
        :title="$isEditing ? 'Edit Tingkatan & Voucher Member' : 'Tambah Tingkatan Member Baru'"
        :subtitle="$isEditing ? 'Perbarui syarat akumulasi belanja, tempelkan voucher eksklusif, dan atur keuntungan tier' : 'Buat tingkatan loyalitas baru untuk pelanggan Storefront Malega Apparel'"
        maxWidth="2xl"
    >
        <form wire:submit.prevent="saveTier" class="space-y-4 text-xs">
            <!-- Grid 1: Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Nama Tingkatan <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Contoh: Gold, VIP Platinum"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                    />
                    @error('name') <span class="text-[10px] text-rose-400 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Min. Belanja Seumur Hidup (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <input
                        type="number"
                        wire:model="min_spend"
                        placeholder="0"
                        min="0"
                        step="10000"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white font-mono placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                    />
                    <span class="text-[10px] text-slate-500 block">Nominal belanja pesanan selesai untuk unlock tier ini</span>
                    @error('min_spend') <span class="text-[10px] text-rose-400 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Grid 2: Voucher Tempelan & Highlight Label -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-[#CBAC70]">
                        🎁 Voucher Khusus Terhubung
                    </label>
                    <select
                        wire:model="voucher_id"
                        class="w-full bg-[#070C1A] border border-[#CBAC70]/40 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-[#CBAC70]"
                    >
                        <option value="">-- Tanpa Voucher Khusus --</option>
                        @foreach($vouchers as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->code }} — {{ $v->name }} ({{ $v->type->value === 'percentage' ? $v->amount . '%' : 'Rp ' . number_format($v->amount, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-500 block">Voucher ini akan otomatis eksklusif untuk member di tier ini</span>
                    @error('voucher_id') <span class="text-[10px] text-rose-400 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Label Highlight Diskon
                    </label>
                    <input
                        type="text"
                        wire:model="discount_label"
                        placeholder="Contoh: Diskon 15% All-Item"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                    />
                    @error('discount_label') <span class="text-[10px] text-rose-400 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Grid 3: Badge Styling & Order -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Teks Badge
                    </label>
                    <input
                        type="text"
                        wire:model="badge_text"
                        placeholder="★ Gold Member"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                    />
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Tema Warna Badge
                    </label>
                    <select
                        wire:model="badge_color"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white focus:outline-none focus:border-[#CBAC70]"
                    >
                        <option value="slate">Slate (Abu Silver)</option>
                        <option value="amber">Amber (Kuning Keemasan)</option>
                        <option value="gold">Gold (Emas Luxury)</option>
                        <option value="emerald">Emerald (Hijau Premium)</option>
                        <option value="rose">Rose (Merah Eksklusif)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                        Urutan Level
                    </label>
                    <input
                        type="number"
                        wire:model="order"
                        min="1"
                        class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white font-mono focus:outline-none focus:border-[#CBAC70]"
                    />
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="space-y-1">
                <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-300">
                    Deskripsi Ringkas Tier
                </label>
                <textarea
                    wire:model="description"
                    rows="2"
                    placeholder="Deskripsi peruntukan level member ini..."
                    class="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                ></textarea>
            </div>

            <!-- Section Perks / Keuntungan Repeater -->
            <div class="space-y-2 pt-2 border-t border-white/10">
                <div class="flex items-center justify-between">
                    <label class="block text-[11px] font-mono font-bold uppercase tracking-wider text-[#CBAC70]">
                        Daftar Keuntungan / Perks Member
                    </label>
                    <button
                        type="button"
                        wire:click="addPerk"
                        class="text-[11px] text-[#CBAC70] hover:text-[#E3CD99] font-bold flex items-center gap-1 cursor-pointer"
                    >
                        <span>+ Tambah Poin Benefit</span>
                    </button>
                </div>

                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    @foreach($perks as $index => $perk)
                        <div class="p-2.5 rounded-xl bg-black/40 border border-white/5 flex items-center gap-2" wire:key="perk-row-{{ $index }}">
                            <div class="w-24 shrink-0">
                                <select
                                    wire:model="perks.{{ $index }}.icon"
                                    class="w-full bg-[#070C1A] border border-white/10 rounded-lg px-2 py-1.5 text-[11px] text-white"
                                >
                                    <option value="gift">🎁 Hadiah/Kupon</option>
                                    <option value="zap">⚡ Kilat/Prioritas</option>
                                    <option value="truck">🚚 Pengiriman</option>
                                    <option value="crown">👑 VIP Mahkota</option>
                                    <option value="sparkles">✨ Kilau/Rilis</option>
                                    <option value="clock">⏱️ Waktu/Early</option>
                                </select>
                            </div>

                            <div class="flex-1 space-y-1">
                                <input
                                    type="text"
                                    wire:model="perks.{{ $index }}.title"
                                    placeholder="Judul Benefit (cth: Diskon 15%)"
                                    class="w-full bg-[#070C1A] border border-white/10 rounded-lg px-2 py-1 text-white text-[11px]"
                                />
                                <input
                                    type="text"
                                    wire:model="perks.{{ $index }}.desc"
                                    placeholder="Keterangan singkat..."
                                    class="w-full bg-[#070C1A] border border-white/10 rounded-lg px-2 py-1 text-slate-300 text-[10px]"
                                />
                            </div>

                            <button
                                type="button"
                                wire:click="removePerk({{ $index }})"
                                class="text-slate-500 hover:text-rose-400 p-1 cursor-pointer"
                                title="Hapus poin ini"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Aktif Toggle -->
            <div class="flex items-center gap-2 pt-2">
                <input
                    type="checkbox"
                    id="tier-is-active"
                    wire:model="is_active"
                    class="rounded border-white/20 bg-slate-900 text-[#CBAC70] focus:ring-[#CBAC70]"
                />
                <label for="tier-is-active" class="text-xs text-slate-300 cursor-pointer">
                    Aktifkan tingkatan member ini di sistem & Storefront
                </label>
            </div>

            <!-- Form Footer -->
            <div class="pt-3 border-t border-white/10 flex items-center justify-end gap-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-tier-modal')"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-5 py-2 rounded-xl text-xs font-bold text-[#0B132B] bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] hover:from-[#E3CD99] hover:to-[#CBAC70] transition shadow-md active:scale-95 cursor-pointer"
                >
                    {{ $isEditing ? 'Simpan Perubahan' : 'Buat Tingkatan' }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal
        id="delete-tier-modal"
        title="Konfirmasi Hapus Tingkatan Member"
        message="Apakah Anda yakin ingin menghapus tingkatan member ini? Pelanggan yang berada di tier ini akan otomatis dialihkan ke tier terdekat."
        confirmText="Hapus Tingkatan"
        cancelText="Batal"
        type="danger"
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="deleteTier"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer bg-rose-600 hover:bg-rose-500 text-white"
                x-on:click="$dispatch('close-confirmation-delete-tier-modal')"
            >
                Hapus Tingkatan
            </button>
        </x-slot:action>
    </x-confirmation-modal>
</div>

<div class="space-y-6">
    <!-- Header Summary Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-[#0B132B]/90 border border-slate-800 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-mono uppercase tracking-wider text-slate-400">Total Pelanggan Terdaftar</p>
                    <p class="text-2xl font-black text-slate-100 mt-1">{{ number_format($totalCustomersCount, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#0B132B]/90 border border-[#CBAC70]/30 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-mono uppercase tracking-wider text-[#CBAC70]">VIP Platinum Members</p>
                    <p class="text-2xl font-black text-[#CBAC70] mt-1">{{ number_format($vipCount, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#CBAC70]/15 border border-[#CBAC70]/40 flex items-center justify-center text-[#CBAC70]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-[#0B132B]/90 border border-emerald-500/30 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-mono uppercase tracking-wider text-emerald-400">Langganan Pemasaran (Opt-In)</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($marketingSubscribersCount, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Pelanggan & Kontak"
        subtitle="Kelola database profil pembeli, CRM pemasaran, segmentasi member, dan broadcast promosi"
        :count="$totalCustomersCount"
    >
        <!-- Primary Header Action -->
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="exportMarketingCsv"
                    class="px-3.5 py-1.5 rounded-lg bg-emerald-600/20 border border-emerald-500/40 hover:bg-emerald-600/30 text-emerald-300 font-semibold text-[11px] shadow-sm transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
                    title="Unduh file CSV untuk WhatsApp / Email Marketing Broadcast"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export CSV Pemasaran</span>
                </button>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-[11px] shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Pelanggan</span>
                </button>
            </div>
        </x-slot:actions>

        <!-- Filter & Control Bar -->
        <x-slot:controls>
            <!-- Search Bar -->
            <div class="relative w-full sm:w-60 lg:w-72">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, WhatsApp, email..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 pl-8 pr-3 text-[11px] text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
            </div>

            <!-- Tier Filter -->
            <select
                wire:model.live="tierFilter"
                class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
            >
                <option value="">Semua Tier</option>
                <option value="Silver">Silver Member</option>
                <option value="Gold">Gold Member</option>
                <option value="VIP Platinum">VIP Platinum</option>
            </select>

            <!-- Marketing Opt-In Filter -->
            <select
                wire:model.live="marketingFilter"
                class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
            >
                <option value="">Semua Status Promo</option>
                <option value="opt_in">Langganan WA/Email (Opt-In)</option>
                <option value="opt_out">Non-Langganan</option>
            </select>

            <!-- Sort By Dropdown -->
            <select
                wire:model.live="sortBy"
                class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
            >
                <option value="latest">Terbaru</option>
                <option value="spend_desc">Belanja Terbanyak</option>
                <option value="orders_desc">Pesanan Terbanyak</option>
            </select>
        </x-slot:controls>

        <!-- Customers Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[11px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-4 py-3 font-medium">Pelanggan</th>
                    <th class="px-4 py-3 font-medium">Kontak & WhatsApp</th>
                    <th class="px-4 py-3 font-medium text-center">Tier Status</th>
                    <th class="px-4 py-3 font-medium text-center">Total Pesanan</th>
                    <th class="px-4 py-3 font-medium text-right">Akumulasi Belanja</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($customers as $customer)
                    <tr wire:key="customer-row-{{ $customer->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Customer Name & Avatar -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#CBAC70] to-[#BD9B58] flex items-center justify-center text-[#0B132B] font-bold text-xs shadow-xs">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-200 text-xs group-hover:text-[#CBAC70] transition-colors">
                                        {{ $customer->name }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-slate-500 text-[10px] font-mono">
                                            Sejak {{ $customer->created_at->format('d M Y') }}
                                        </span>
                                        @if($customer->marketing_opt_in)
                                            <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                                Opt-In WA
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact Details & Direct WhatsApp Action -->
                        <td class="px-4 py-3">
                            <p class="text-slate-300 font-mono text-xs">{{ $customer->email }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <p class="text-slate-400 text-[11px] font-mono">{{ $customer->phone }}</p>
                                <a 
                                    href="{{ $customer->whatsapp_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/25 transition-colors inline-flex items-center gap-1"
                                    title="Kirim pesan WhatsApp ke pelanggan"
                                >
                                    <span>💬 Chat WA</span>
                                </a>
                            </div>
                        </td>

                        <!-- Tier Status Badge -->
                        <td class="px-4 py-3 text-center">
                            @if($customer->membership_tier === 'VIP Platinum')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/40">
                                    💎 VIP Platinum
                                </span>
                            @elseif($customer->membership_tier === 'Gold')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                    ⭐ Gold
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    Silver
                                </span>
                            @endif
                        </td>

                        <!-- Total Orders Count -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-slate-800/80 text-slate-300 border border-slate-700/60">
                                {{ $customer->total_orders_count }} Pesanan
                            </span>
                        </td>

                        <!-- Total Spend Amount -->
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-100 text-sm">
                            {{ $customer->formatted_total_spend }}
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Order History Button -->
                                <button
                                    type="button"
                                    wire:click="openHistoryModal({{ $customer->id }})"
                                    title="Lihat Riwayat Pesanan"
                                    class="p-1 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-white/5 transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>

                                <!-- Edit Customer Button -->
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $customer->id }})"
                                    title="Edit Data Pelanggan"
                                    class="p-1 rounded-lg text-slate-400 hover:text-[#CBAC70] hover:bg-white/5 transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                            Tidak ada data pelanggan yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <!-- Modal Form Tambah / Edit Pelanggan -->
    <x-modal name="customer-modal" title="{{ $customerId ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru' }}" max-width="md">
        <form wire:submit="saveCustomer" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap</label>
                <input
                    type="text"
                    wire:model="name"
                    placeholder="Contoh: Dimas Bagaskara"
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg p-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70]"
                >
                @error('name') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
                <input
                    type="email"
                    wire:model="email"
                    placeholder="Contoh: dimas@example.com"
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg p-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70]"
                >
                @error('email') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">No. WhatsApp / Telepon</label>
                <input
                    type="text"
                    wire:model="phone"
                    placeholder="Contoh: 081234567890"
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg p-2 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70]"
                >
                @error('phone') <span class="text-[10px] text-rose-400">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tier Keanggotaan</label>
                    <select
                        wire:model="membershipTier"
                        class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg p-2 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]"
                    >
                        <option value="Silver">Silver Member</option>
                        <option value="Gold">Gold Member</option>
                        <option value="VIP Platinum">VIP Platinum</option>
                    </select>
                </div>

                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="marketingOptIn"
                            class="rounded border-slate-700 bg-[#070C1A] text-[#CBAC70] focus:ring-[#CBAC70]"
                        >
                        <span class="text-xs text-slate-300">Opt-in Promo WA</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-800">
                <button
                    type="button"
                    @click="closeModal()"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] text-[#0B132B] font-bold text-xs"
                >
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Riwayat Pesanan Pelanggan -->
    <x-modal name="customer-history-modal" title="Riwayat Belanja Pelanggan" max-width="lg">
        @if($activeCustomer)
            <div class="space-y-4">
                <div class="p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-100">{{ $activeCustomer->name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $activeCustomer->email }} • {{ $activeCustomer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-[#CBAC70] font-bold">{{ $activeCustomer->membership_tier }}</p>
                        <p class="text-xs font-mono text-slate-300">Total: {{ $activeCustomer->formatted_total_spend }}</p>
                    </div>
                </div>

                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse($activeCustomer->orders as $order)
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-mono font-bold text-slate-200">{{ $order->order_number }}</p>
                                <p class="text-[10px] text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} • {{ $order->items->count() }} item</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $order->status->badgeClasses() }}">
                                    {{ $order->status->label() }}
                                </span>
                                <p class="text-xs font-mono font-bold text-[#CBAC70] mt-0.5">{{ $order->formatted_total_amount }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat pesanan.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </x-modal>
</div>

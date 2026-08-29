<div class="space-y-6">
    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Pelanggan & Kontak"
        subtitle="Buku profil pembeli, akumulasi belanja, dan riwayat pesanan seumur hidup pelanggan Malega Apparel"
        :count="$totalCustomersCount"
    >
        <!-- Filter & Control Bar -->
        <x-slot:controls>
            <!-- Search Bar -->
            <div class="relative w-full sm:w-64">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, email, WhatsApp..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl py-2 pl-10 pr-4 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
            </div>

            <!-- Sort By Dropdown -->
            <select
                wire:model.live="sortBy"
                class="bg-[#070C1A] border border-slate-700/80 rounded-xl py-2 px-3 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
            >
                <option value="latest">Terbaru Terdaftar</option>
                <option value="spend_desc">Total Belanja Tertinggi</option>
                <option value="orders_desc">Pesanan Terbanyak</option>
            </select>

            <!-- Add Customer Button -->
            <button
                type="button"
                wire:click="openCreateModal"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Pelanggan</span>
            </button>
        </x-slot:controls>

        <!-- Customers Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[11px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-5 py-3.5 font-medium">Pelanggan</th>
                    <th class="px-5 py-3.5 font-medium">Kontak</th>
                    <th class="px-5 py-3.5 font-medium text-center">Total Pesanan</th>
                    <th class="px-5 py-3.5 font-medium text-right">Akumulasi Belanja</th>
                    <th class="px-5 py-3.5 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($customers as $customer)
                    <tr wire:key="customer-row-{{ $customer->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Customer Name & Avatar -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#CBAC70] to-[#BD9B58] flex items-center justify-center text-[#0B132B] font-bold text-xs shadow-xs">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-200 text-xs group-hover:text-[#CBAC70] transition-colors">
                                        {{ $customer->name }}
                                    </p>
                                    <p class="text-slate-500 text-[10px] font-mono mt-0.5">
                                        Bergabung {{ $customer->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Contact Details -->
                        <td class="px-5 py-4">
                            <p class="text-slate-300 font-mono text-xs">{{ $customer->email }}</p>
                            <p class="text-slate-400 text-[11px] mt-0.5 font-mono">{{ $customer->phone }}</p>
                        </td>

                        <!-- Total Orders Count -->
                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-slate-800/80 text-slate-300 border border-slate-700/60">
                                {{ $customer->total_orders_count }} Pesanan
                            </span>
                        </td>

                        <!-- Total Spend Amount -->
                        <td class="px-5 py-4 text-right font-mono font-bold text-slate-100 text-sm">
                            {{ $customer->formatted_total_spend }}
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Order History Button -->
                                <button
                                    type="button"
                                    wire:click="openHistoryModal({{ $customer->id }})"
                                    title="Lihat Riwayat Pesanan"
                                    class="px-2.5 py-1.5 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer inline-flex items-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 text-[#CBAC70]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Riwayat</span>
                                </button>

                                <!-- Edit Button -->
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $customer->id }})"
                                    title="Edit Data Pelanggan"
                                    class="p-1.5 text-slate-400 hover:text-[#CBAC70] hover:bg-white/5 rounded-lg transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-200">Belum ada pelanggan ditemukan</p>
                                <p class="text-xs text-slate-500 mt-1">Pelanggan akan otomatis tercatat saat melakukan checkout pesanan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Custom Themed Pagination Slot -->
        <x-slot:pagination>
            {{ $customers->links() }}
        </x-slot:pagination>
    </x-table-card>

    <!-- Reusable Customer Create/Edit Modal -->
    <x-modal
        id="customer-modal"
        :title="$customerId ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'"
        subtitle="Kelola profil kontak dan informasi dasar pembeli"
        maxWidth="md"
    >
        <form wire:submit="saveCustomer" class="space-y-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" wire:model="name" placeholder="Nama Lengkap" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                @error('name') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">Alamat Email <span class="text-rose-400">*</span></label>
                <input type="email" wire:model="email" placeholder="customer@mail.com" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                @error('email') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-300">No. WhatsApp / Telepon <span class="text-rose-400">*</span></label>
                <input type="text" wire:model="phone" placeholder="08123456789" required class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-[#CBAC70]">
                @error('phone') <p class="text-[11px] text-rose-400">{{ $message }}</p> @enderror
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-customer-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveCustomer">
                        {{ $customerId ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}
                    </span>
                    <span wire:loading.inline-flex wire:target="saveCustomer" class="items-center gap-1.5">
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

    <!-- Reusable Customer Lifetime Order History Modal -->
    <x-modal
        id="customer-history-modal"
        :title="'Riwayat Transaksi — ' . ($activeCustomer?->name ?? '')"
        subtitle="Seluruh pesanan seumur hidup dan akumulasi belanja pelanggan"
        maxWidth="3xl"
    >
        @if($activeCustomer)
            <div class="space-y-4">
                <!-- Customer Summary Info Card -->
                <div class="p-4 rounded-2xl bg-[#070C1A] border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-sm text-slate-100">{{ $activeCustomer->name }}</p>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $activeCustomer->email }} &bull; {{ $activeCustomer->phone }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-mono text-slate-400 uppercase">Akumulasi Belanja</p>
                        <p class="font-mono font-bold text-base text-[#CBAC70]">{{ $activeCustomer->formatted_total_spend }}</p>
                    </div>
                </div>

                <!-- Orders List Table -->
                <div class="rounded-xl border border-slate-800 bg-[#070C1A] overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-white/[0.02] border-b border-slate-800 font-mono text-[10px] text-slate-400 uppercase">
                            <tr>
                                <th class="px-4 py-3">No. Pesanan</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Total Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50">
                            @forelse($activeCustomer->orders as $ord)
                                <tr class="hover:bg-white/[0.01]">
                                    <td class="px-4 py-3 font-mono font-bold text-[#CBAC70]">
                                        {{ $ord->order_number }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">
                                        {{ $ord->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $ord->order_status->badgeClasses() }}">
                                            {{ $ord->order_status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-slate-100">
                                        {{ $ord->formatted_grand_total }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-slate-500 text-xs">
                                        Pelanggan ini belum memiliki riwayat pesanan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Close Button -->
                <div class="pt-2 flex justify-end">
                    <button
                        type="button"
                        x-on:click="$dispatch('close-modal-customer-history-modal')"
                        class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                    >
                        Tutup Riwayat
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>

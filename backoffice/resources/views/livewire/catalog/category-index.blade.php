<div class="space-y-6">
    <!-- Double-Card Wrapped Table Container -->
    <x-table-card
        title="Manajemen Kategori"
        subtitle="Kelola klasifikasi hierarki lini produk busana Malega Apparel"
        :count="$totalCount"
    >
        <!-- Filter & Control Bar (Full-Width Single Row) -->
        <x-slot:controls>
            <!-- Search Bar -->
            <div class="relative w-full sm:w-60 lg:w-72">
                <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Cari kategori..." 
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 pl-8 pr-3 text-[11px] text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                >
            </div>

            <!-- Status Filter -->
            <select
                wire:model.live="statusFilter"
                class="bg-[#070C1A] border border-slate-700/80 rounded-lg py-1.5 px-2.5 text-[11px] text-slate-300 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors cursor-pointer"
            >
                <option value="all">Semua Status</option>
                <option value="active">Hanya Aktif</option>
                <option value="inactive">Hanya Nonaktif</option>
            </select>
        </x-slot:controls>

        <x-slot:actions>
                <!-- Create Category Button -->
                <button 
                    type="button"
                    wire:click="openCreateModal"
                    class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-[11px] shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Kategori</span>
                </button>
        </x-slot:actions>

        <!-- Category Table Body -->
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="text-[10px] font-mono text-slate-400 uppercase tracking-wider border-b border-slate-800/80 bg-white/[0.02]">
                    <th class="px-4 py-3 font-medium w-12 text-center">No</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium hidden sm:table-cell">Slug</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 font-medium text-center">Produk</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($categories as $category)
                    <tr wire:key="cat-row-{{ $category->id }}" class="hover:bg-white/[0.02] transition-colors group">
                        <!-- Sort Order -->
                        <td class="px-4 py-3 text-center font-mono text-slate-400">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded-md bg-slate-800/80 text-slate-300 font-semibold text-[10px]">
                                {{ $category->sort_order }}
                            </span>
                        </td>

                        <!-- Category Name & Description -->
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-100 group-hover:text-[#CBAC70] transition-colors text-[13px]">
                                    {{ $category->name }}
                                </p>
                                @if($category->description)
                                    <p class="text-slate-400 text-[10px] truncate max-w-xs mt-0.5">{{ $category->description }}</p>
                                @endif
                            </div>
                        </td>

                        <!-- Slug -->
                        <td class="px-4 py-3 hidden sm:table-cell font-mono text-[10px] text-[#CBAC70]/80">
                            {{ $category->slug }}
                        </td>

                        <!-- Status Badge -->
                        <td class="px-4 py-3 text-center">
                            <button
                                type="button"
                                wire:click="toggleStatus({{ $category->id }})"
                                title="Klik untuk mengubah status"
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold cursor-pointer transition-all {{ $category->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700' }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-400 shadow-[0_0_6px_rgba(52,211,153,0.8)]' : 'bg-slate-500' }}"></span>
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>

                        <!-- Products Count Badge -->
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-mono font-medium {{ $category->products_count > 0 ? 'bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30' : 'bg-slate-800 text-slate-500' }}">
                                {{ $category->products_count }} Produk
                            </span>
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Edit Button -->
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $category->id }})"
                                    title="Edit Kategori"
                                    class="p-1.5 text-slate-400 hover:text-[#CBAC70] hover:bg-white/5 rounded-lg transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $category->id }})"
                                    title="Hapus Kategori"
                                    class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-lg transition-colors cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800/80 flex items-center justify-center text-slate-500 mb-3">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-200">Tidak ada kategori ditemukan</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau tambahkan kategori baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Custom Themed Pagination Slot -->
        <x-slot:pagination>
            {{ $categories->links() }}
        </x-slot:pagination>
    </x-table-card>

    <!-- Reusable Create & Edit Category Modal -->
    <x-modal
        id="category-modal"
        :title="$isEditing ? 'Edit Kategori Produk' : 'Tambah Kategori Baru'"
        :subtitle="$isEditing ? 'Perbarui informasi dan urutan kategori' : 'Buat kategori baru untuk mengelompokkan koleksi busana'"
        maxWidth="lg"
    >
        <form wire:submit="saveCategory" class="space-y-4">
            <!-- Name -->
            <x-input
                wire:model="name"
                label="Nama Kategori"
                name="name"
                placeholder="misal: Outerwear & Jaket"
                required="true"
                autofocus
            />

            <!-- Slug -->
            <x-input
                wire:model="slug"
                label="Slug URL (Opsional — otomatis dibuat jika kosong)"
                name="slug"
                placeholder="misal: outerwear-jaket"
            />

            <!-- Description -->
            <div class="space-y-1.5">
                <label for="cat-description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea
                    id="cat-description"
                    wire:model="description"
                    rows="3"
                    placeholder="Tuliskan deskripsi singkat kategori busana ini..."
                    class="w-full bg-[#070C1A] border border-slate-700/80 rounded-xl p-3 text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70] transition-colors"
                ></textarea>
                @error('description')
                    <p class="text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order & Active Toggle in 2-Columns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <x-input
                    wire:model="sort_order"
                    label="Nomor Urutan Tampil"
                    name="sort_order"
                    type="number"
                    min="0"
                    required="true"
                />

                <div class="flex flex-col justify-center pt-5">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70] focus:ring-offset-0 focus:ring-1 transition-colors"
                        >
                        <span class="text-xs font-semibold text-slate-200">Kategori Aktif</span>
                    </label>
                    <p class="text-[10px] text-slate-400 mt-1 pl-7">Kategori aktif dapat langsung dihubungkan dengan produk.</p>
                </div>
            </div>

            <!-- Modal Action Buttons Footer -->
            <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal-category-modal')"
                    class="px-4 py-2 rounded-xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition-colors cursor-pointer"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-xs shadow-md shadow-[#CBAC70]/10 transition-all cursor-pointer disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveCategory">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Buat Kategori' }}
                    </span>
                    <span wire:loading.inline-flex wire:target="saveCategory" class="items-center gap-1.5">
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

    <!-- Reusable Delete Confirmation Modal -->
    <x-confirmation-modal
        id="delete-category-modal"
        title="Konfirmasi Hapus Kategori"
        message="Apakah Anda yakin ingin menghapus kategori ini?"
        confirmText="Ya, Hapus Kategori"
        cancelText="Batal"
        type="danger"
        icon="delete"
    >
        <x-slot:action>
            <button
                type="button"
                wire:click="deleteCategory"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-950/30 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="deleteCategory">Ya, Hapus Sekarang</span>
                <span wire:loading.inline-flex wire:target="deleteCategory" class="items-center gap-1.5">
                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Menghapus...</span>
                </span>
            </button>
        </x-slot:action>
    </x-confirmation-modal>
</div>

<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\DeleteCategoryAction;
use App\Actions\Catalog\UpdateCategoryAction;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Katalog Kategori | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    // Modal Form State
    public bool $isEditing = false;

    public ?int $editingCategoryId = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public int $sort_order = 0;

    public bool $is_active = true;

    // Delete Modal State
    public ?int $deletingCategoryId = null;

    public string $deletingCategoryName = '';

    /**
     * Define the custom pagination view.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    /**
     * Reset pagination when search or filters change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open create category modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['editingCategoryId', 'name', 'slug', 'description', 'sort_order']);
        $this->is_active = true;
        $this->isEditing = false;

        $this->dispatch('open-modal-category-modal');
    }

    /**
     * Open edit category modal with existing data.
     */
    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $category = Category::findOrFail($id);

        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->sort_order = $category->sort_order;
        $this->is_active = $category->is_active;
        $this->isEditing = true;

        $this->dispatch('open-modal-category-modal');
    }

    /**
     * Save category (create or update).
     */
    public function saveCategory(CreateCategoryAction $createAction, UpdateCategoryAction $updateAction): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $payload = [
            'name' => $this->name,
            'slug' => $this->slug ?: null,
            'description' => $this->description ?: null,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing && $this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $updateAction->execute($category, $payload);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Kategori Diperbarui',
                'message' => "Kategori '{$this->name}' berhasil diperbarui.",
            ]);
        } else {
            $createAction->execute($payload);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Kategori Ditambahkan',
                'message' => "Kategori '{$this->name}' berhasil dibuat.",
            ]);
        }

        $this->dispatch('close-modal-category-modal');
        $this->reset(['editingCategoryId', 'name', 'slug', 'description', 'sort_order', 'is_active', 'isEditing']);
    }

    /**
     * Confirm category deletion.
     */
    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;

        $this->dispatch('open-confirmation-delete-category-modal', [
            'title' => 'Konfirmasi Hapus Kategori',
            'message' => "Apakah Anda yakin ingin menghapus kategori '{$category->name}'? Tindakan ini tidak dapat dibatalkan.",
        ]);
    }

    /**
     * Execute category deletion.
     */
    public function deleteCategory(DeleteCategoryAction $deleteAction): void
    {
        if (! $this->deletingCategoryId) {
            return;
        }

        $category = Category::find($this->deletingCategoryId);
        if (! $category) {
            return;
        }

        try {
            $name = $category->name;
            $deleteAction->execute($category);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Kategori Dihapus',
                'message' => "Kategori '{$name}' telah dihapus.",
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Menghapus',
                'message' => $e->validator->errors()->first('category'),
            ]);
        }

        $this->reset(['deletingCategoryId', 'deletingCategoryName']);
        $this->dispatch('close-confirmation-delete-category-modal');
    }

    /**
     * Toggle active status directly from table.
     */
    public function toggleStatus(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);

        $statusText = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Status Diperbarui',
            'message' => "Kategori '{$category->name}' telah {$statusText}.",
        ]);
    }

    public function render()
    {
        $query = Category::withCount('products')
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $term)->orWhere('slug', 'like', $term));
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('sort_order')
            ->orderBy('name');

        $categories = $query->paginate(15);
        $totalCount = Category::count();

        return view('livewire.catalog.category-index', [
            'categories' => $categories,
            'totalCount' => $totalCount,
        ]);
    }
}

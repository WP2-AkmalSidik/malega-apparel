<?php

namespace App\Livewire\Catalog;

use App\Models\FabricSpecification;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Spesifikasi Bahan & Konstruksi | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class FabricSpecIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    // Modal Form State
    public bool $isEditing = false;

    public ?int $editingSpecId = null;

    public string $name = '';

    public string $brand = 'Malega Apparel';

    public string $gramasi = '';

    public string $material = '';

    public string $fit_cutting = '';

    public string $collar_hood = '';

    public string $care_instructions = '';

    public bool $is_active = true;

    // Apply to Products Modal State
    public ?int $applyingSpecId = null;

    public string $applyingSpecName = '';

    /**
     * Array of selected product IDs to apply this spec to.
     *
     * @var array<int, int>
     */
    public array $selectedProductIds = [];

    public string $productSearch = '';

    // Delete Modal State
    public ?int $deletingSpecId = null;

    public string $deletingSpecName = '';

    /**
     * Custom pagination view.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open create specification modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'editingSpecId',
            'name',
            'gramasi',
            'material',
            'fit_cutting',
            'collar_hood',
            'care_instructions',
        ]);
        $this->brand = 'Malega Apparel';
        $this->is_active = true;
        $this->isEditing = false;

        $this->dispatch('open-modal-spec-modal');
    }

    /**
     * Open edit specification modal.
     */
    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $spec = FabricSpecification::findOrFail($id);

        $this->editingSpecId = $spec->id;
        $this->name = $spec->name;
        $this->brand = $spec->brand ?: 'Malega Apparel';
        $this->gramasi = $spec->gramasi;
        $this->material = $spec->material;
        $this->fit_cutting = $spec->fit_cutting ?? '';
        $this->collar_hood = $spec->collar_hood ?? '';
        $this->care_instructions = $spec->care_instructions ?? '';
        $this->is_active = $spec->is_active;
        $this->isEditing = true;

        $this->dispatch('open-modal-spec-modal');
    }

    /**
     * Save specification (create or update).
     */
    public function saveSpec(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'gramasi' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'fit_cutting' => 'nullable|string|max:255',
            'collar_hood' => 'nullable|string|max:255',
            'care_instructions' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'brand' => $this->brand,
            'gramasi' => $this->gramasi,
            'material' => $this->material,
            'fit_cutting' => $this->fit_cutting,
            'collar_hood' => $this->collar_hood,
            'care_instructions' => $this->care_instructions,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing && $this->editingSpecId) {
            $spec = FabricSpecification::findOrFail($this->editingSpecId);
            $spec->update($data);

            // Update all attached products with fresh specifications array
            $structuredSpecs = $spec->toProductSpecifications();
            Product::where('fabric_spec_id', $spec->id)->update([
                'specifications' => $structuredSpecs,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Spesifikasi Diperbarui',
                'message' => "Spesifikasi \"{$this->name}\" berhasil disimpan dan disinkronkan ke produk terkait.",
            ]);
        } else {
            FabricSpecification::create($data);

            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Spesifikasi Dibuat',
                'message' => "Spesifikasi \"{$this->name}\" berhasil ditambahkan.",
            ]);
        }

        $this->dispatch('close-modal-spec-modal');
    }

    /**
     * Open Apply Spec to Products modal.
     */
    public function openApplyModal(int $id): void
    {
        $spec = FabricSpecification::findOrFail($id);
        $this->applyingSpecId = $spec->id;
        $this->applyingSpecName = $spec->name;
        $this->productSearch = '';

        // Pre-select products that currently have this fabric spec
        $this->selectedProductIds = Product::where('fabric_spec_id', $spec->id)->pluck('id')->toArray();

        $this->dispatch('open-modal-apply-modal');
    }

    /**
     * Save applied specification to selected products.
     */
    public function applyToProducts(): void
    {
        if (! $this->applyingSpecId) {
            return;
        }

        $spec = FabricSpecification::findOrFail($this->applyingSpecId);
        $structuredSpecs = $spec->toProductSpecifications();

        // 1. Remove spec from unselected products that previously had it
        Product::where('fabric_spec_id', $spec->id)
            ->whereNotIn('id', $this->selectedProductIds)
            ->update([
                'fabric_spec_id' => null,
            ]);

        // 2. Apply spec to all selected products
        if (! empty($this->selectedProductIds)) {
            Product::whereIn('id', $this->selectedProductIds)->update([
                'fabric_spec_id' => $spec->id,
                'specifications' => $structuredSpecs,
            ]);
        }

        $count = count($this->selectedProductIds);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Spesifikasi Diterapkan',
            'message' => "Spesifikasi \"{$spec->name}\" berhasil diterapkan ke {$count} produk.",
        ]);

        $this->dispatch('close-modal-apply-modal');
    }

    /**
     * Open delete confirmation modal.
     */
    public function openDeleteModal(int $id): void
    {
        $spec = FabricSpecification::findOrFail($id);
        $this->deletingSpecId = $spec->id;
        $this->deletingSpecName = $spec->name;

        $this->dispatch('open-modal-delete-spec-modal');
    }

    /**
     * Delete specification.
     */
    public function deleteSpec(): void
    {
        if ($this->deletingSpecId) {
            $spec = FabricSpecification::findOrFail($this->deletingSpecId);
            $name = $spec->name;

            // Detach from products first
            Product::where('fabric_spec_id', $spec->id)->update(['fabric_spec_id' => null]);
            $spec->delete();

            $this->dispatch('toast', [
                'type' => 'info',
                'title' => 'Spesifikasi Dihapus',
                'message' => "Spesifikasi \"{$name}\" telah dihapus.",
            ]);
        }

        $this->dispatch('close-modal-delete-spec-modal');
    }

    public function render()
    {
        $query = FabricSpecification::withCount('products');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('material', 'like', '%'.$this->search.'%')
                    ->orWhere('gramasi', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $specs = $query->latest()->paginate(10);

        // Products query for Apply modal
        $availableProducts = Product::select('id', 'name', 'category_id', 'fabric_spec_id')
            ->with('category')
            ->when($this->productSearch, fn ($q) => $q->where('name', 'like', '%'.$this->productSearch.'%'))
            ->orderBy('name')
            ->get();

        return view('livewire.catalog.fabric-spec-index', [
            'specs' => $specs,
            'availableProducts' => $availableProducts,
        ]);
    }
}

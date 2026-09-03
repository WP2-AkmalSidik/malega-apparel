<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Master Koleksi & Lookbook | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class CollectionIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $seasonFilter = 'all';

    // Modal Form State (Create / Edit)
    public bool $isEditing = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $subtitle = '';

    public string $slug = '';

    public string $season = 'Spring / Summer';

    public string $release_year = '2026';

    public string $badge = 'SS26 DROP';

    public string $featured_material = '';

    public ?int $gsm_weight = null;

    public string $description = '';

    public string $storytelling = '';

    public string $paletteInput = '#0B132B, #CBAC70, #1E293B';

    public string $tagsInput = 'SS26, Limited Drop, Heavyweight';

    public string $cover_image = '';

    public string $banner_image = '';

    public bool $is_active = true;

    // Attach Products Modal State
    public ?int $managingCollectionId = null;

    public string $managingCollectionName = '';

    public array $selectedProductIds = [];

    public string $productSearch = '';

    public string $productCategoryFilter = 'all';

    // Delete Modal State
    public ?int $deletingId = null;

    public string $deletingName = '';

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

    public function updatedSeasonFilter(): void
    {
        $this->resetPage();
    }

    public function updatedName(): void
    {
        if (! $this->isEditing || empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal(): void
    {
        $this->resetErrorBag();
        $this->reset([
            'isEditing',
            'editingId',
            'name',
            'subtitle',
            'slug',
            'season',
            'release_year',
            'badge',
            'featured_material',
            'gsm_weight',
            'description',
            'storytelling',
            'paletteInput',
            'tagsInput',
            'cover_image',
            'banner_image',
            'is_active',
        ]);

        $this->season = 'Spring / Summer';
        $this->release_year = '2026';
        $this->badge = 'SS26 DROP';
        $this->paletteInput = '#0B132B, #CBAC70, #1E293B';
        $this->tagsInput = 'SS26, Limited Drop, Heavyweight';
        $this->is_active = true;

        $this->dispatch('open-modal', id: 'collection-form-modal');
    }

    public function openEditModal(int $id): void
    {
        $this->resetErrorBag();
        $collection = Collection::findOrFail($id);

        $this->isEditing = true;
        $this->editingId = $collection->id;
        $this->name = $collection->name;
        $this->subtitle = $collection->subtitle ?? '';
        $this->slug = $collection->slug;
        $this->season = $collection->season ?? 'Spring / Summer';
        $this->release_year = $collection->release_year ?? '2026';
        $this->badge = $collection->badge ?? '';
        $this->featured_material = $collection->featured_material ?? '';
        $this->gsm_weight = $collection->gsm_weight;
        $this->description = $collection->description ?? '';
        $this->storytelling = $collection->storytelling ?? '';
        $this->paletteInput = is_array($collection->palette) ? implode(', ', $collection->palette) : '';
        $this->tagsInput = is_array($collection->tags) ? implode(', ', $collection->tags) : '';
        $this->cover_image = $collection->cover_image ?? '';
        $this->banner_image = $collection->banner_image ?? '';
        $this->is_active = (bool) $collection->is_active;

        $this->dispatch('open-modal', id: 'collection-form-modal');
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:150',
            'slug' => 'required|string|max:150|unique:collections,slug,'.($this->editingId ?? 'NULL').',id',
            'subtitle' => 'nullable|string|max:255',
            'season' => 'required|string|max:100',
            'release_year' => 'required|string|max:20',
            'badge' => 'nullable|string|max:50',
            'featured_material' => 'nullable|string|max:150',
            'gsm_weight' => 'nullable|integer|min:50|max:1000',
            'description' => 'nullable|string|max:1000',
            'storytelling' => 'nullable|string|max:2000',
            'paletteInput' => 'nullable|string|max:255',
            'tagsInput' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:500',
            'banner_image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];

        $this->validate($rules);

        $palette = array_values(array_filter(array_map('trim', explode(',', $this->paletteInput))));
        $tags = array_values(array_filter(array_map('trim', explode(',', $this->tagsInput))));

        $data = [
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'slug' => Str::slug($this->slug ?: $this->name),
            'season' => $this->season,
            'release_year' => $this->release_year,
            'badge' => $this->badge,
            'featured_material' => $this->featured_material,
            'gsm_weight' => $this->gsm_weight,
            'description' => $this->description,
            'storytelling' => $this->storytelling,
            'palette' => $palette,
            'tags' => $tags,
            'cover_image' => $this->cover_image ?: ($this->banner_image ?: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80'),
            'banner_image' => $this->banner_image ?: ($this->cover_image ?: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80'),
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing && $this->editingId) {
            $collection = Collection::findOrFail($this->editingId);
            $collection->update($data);
            $message = "Koleksi '{$collection->name}' berhasil diperbarui.";
        } else {
            $collection = Collection::create($data);
            $message = "Koleksi '{$collection->name}' berhasil ditambahkan ke katalog.";
        }

        $this->dispatch('close-modal', id: 'collection-form-modal');
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function toggleStatus(int $id): void
    {
        $collection = Collection::findOrFail($id);
        $collection->update(['is_active' => ! $collection->is_active]);

        $statusLabel = $collection->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('notify', type: 'success', message: "Status koleksi '{$collection->name}' berhasil {$statusLabel}.");
    }

    public function openProductsModal(int $id): void
    {
        $collection = Collection::with('products:id,name,category_id')->findOrFail($id);
        $this->managingCollectionId = $collection->id;
        $this->managingCollectionName = $collection->name;
        $this->selectedProductIds = $collection->products->pluck('id')->toArray();
        $this->productSearch = '';
        $this->productCategoryFilter = 'all';

        $this->dispatch('open-modal', id: 'manage-products-modal');
    }

    public function toggleProductSelection(int $productId): void
    {
        if (in_array($productId, $this->selectedProductIds)) {
            $this->selectedProductIds = array_values(array_diff($this->selectedProductIds, [$productId]));
        } else {
            $this->selectedProductIds[] = $productId;
        }
    }

    public function saveCollectionProducts(): void
    {
        if (! $this->managingCollectionId) {
            return;
        }

        $collection = Collection::findOrFail($this->managingCollectionId);
        $collection->products()->sync($this->selectedProductIds);

        $count = count($this->selectedProductIds);
        $this->dispatch('close-modal', id: 'manage-products-modal');
        $this->dispatch('notify', type: 'success', message: "Berhasil menghubungkan {$count} produk ke koleksi '{$collection->name}'.");
    }

    public function detachProduct(int $collectionId, int $productId): void
    {
        $collection = Collection::findOrFail($collectionId);
        $collection->products()->detach($productId);

        $product = Product::find($productId);
        $productName = $product ? $product->name : 'Produk';

        $this->dispatch('notify', type: 'success', message: "'{$productName}' berhasil dicabut dari koleksi '{$collection->name}'.");
    }

    public function confirmDelete(int $id): void
    {
        $collection = Collection::findOrFail($id);
        $this->deletingId = $collection->id;
        $this->deletingName = $collection->name;

        $this->dispatch('open-modal', id: 'delete-collection-modal');
    }

    public function deleteCollection(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $collection = Collection::findOrFail($this->deletingId);
        $collection->products()->detach();
        $collection->delete();

        $this->dispatch('close-modal', id: 'delete-collection-modal');
        $this->dispatch('notify', type: 'success', message: "Koleksi '{$this->deletingName}' berhasil dihapus.");

        $this->reset(['deletingId', 'deletingName']);
    }

    public function render()
    {
        $collections = Collection::query()
            ->withCount('products')
            ->with(['products:id,name,fabric_spec_id,category_id'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('subtitle', 'like', $term)
                        ->orWhere('season', 'like', $term)
                        ->orWhere('badge', 'like', $term)
                        ->orWhere('featured_material', 'like', $term);
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->seasonFilter !== 'all', function ($q) {
                $q->where('season', $this->seasonFilter);
            })
            ->latest('id')
            ->paginate(8);

        // Stats calculation
        $totalCollections = Collection::count();
        $activeCollections = Collection::where('is_active', true)->count();
        $totalAttachedProducts = \Illuminate\Support\Facades\DB::table('collection_product')->distinct('product_id')->count('product_id');

        // Available products for binding modal
        $availableProducts = Product::active()
            ->with('category:id,name')
            ->when($this->productSearch, function ($q) {
                $term = '%'.$this->productSearch.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('material', 'like', $term);
                });
            })
            ->when($this->productCategoryFilter !== 'all', function ($q) {
                $q->where('category_id', $this->productCategoryFilter);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $seasonsList = Collection::distinct()->pluck('season')->filter()->values();

        return view('livewire.catalog.collection-index', [
            'collections' => $collections,
            'totalCollections' => $totalCollections,
            'activeCollections' => $activeCollections,
            'totalAttachedProducts' => $totalAttachedProducts,
            'availableProducts' => $availableProducts,
            'categories' => $categories,
            'seasonsList' => $seasonsList,
        ]);
    }
}

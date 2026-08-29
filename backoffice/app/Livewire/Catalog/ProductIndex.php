<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\CreateProductAction;
use App\Actions\Catalog\DeleteProductAction;
use App\Actions\Catalog\UpdateProductAction;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Katalog Produk | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $statusFilter = 'all';

    // Modal Form State
    public bool $isEditing = false;

    public ?int $editingProductId = null;

    public ?int $category_id = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public string $status = 'active';

    public ?string $featured_image = null;

    /**
     * List of variants for the product form.
     *
     * @var array<int, array{id?: int|null, sku: string, title: string, price: int|string, compare_at_price?: int|string|null, cost_price?: int|string|null, weight_grams: int, is_active: bool}>
     */
    public array $variants = [];

    // Delete Modal State
    public ?int $deletingProductId = null;

    public string $deletingProductName = '';

    /**
     * Define the custom pagination view.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.custom';
    }

    /**
     * Reset pagination on search or filter change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Add an empty variant row to the form.
     */
    public function addVariant(): void
    {
        $this->variants[] = [
            'id' => null,
            'sku' => '',
            'title' => 'Standard / All Size',
            'price' => 0,
            'compare_at_price' => null,
            'cost_price' => null,
            'weight_grams' => 250,
            'is_active' => true,
        ];
    }

    /**
     * Remove a variant row from the form.
     */
    public function removeVariant(int $index): void
    {
        if (count($this->variants) > 1) {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        } else {
            $this->dispatch('toast', [
                'type' => 'warning',
                'title' => 'Perhatian',
                'message' => 'Produk wajib memiliki minimal satu varian SKU.',
            ]);
        }
    }

    /**
     * Quick generator for standard sizes (S, M, L, XL).
     */
    public function generateStandardSizes(): void
    {
        $baseSku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name ?: 'MLG'), 0, 6));
        $defaultPrice = ! empty($this->variants[0]['price']) ? (int) $this->variants[0]['price'] : 299000;

        $this->variants = [
            ['id' => null, 'sku' => "{$baseSku}-S", 'title' => 'Ukuran S', 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-M", 'title' => 'Ukuran M', 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-L", 'title' => 'Ukuran L', 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-XL", 'title' => 'Ukuran XL', 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
        ];

        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Varian Digenerate',
            'message' => '4 varian ukuran standar (S, M, L, XL) berhasil dibuat.',
        ]);
    }

    /**
     * Open create product modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $firstCategory = Category::active()->orderBy('sort_order')->first();

        $this->reset(['editingProductId', 'name', 'slug', 'description', 'featured_image']);
        $this->category_id = $firstCategory?->id;
        $this->status = 'active';
        $this->isEditing = false;

        $this->variants = [
            [
                'id' => null,
                'sku' => '',
                'title' => 'All Size / Standar',
                'price' => 299000,
                'compare_at_price' => null,
                'cost_price' => null,
                'weight_grams' => 250,
                'is_active' => true,
            ],
        ];

        $this->dispatch('open-modal-product-modal');
    }

    /**
     * Open edit product modal with existing data and variants.
     */
    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $product = Product::with(['variants'])->findOrFail($id);

        $this->editingProductId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->status = $product->status->value;
        $this->featured_image = $product->featured_image;
        $this->isEditing = true;

        $this->variants = $product->variants->map(fn ($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'title' => $v->title,
            'price' => $v->price,
            'compare_at_price' => $v->compare_at_price,
            'cost_price' => $v->cost_price,
            'weight_grams' => $v->weight_grams,
            'is_active' => $v->is_active,
        ])->toArray();

        if (empty($this->variants)) {
            $this->addVariant();
        }

        $this->dispatch('open-modal-product-modal');
    }

    /**
     * Save product (create or update).
     */
    public function saveProduct(CreateProductAction $createAction, UpdateProductAction $updateAction): void
    {
        $this->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,active,inactive,archived'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:64'],
            'variants.*.title' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.weight_grams' => ['required', 'integer', 'min:1'],
            'variants.*.is_active' => ['boolean'],
        ]);

        $payload = [
            'category_id' => (int) $this->category_id,
            'name' => $this->name,
            'slug' => $this->slug ?: null,
            'description' => $this->description ?: null,
            'status' => ProductStatus::from($this->status),
            'featured_image' => $this->featured_image ?: null,
            'variants' => $this->variants,
        ];

        try {
            if ($this->isEditing && $this->editingProductId) {
                $product = Product::findOrFail($this->editingProductId);
                $updateAction->execute($product, $payload);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'title' => 'Produk Diperbarui',
                    'message' => "Produk '{$this->name}' beserta variannya berhasil diperbarui.",
                ]);
            } else {
                $createAction->execute($payload);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'title' => 'Produk Ditambahkan',
                    'message' => "Produk '{$this->name}' beserta variannya berhasil dibuat.",
                ]);
            }

            $this->dispatch('close-modal-product-modal');
            $this->reset(['editingProductId', 'category_id', 'name', 'slug', 'description', 'status', 'featured_image', 'variants', 'isEditing']);
        } catch (ValidationException $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan',
                'message' => $e->validator->errors()->first('variants') ?? 'Pastikan seluruh kolom varian valid.',
            ]);
        }
    }

    /**
     * Confirm product deletion.
     */
    public function confirmDelete(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->name;

        $this->dispatch('open-confirmation-delete-product-modal', [
            'title' => 'Konfirmasi Hapus Produk',
            'message' => "Apakah Anda yakin ingin menghapus produk '{$product->name}' beserta seluruh varian SKU-nya? Tindakan ini tidak dapat dibatalkan.",
        ]);
    }

    /**
     * Execute product deletion.
     */
    public function deleteProduct(DeleteProductAction $deleteAction): void
    {
        if (! $this->deletingProductId) {
            return;
        }

        $product = Product::find($this->deletingProductId);
        if (! $product) {
            return;
        }

        $name = $product->name;
        $deleteAction->execute($product);

        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'Produk Dihapus',
            'message' => "Produk '{$name}' telah dihapus dari katalog.",
        ]);

        $this->reset(['deletingProductId', 'deletingProductName']);
        $this->dispatch('close-confirmation-delete-product-modal');
    }

    public function render()
    {
        $categories = Category::active()->orderBy('name')->get();

        $query = Product::with(['category', 'variants'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhereHas('variants', fn ($v) => $v->where('sku', 'like', $term));
                });
            })
            ->when($this->categoryFilter !== 'all', fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->latest();

        $products = $query->paginate(15);
        $totalCount = Product::count();

        return view('livewire.catalog.product-index', [
            'products' => $products,
            'categories' => $categories,
            'totalCount' => $totalCount,
        ]);
    }
}

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
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Katalog Produk | Malega Apparel Backoffice')]
#[Layout('layouts.app')]
class ProductIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $statusFilter = 'all';

    // Modal Form State
    public bool $isEditing = false;

    public ?int $editingProductId = null;

    public ?int $category_id = null;

    public ?int $fabric_spec_id = null;

    public string $name = '';

    public string $slug = '';

    public ?string $description = null;

    public string $status = 'active';

    public ?string $featured_image = null;

    /**
     * Size format type: 'letter' (Abjad) or 'numeric' (Angka/Nomor).
     */
    public string $sizeType = 'letter';

    /**
     * Uploaded main featured image file from device.
     */
    public $featured_image_file = null;

    /**
     * Uploaded variant image files from device.
     *
     * @var array<int, mixed>
     */
    public array $variant_image_files = [];

    /**
     * List of variants for the product form.
     *
     * @var array<int, array{id?: int|null, sku: string, title: string, color_name?: string|null, color_hex?: string|null, size?: string|null, image_url?: string|null, price: int|string, compare_at_price?: int|string|null, cost_price?: int|string|null, weight_grams: int, is_active: bool}>
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
     * Handle size type toggle (Abjad vs Angka).
     */
    public function updatedSizeType(string $value): void
    {
        if ($value === 'numeric') {
            foreach ($this->variants as &$v) {
                if (! empty($v['size']) && ! is_numeric($v['size'])) {
                    $v['size'] = '30';
                }
            }
        } else {
            foreach ($this->variants as &$v) {
                if (! empty($v['size']) && is_numeric($v['size'])) {
                    $v['size'] = 'L';
                }
            }
        }
        unset($v);
    }

    /**
     * Add an empty variant row to the form.
     */
    public function addVariant(): void
    {
        $defaultSize = $this->sizeType === 'numeric' ? '30' : 'L';

        $this->variants[] = [
            'id' => null,
            'sku' => '',
            'title' => 'Ukuran '.$defaultSize,
            'color_name' => 'Onyx Black',
            'color_hex' => '#0B132B',
            'size' => $defaultSize,
            'image_url' => '',
            'price' => 299000,
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
     * Quick generator for standard letter sizes (S, M, L, XL).
     */
    public function generateStandardSizes(): void
    {
        $baseSku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name ?: 'MLG'), 0, 6));
        $defaultPrice = ! empty($this->variants[0]['price']) ? (int) $this->variants[0]['price'] : 299000;
        $defaultColor = ! empty($this->variants[0]['color_name']) ? $this->variants[0]['color_name'] : 'Onyx Black';
        $defaultHex = ! empty($this->variants[0]['color_hex']) ? $this->variants[0]['color_hex'] : '#0B132B';
        $defaultImg = ! empty($this->variants[0]['image_url']) ? $this->variants[0]['image_url'] : '';

        $this->variants = [
            ['id' => null, 'sku' => "{$baseSku}-S", 'title' => 'Ukuran S', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => 'S', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-M", 'title' => 'Ukuran M', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => 'M', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-L", 'title' => 'Ukuran L', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => 'L', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-XL", 'title' => 'Ukuran XL', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => 'XL', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 250, 'is_active' => true],
        ];

        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Varian Abjad Dibuat',
            'message' => '4 varian ukuran abjad (S, M, L, XL) berhasil dibuat.',
        ]);
    }

    /**
     * Quick generator for standard number sizes (28, 30, 32, 34).
     */
    public function generateNumberSizes(): void
    {
        $baseSku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name ?: 'MLG'), 0, 6));
        $defaultPrice = ! empty($this->variants[0]['price']) ? (int) $this->variants[0]['price'] : 349000;
        $defaultColor = ! empty($this->variants[0]['color_name']) ? $this->variants[0]['color_name'] : 'Raw Indigo';
        $defaultHex = ! empty($this->variants[0]['color_hex']) ? $this->variants[0]['color_hex'] : '#1A2A4E';
        $defaultImg = ! empty($this->variants[0]['image_url']) ? $this->variants[0]['image_url'] : '';

        $this->variants = [
            ['id' => null, 'sku' => "{$baseSku}-28", 'title' => 'Size 28', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => '28', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 450, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-30", 'title' => 'Size 30', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => '30', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 450, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-32", 'title' => 'Size 32', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => '32', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 450, 'is_active' => true],
            ['id' => null, 'sku' => "{$baseSku}-34", 'title' => 'Size 34', 'color_name' => $defaultColor, 'color_hex' => $defaultHex, 'size' => '34', 'image_url' => $defaultImg, 'price' => $defaultPrice, 'compare_at_price' => null, 'cost_price' => null, 'weight_grams' => 450, 'is_active' => true],
        ];

        $this->dispatch('toast', [
            'type' => 'info',
            'title' => 'Varian Nomor Dibuat',
            'message' => '4 varian ukuran nomor celana (28, 30, 32, 34) berhasil dibuat.',
        ]);
    }

    /**
     * Open create product modal.
     */
    public function openCreateModal(): void
    {
        $this->resetValidation();
        $firstCategory = Category::active()->orderBy('sort_order')->first();
        $firstSpec = \App\Models\FabricSpecification::where('is_active', true)->first();

        $this->reset(['editingProductId', 'name', 'slug', 'description', 'featured_image', 'featured_image_file', 'variant_image_files']);
        $this->category_id = $firstCategory?->id;
        $this->fabric_spec_id = $firstSpec?->id;
        $this->status = 'active';
        $this->sizeType = 'letter';
        $this->isEditing = false;

        $this->variants = [
            [
                'id' => null,
                'sku' => '',
                'title' => 'All Size / Standar',
                'color_name' => 'Onyx Black',
                'color_hex' => '#0B132B',
                'size' => 'L',
                'image_url' => '',
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
        $this->fabric_spec_id = $product->fabric_spec_id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->status = $product->status->value;
        $this->featured_image = $product->featured_image;
        $this->featured_image_file = null;
        $this->variant_image_files = [];
        $this->isEditing = true;

        $firstSize = $product->variants->first()?->size ?? '';
        $this->sizeType = is_numeric($firstSize) ? 'numeric' : 'letter';

        $this->variants = $product->variants->map(fn ($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'title' => $v->title,
            'color_name' => $v->color_name ?? '',
            'color_hex' => $v->color_hex ?? '#0B132B',
            'size' => $v->size ?? '',
            'image_url' => $v->image_url ?? '',
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
            'featured_image_file' => ['nullable', 'image', 'max:5120'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:64'],
            'variants.*.title' => ['required', 'string', 'max:255'],
            'variants.*.color_name' => ['nullable', 'string', 'max:64'],
            'variants.*.color_hex' => ['nullable', 'string', 'max:16'],
            'variants.*.size' => ['nullable', 'string', 'max:32'],
            'variants.*.image_url' => ['nullable', 'string', 'max:1000'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.weight_grams' => ['required', 'integer', 'min:1'],
            'variants.*.is_active' => ['boolean'],
        ]);

        // 1. Process Main Featured Image Upload
        $featuredImagePath = $this->featured_image;
        if ($this->featured_image_file) {
            $featuredImagePath = $this->featured_image_file->store('products', 'public');
        }

        // 2. Process Variant Images Uploads
        $processedVariants = $this->variants;
        foreach ($processedVariants as $idx => &$variant) {
            if (isset($this->variant_image_files[$idx]) && is_object($this->variant_image_files[$idx])) {
                $variantPath = $this->variant_image_files[$idx]->store('products/variants', 'public');
                $variant['image_url'] = 'storage/' . $variantPath;
            }
        }
        unset($variant);

        $payload = [
            'category_id' => (int) $this->category_id,
            'fabric_spec_id' => $this->fabric_spec_id ? (int) $this->fabric_spec_id : null,
            'name' => $this->name,
            'slug' => $this->slug ?: null,
            'description' => $this->description ?: null,
            'status' => ProductStatus::from($this->status),
            'featured_image' => $featuredImagePath ?: null,
            'variants' => $processedVariants,
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
            $this->reset(['editingProductId', 'category_id', 'fabric_spec_id', 'name', 'slug', 'description', 'status', 'featured_image', 'variants', 'isEditing']);
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
        $fabricSpecs = \App\Models\FabricSpecification::where('is_active', true)->orderBy('name')->get();

        $query = Product::with(['category', 'variants', 'fabricSpecification'])
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
            'fabricSpecs' => $fabricSpecs,
            'totalCount' => $totalCount,
        ]);
    }
}

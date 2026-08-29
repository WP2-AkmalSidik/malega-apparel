<?php

namespace Tests\Feature\Api\V1;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected Product $activeProduct;

    protected Product $draftProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = app(CreateCategoryAction::class)->execute([
            'name' => 'Kemeja Formal',
            'slug' => 'kemeja-formal',
            'is_active' => true,
        ]);

        $this->activeProduct = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Oxford Navy Active',
            'slug' => 'oxford-navy-active',
            'description' => 'Kemeja katun premium.',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'API-OXF-S', 'title' => 'S', 'price' => 300000, 'weight_grams' => 250],
                ['sku' => 'API-OXF-M', 'title' => 'M', 'price' => 320000, 'weight_grams' => 260],
            ],
        ]);

        // Inbound stock for variant S
        app(AddStockInboundAction::class)->execute($this->activeProduct->variants->first()->inventoryItem, [
            'quantity' => 15,
        ]);

        $this->draftProduct = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Secret Draft Product',
            'slug' => 'secret-draft',
            'status' => ProductStatus::Draft,
            'variants' => [
                ['sku' => 'DFT-SKU-01', 'title' => 'All', 'price' => 500000],
            ],
        ]);
    }

    public function test_can_list_active_categories(): void
    {
        $response = $this->getJson(route('api.v1.categories.index'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'name' => 'Kemeja Formal',
                'slug' => 'kemeja-formal',
            ]);
    }

    public function test_can_list_active_collections(): void
    {
        Collection::create([
            'name' => 'Summer Collection 2026',
            'slug' => 'summer-2026',
            'is_active' => true,
        ]);

        $response = $this->getJson(route('api.v1.collections.index'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'name' => 'Summer Collection 2026',
                'slug' => 'summer-2026',
            ]);
    }

    public function test_can_list_active_products_and_excludes_drafts(): void
    {
        $response = $this->getJson(route('api.v1.products.index'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'name' => 'Oxford Navy Active',
                'slug' => 'oxford-navy-active',
            ])
            ->assertJsonMissing([
                'name' => 'Secret Draft Product',
            ]);
    }

    public function test_can_filter_products_by_category_and_search(): void
    {
        $response = $this->getJson(route('api.v1.products.index', [
            'category' => 'kemeja-formal',
            'search' => 'Oxford',
        ]));

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Oxford Navy Active']);
    }

    public function test_can_get_product_detail_by_slug_with_stock_info(): void
    {
        $response = $this->getJson(route('api.v1.products.show', ['slug' => 'oxford-navy-active']));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Oxford Navy Active',
                    'slug' => 'oxford-navy-active',
                ],
            ])
            ->assertJsonFragment([
                'sku' => 'API-OXF-S',
                'is_in_stock' => true,
                'available_stock' => 15,
            ]);
    }

    public function test_draft_product_detail_returns_404(): void
    {
        $response = $this->getJson(route('api.v1.products.show', ['slug' => 'secret-draft']));

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
            ]);
    }
}

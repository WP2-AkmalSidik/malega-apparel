<?php

namespace Tests\Feature\Catalog;

use App\Models\Product;
use Database\Seeders\StorefrontCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontCatalogSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StorefrontCatalogSeeder::class);
    }

    public function test_storefront_can_list_all_seeded_products_with_rich_attributes(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'subtitle',
                        'badge',
                        'rating',
                        'review_count',
                        'sold_count',
                        'gsm',
                        'material',
                        'category',
                        'price' => ['min', 'max', 'compare_at', 'discount_percentage', 'formatted'],
                        'colors',
                        'variants_count',
                        'is_in_stock',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(8, count($response->json('data')));
    }

    public function test_storefront_can_get_single_product_detail_with_specifications_and_colors(): void
    {
        $response = $this->getJson('/api/v1/products/atelier-monogram-embroidered-cap');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Atelier Monogram Embroidered Cap')
            ->assertJsonPath('data.specifications.Brand', 'Malega Apparel')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'subtitle',
                    'features',
                    'specifications',
                    'colors' => [
                        '*' => ['name', 'hex', 'image'],
                    ],
                    'sizes',
                    'variants' => [
                        '*' => [
                            'id',
                            'sku',
                            'title',
                            'color' => ['name', 'hex', 'image'],
                            'size',
                            'price',
                            'available_stock',
                        ],
                    ],
                ],
            ]);

        $colors = $response->json('data.colors');
        $this->assertCount(2, $colors);
        $this->assertEquals('Obsidian Black', $colors[0]['name']);
    }

    public function test_storefront_can_fetch_lookbook_collections_with_rich_metadata(): void
    {
        $response = $this->getJson('/api/v1/collections');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'subtitle',
                        'season',
                        'badge',
                        'featured_material',
                        'palette',
                        'tags',
                        'cover_image',
                        'products_count',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(5, count($response->json('data')));
    }
}

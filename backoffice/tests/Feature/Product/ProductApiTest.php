<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use Database\Seeders\StorefrontCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StorefrontCatalogSeeder::class);
    }

    public function test_can_list_products_with_filters(): void
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price' => [
                            'min',
                            'max',
                            'formatted',
                        ],
                        'colors',
                        'sizes',
                    ],
                ],
            ]);
    }

    public function test_can_get_product_detail_with_variant_matrix(): void
    {
        $product = Product::where('slug', 'obsidian-heavyweight-boxy-tee-300gsm')->first();
        $this->assertNotNull($product);

        $response = $this->getJson('/api/v1/products/'.$product->slug);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.slug', 'obsidian-heavyweight-boxy-tee-300gsm')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'price' => [
                        'min',
                        'max',
                        'compare_at',
                        'formatted',
                    ],
                    'colors',
                    'sizes',
                    'variants' => [
                        '*' => [
                            'id',
                            'sku',
                            'title',
                            'color' => [
                                'name',
                                'hex',
                                'image',
                            ],
                            'size',
                            'price',
                            'formatted_price',
                            'available_stock',
                            'is_in_stock',
                        ],
                    ],
                ],
            ]);

        // Verify variant price differential (XXL or Acid Wash is priced higher)
        $variants = $response->json('data.variants');
        $this->assertNotEmpty($variants);

        $baseVariant = collect($variants)->first(fn ($v) => $v['color']['name'] === 'Onyx Black' && $v['size'] === 'M');
        $xxlAcidVariant = collect($variants)->first(fn ($v) => $v['color']['name'] === 'Vintage Acid Wash' && $v['size'] === 'XXL');

        $this->assertNotNull($baseVariant);
        $this->assertNotNull($xxlAcidVariant);
        $this->assertGreaterThan($baseVariant['price'], $xxlAcidVariant['price']);
    }
}

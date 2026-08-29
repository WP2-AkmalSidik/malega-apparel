<?php

namespace Tests\Feature\Api\V1;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Actions\Orders\CreateOrderAction;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutApiTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = app(CreateCategoryAction::class)->execute([
            'name' => 'Kemeja Formal',
            'slug' => 'kemeja-formal',
            'is_active' => true,
        ]);

        $this->product = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Oxford Shirt Storefront',
            'slug' => 'oxford-shirt-storefront',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'STR-OXF-M', 'title' => 'Size M', 'price' => 350000, 'weight_grams' => 250],
            ],
        ]);

        $this->variant = $this->product->variants->first();

        // Inbound initial stock of 10 units
        app(AddStockInboundAction::class)->execute($this->variant->inventoryItem, [
            'quantity' => 10,
        ]);
    }

    public function test_can_checkout_order_via_storefront_api(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Reza Rahadian',
                'email' => 'reza@actors.id',
                'phone' => '081234567890',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 2,
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Reza Rahadian',
                'phone' => '081234567890',
                'address_line1' => 'Jl. Kemang Raya No. 12',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12730',
                'courier_name' => 'JNE REG',
            ],
            'shipping_total' => 18000,
            'discount_total' => 0,
            'notes' => 'Tolong segera dikirimkan.',
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'pricing' => [
                        'subtotal' => 700000, // 2 x 350,000
                        'shipping_total' => 18000,
                        'grand_total' => 718000,
                    ],
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'source' => 'storefront',
            'subtotal' => 700000,
            'grand_total' => 718000,
        ]);

        // Stock must be reserved atomically (2 units reserved, 8 available)
        $this->assertEquals(2, $this->variant->fresh()->inventoryItem->reserved);
        $this->assertEquals(8, $this->variant->fresh()->inventoryItem->available);
    }

    public function test_checkout_fails_when_stock_is_insufficient(): void
    {
        $payload = [
            'customer' => [
                'name' => 'Over Buyer',
                'email' => 'over@buyer.com',
                'phone' => '0811111111',
            ],
            'items' => [
                [
                    'variant_id' => $this->variant->id,
                    'quantity' => 15, // Only 10 available!
                ],
            ],
            'shipping_address' => [
                'recipient_name' => 'Over Buyer',
                'phone' => '0811111111',
                'address_line1' => 'Jl. Test',
                'city' => 'Jakarta',
                'province' => 'DKI',
                'postal_code' => '10000',
            ],
        ];

        $response = $this->postJson(route('api.v1.orders.checkout'), $payload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_track_order_by_order_number(): void
    {
        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Gading Marten', 'email' => 'gading@test.com', 'phone' => '08123456789'],
            'items' => [['variant_id' => $this->variant->id, 'quantity' => 1]],
            'address' => [
                'recipient_name' => 'Gading Marten',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Andara',
                'city' => 'Depok',
                'province' => 'Jawa Barat',
                'postal_code' => '16514',
            ],
        ]);

        $response = $this->getJson(route('api.v1.orders.track', ['order_number' => $order->order_number]));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                ],
            ]);
    }

    public function test_tracking_non_existent_order_returns_404(): void
    {
        $response = $this->getJson(route('api.v1.orders.track', ['order_number' => 'MLG-99999999-9999']));

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
            ]);
    }
}

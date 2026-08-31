<?php

namespace Tests\Feature\Orders;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Actions\Inventory\AddStockInboundAction;
use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\UpdateFulfillmentAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Livewire\Orders\OrderIndex;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Category $category;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $this->category = app(CreateCategoryAction::class)->execute([
            'name' => 'Kemeja Formal',
            'slug' => 'kemeja-formal',
        ]);

        $this->product = app(CreateProductAction::class)->execute([
            'category_id' => $this->category->id,
            'name' => 'Oxford Signature Navy',
            'status' => 'active',
            'variants' => [
                ['sku' => 'MLG-ORD-NVY-M', 'title' => 'Size M', 'price' => 300000, 'weight_grams' => 250],
            ],
        ]);

        $this->variant = $this->product->variants->first();

        // Inbound initial stock
        app(AddStockInboundAction::class)->execute($this->variant->inventoryItem, [
            'quantity' => 20,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_guest_is_redirected_from_orders_to_login(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_staff_can_view_orders_page(): void
    {
        $this->actingAs($this->admin);

        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Budi Santoso', 'email' => 'budi@test.com', 'phone' => '08123456789'],
            'items' => [['variant_id' => $this->variant->id, 'quantity' => 1]],
            'address' => [
                'recipient_name' => 'Budi Santoso',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Merdeka 10',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
            ],
            'shipping_total' => 15000,
        ]);

        $response = $this->get(route('orders.index'));

        $response->assertOk()
            ->assertSee('Pesanan & Transaksi')
            ->assertSee($order->order_number)
            ->assertSee('Budi Santoso');
    }

    public function test_can_create_order_manually_via_livewire_modal(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(OrderIndex::class)
            ->call('openCreateModal')
            ->set('customerName', 'Agus Salim')
            ->set('customerEmail', 'agus.salim@gmail.com')
            ->set('customerPhone', '081987654321')
            ->set('recipientName', 'Agus Salim')
            ->set('recipientPhone', '081987654321')
            ->set('addressLine1', 'Jl. Sudirman Kav 28')
            ->set('city', 'Jakarta Selatan')
            ->set('province', 'DKI Jakarta')
            ->set('postalCode', '12190')
            ->set('shippingTotal', 20000)
            ->set('discountTotal', 50000)
            ->set('orderItems', [
                [
                    'variant_id' => $this->variant->id,
                    'sku' => $this->variant->sku,
                    'title' => $this->variant->title,
                    'product_name' => $this->product->name,
                    'price' => (int) $this->variant->price,
                    'quantity' => 2,
                ],
            ])
            ->call('saveOrder')
            ->assertDispatched('toast')
            ->assertDispatched('close-modal-create-order-modal');

        $this->assertDatabaseHas('orders', [
            'subtotal' => 600000, // 2 x 300,000
            'discount_total' => 50000,
            'shipping_total' => 20000,
            'grand_total' => 570000, // 600,000 - 50,000 + 20,000
            'order_status' => OrderStatus::Pending->value,
            'payment_status' => PaymentStatus::Unpaid->value,
        ]);

        $this->assertDatabaseHas('order_items', [
            'sku' => 'MLG-ORD-NVY-M',
            'product_name' => 'Oxford Signature Navy',
            'unit_price' => 300000,
            'quantity' => 2,
            'subtotal' => 600000,
        ]);

        // Stock must be reserved atomically (2 units)
        $this->assertEquals(2, $this->variant->fresh()->inventoryItem->reserved);
        $this->assertEquals(18, $this->variant->fresh()->inventoryItem->available);
    }

    public function test_can_mark_order_as_paid_and_processing(): void
    {
        $this->actingAs($this->admin);

        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Doni Siregar', 'email' => 'doni@test.com', 'phone' => '08123456789'],
            'items' => [['variant_id' => $this->variant->id, 'quantity' => 1]],
            'address' => [
                'recipient_name' => 'Doni Siregar',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Gatot Subroto',
                'city' => 'Jakarta',
                'province' => 'DKI',
                'postal_code' => '12930',
            ],
        ]);

        Livewire::test(OrderIndex::class)
            ->set('selectedOrderId', $order->id)
            ->call('markAsPaid')
            ->assertDispatched('toast');

        $this->assertEquals(PaymentStatus::Paid, $order->fresh()->payment_status);
        $this->assertEquals(OrderStatus::Processing, $order->fresh()->order_status);
    }

    public function test_can_fulfill_order_with_tracking_number_and_deduct_stock(): void
    {
        $this->actingAs($this->admin);

        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Hendra Setiawan', 'email' => 'hendra@test.com', 'phone' => '08123456789'],
            'items' => [['variant_id' => $this->variant->id, 'quantity' => 3]],
            'address' => [
                'recipient_name' => 'Hendra Setiawan',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Asia Afrika',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40111',
                'courier_name' => 'JNE REG',
            ],
        ]);

        // Reserved was 3, on_hand was 20
        $this->assertEquals(3, $this->variant->fresh()->inventoryItem->reserved);

        app(UpdateFulfillmentAction::class)->execute($order, [
            'courier_name' => 'JNE REG',
            'tracking_number' => 'JNE-99887766',
            'user_id' => $this->admin->id,
        ]);

        $this->assertEquals(FulfillmentStatus::Fulfilled, $order->fresh()->fulfillment_status);
        $this->assertEquals(17, $this->variant->fresh()->inventoryItem->on_hand); // 20 - 3
        $this->assertEquals(0, $this->variant->fresh()->inventoryItem->reserved);
    }

    public function test_cancelling_order_releases_reserved_stock(): void
    {
        $this->actingAs($this->admin);

        $order = app(CreateOrderAction::class)->execute([
            'customer' => ['name' => 'Eko Prasetyo', 'email' => 'eko@test.com', 'phone' => '08123456789'],
            'items' => [['variant_id' => $this->variant->id, 'quantity' => 4]],
            'address' => [
                'recipient_name' => 'Eko Prasetyo',
                'phone' => '08123456789',
                'address_line1' => 'Jl. Diponegoro',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60241',
            ],
        ]);

        $this->assertEquals(4, $this->variant->fresh()->inventoryItem->reserved);

        Livewire::test(OrderIndex::class)
            ->set('selectedOrderId', $order->id)
            ->call('cancelOrder')
            ->assertDispatched('toast');

        $this->assertEquals(OrderStatus::Cancelled, $order->fresh()->order_status);
        $this->assertEquals(0, $this->variant->fresh()->inventoryItem->reserved);
        $this->assertEquals(20, $this->variant->fresh()->inventoryItem->available);
    }

    public function test_order_pagination_renders_max_15_per_page(): void
    {
        $this->actingAs($this->admin);

        for ($i = 1; $i <= 18; $i++) {
            Order::create([
                'order_number' => "MLG-PAGE-{$i}",
                'subtotal' => 100000,
                'grand_total' => 100000,
                'order_status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'fulfillment_status' => FulfillmentStatus::Unfulfilled,
            ]);
        }

        Livewire::test(OrderIndex::class)
            ->assertViewHas('orders', function ($orders) {
                return $orders->count() === 15;
            });
    }

    public function test_authenticated_staff_can_view_and_print_thermal_shipping_label(): void
    {
        $this->actingAs($this->admin);

        $order = Order::create([
            'order_number' => 'MLG-LABEL-TEST-01',
            'subtotal' => 589000,
            'grand_total' => 607000,
            'order_status' => OrderStatus::Processing,
            'payment_status' => PaymentStatus::Paid,
            'fulfillment_status' => FulfillmentStatus::Fulfilled,
        ]);

        $order->address()->create([
            'recipient_name' => 'Arya Bimasakti',
            'phone' => '081298765432',
            'address_line1' => 'Jl. Boulevard Barat Raya Blok LA-1 No. 12',
            'city' => 'Jakarta Utara',
            'province' => 'DKI Jakarta',
            'postal_code' => '14240',
            'courier_name' => 'JNE (REG)',
            'tracking_number' => 'WYB-1788147864651',
        ]);

        $order->items()->create([
            'product_variant_id' => $this->variant->id,
            'sku' => $this->variant->sku,
            'product_name' => 'Signature Shirt',
            'variant_title' => 'Silver / L',
            'unit_price' => 589000,
            'quantity' => 1,
            'subtotal' => 589000,
        ]);

        $response = $this->get(route('orders.shipping-label', $order->id));

        $response->assertOk()
            ->assertViewIs('orders.shipping-label')
            ->assertSee('WYB-1788147864651')
            ->assertSee('Arya Bimasakti')
            ->assertSee('MALEGA APPAREL')
            ->assertSee('Signature Shirt');
    }
}


<?php

namespace Tests\Feature\Logistics;

use App\Actions\Logistics\CreateBiteshipShipmentAction;
use App\Actions\Logistics\HandleBiteshipWebhookAction;
use App\Actions\Logistics\SyncBiteshipTrackingAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Logistics\BiteshipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BiteshipIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Order $sampleOrder;

    protected ProductVariant $variant;

    protected InventoryItem $inventoryItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);

        $category = Category::create([
            'name' => 'Shirts',
            'slug' => 'shirts',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Oxford Signature Shirt',
            'slug' => 'oxford-signature-shirt',
            'description' => 'Classic Cotton Shirt',
            'status' => 'active',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'MLG-OXF-NVY-M',
            'title' => 'Navy / M',
            'price' => 399000,
            'weight_grams' => 300,
            'is_active' => true,
        ]);

        $this->inventoryItem = InventoryItem::create([
            'variant_id' => $this->variant->id,
            'on_hand' => 50,
            'reserved' => 2,
            'low_stock_threshold' => 5,
        ]);

        $customer = Customer::create([
            'name' => 'Dimas Arya Pratama',
            'email' => 'dimas@example.com',
            'phone' => '081122334455',
        ]);

        $this->sampleOrder = Order::create([
            'order_number' => 'MLG-20260831-0001',
            'customer_id' => $customer->id,
            'source' => 'web',
            'order_status' => OrderStatus::Processing,
            'payment_status' => PaymentStatus::Paid,
            'fulfillment_status' => FulfillmentStatus::Unfulfilled,
            'subtotal' => 399000,
            'shipping_total' => 15000,
            'grand_total' => 414000,
        ]);

        OrderItem::create([
            'order_id' => $this->sampleOrder->id,
            'product_id' => $product->id,
            'variant_id' => $this->variant->id,
            'product_name' => $product->name,
            'variant_title' => $this->variant->title,
            'sku' => $this->variant->sku,
            'unit_price' => 399000,
            'quantity' => 1,
            'subtotal' => 399000,
        ]);

        OrderAddress::create([
            'order_id' => $this->sampleOrder->id,
            'recipient_name' => 'Dimas Arya Pratama',
            'phone' => '081122334455',
            'address_line1' => 'Jl. Sudirman No. 45',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10220',
        ]);
    }

    public function test_biteship_service_can_fetch_supported_couriers(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/couriers' => Http::response([
                'success' => true,
                'couriers' => [
                    ['courier_name' => 'JNE', 'courier_code' => 'jne'],
                    ['courier_name' => 'SiCepat', 'courier_code' => 'sicepat'],
                ],
            ], 200),
        ]);

        $service = app(BiteshipService::class);
        $couriers = $service->getCouriers();

        $this->assertCount(2, $couriers);
        $this->assertEquals('JNE', $couriers[0]['courier_name']);
    }

    public function test_create_biteship_shipment_action_generates_auto_awb_and_deducts_stock(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'id' => 'biteship_ord_12345',
                'status' => 'confirmed',
                'courier' => [
                    'tracking_id' => 'trck_abcde_999',
                    'waybill_id' => 'JNE-CGK-88990011',
                    'company' => 'jne',
                    'type' => 'reg',
                    'link' => 'https://track.biteship.com/trck_abcde_999',
                    'shipment_fee' => 15000,
                ],
            ], 200),
        ]);

        $action = app(CreateBiteshipShipmentAction::class);
        $order = $action->execute($this->sampleOrder, [
            'courier_company' => 'jne',
            'courier_type' => 'reg',
            'user_id' => $this->adminUser->id,
        ]);

        // Assert Order updated
        $this->assertEquals(FulfillmentStatus::Fulfilled, $order->fulfillment_status);
        $this->assertEquals('JNE (REG)', $order->address->courier_name);
        $this->assertEquals('JNE-CGK-88990011', $order->address->tracking_number);

        // Assert Shipment record created
        $shipment = $order->shipment;
        $this->assertNotNull($shipment);
        $this->assertEquals('JNE-CGK-88990011', $shipment->waybill_id);
        $this->assertEquals('JNE', $shipment->courier_company);
        $this->assertEquals('REG', $shipment->courier_service_name);
        $this->assertEquals('confirmed', $shipment->status);

        // Assert Inventory physical stock deducted
        $this->inventoryItem->refresh();
        $this->assertEquals(49, $this->inventoryItem->on_hand); // 50 - 1
        $this->assertEquals(1, $this->inventoryItem->reserved); // 2 - 1

        // Assert Stock Ledger movement
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $this->inventoryItem->id,
            'type' => StockMovementType::Fulfilled->value,
            'quantity_change' => -1,
            'on_hand_after' => 49,
        ]);
    }

    public function test_sync_biteship_tracking_action_updates_shipment_history(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->sampleOrder->id,
            'biteship_order_id' => 'biteship_ord_12345',
            'biteship_tracking_id' => 'trck_abcde_999',
            'courier_company' => 'JNE',
            'courier_service_name' => 'REG',
            'waybill_id' => 'JNE-CGK-88990011',
            'status' => 'confirmed',
        ]);

        Http::fake([
            'https://api.biteship.com/v1/trackings/trck_abcde_999' => Http::response([
                'success' => true,
                'status' => 'in_transit',
                'history' => [
                    ['status' => 'confirmed', 'note' => 'Order created'],
                    ['status' => 'in_transit', 'note' => 'Paket sedang dalam perjalanan ke Jakarta Pusat'],
                ],
                'link' => 'https://track.biteship.com/trck_abcde_999',
            ], 200),
        ]);

        $syncAction = app(SyncBiteshipTrackingAction::class);
        $result = $syncAction->execute($this->sampleOrder);

        $this->assertTrue($result['success']);
        $shipment->refresh();
        $this->assertEquals('in_transit', $shipment->status);
        $this->assertCount(2, $shipment->tracking_history);
    }

    public function test_biteship_webhook_updates_status_and_is_idempotent(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->sampleOrder->id,
            'biteship_order_id' => 'biteship_ord_9988',
            'biteship_tracking_id' => 'trck_9988',
            'courier_company' => 'SICEPAT',
            'courier_service_name' => 'BEST',
            'waybill_id' => '000998877665',
            'status' => 'picking_up',
        ]);

        $webhookAction = app(HandleBiteshipWebhookAction::class);

        // First delivery update
        $res1 = $webhookAction->execute([
            'event' => 'order.status',
            'order_id' => 'biteship_ord_9988',
            'status' => 'delivered',
            'status_note' => 'Paket telah diterima oleh penerima',
        ]);

        $this->assertTrue($res1['success']);
        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
        $this->sampleOrder->refresh();
        $this->assertEquals(FulfillmentStatus::Delivered, $this->sampleOrder->fulfillment_status);

        // Second duplicate delivery update (idempotency check)
        $res2 = $webhookAction->execute([
            'event' => 'order.status',
            'order_id' => 'biteship_ord_9988',
            'status' => 'delivered',
            'status_note' => 'Duplicate webhook payload',
        ]);

        $this->assertTrue($res2['success']);
        $this->assertStringContainsString('Idempotent', $res2['message']);
    }

    public function test_livewire_order_index_can_trigger_auto_awb(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'id' => 'biteship_ord_livewire',
                'status' => 'confirmed',
                'courier' => [
                    'tracking_id' => 'trck_livewire_11',
                    'waybill_id' => 'SICEPAT-LIVE-001',
                    'company' => 'sicepat',
                    'type' => 'reg',
                    'link' => 'https://track.biteship.com/trck_livewire_11',
                    'shipment_fee' => 16000,
                ],
            ], 200),
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Orders\OrderIndex::class)
            ->set('selectedOrderId', $this->sampleOrder->id)
            ->set('biteshipCourier', 'sicepat')
            ->set('biteshipService', 'reg')
            ->call('createBiteshipFulfillment')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('shipments', [
            'order_id' => $this->sampleOrder->id,
            'waybill_id' => 'SICEPAT-LIVE-001',
            'courier_company' => 'SICEPAT',
        ]);
    }

    public function test_public_order_tracking_page_can_search_and_display_milestones(): void
    {
        $shipment = Shipment::create([
            'order_id' => $this->sampleOrder->id,
            'biteship_order_id' => 'biteship_ord_public',
            'biteship_tracking_id' => 'trck_public_1',
            'courier_company' => 'JNE',
            'courier_service_name' => 'REG',
            'waybill_id' => 'JNE-PUBLIC-9988',
            'status' => 'in_transit',
            'tracking_history' => [
                ['status' => 'confirmed', 'note' => 'Pesanan diterima'],
                ['status' => 'in_transit', 'note' => 'Paket sedang diberangkatkan dari Jakarta'],
            ],
        ]);

        Http::fake([
            'https://api.biteship.com/v1/trackings/trck_public_1' => Http::response([
                'success' => true,
                'status' => 'in_transit',
                'history' => [
                    ['status' => 'confirmed', 'note' => 'Pesanan diterima'],
                    ['status' => 'in_transit', 'note' => 'Paket sedang diberangkatkan dari Jakarta'],
                ],
                'link' => 'https://track.biteship.com/trck_public_1',
            ], 200),
        ]);

        // Search by canonical order number
        Livewire::test(\App\Livewire\Public\OrderTracking::class, ['order_number' => $this->sampleOrder->order_number])
            ->assertSee($this->sampleOrder->order_number)
            ->assertSee('JNE-PUBLIC-9988')
            ->assertSee('Paket sedang diberangkatkan dari Jakarta')
            ->assertSee('Dimas Arya Pratama');

        // Search by AWB / Waybill ID
        Livewire::test(\App\Livewire\Public\OrderTracking::class)
            ->set('searchQuery', 'JNE-PUBLIC-9988')
            ->call('search')
            ->assertSee($this->sampleOrder->order_number)
            ->assertSee('JNE-PUBLIC-9988');
    }
}

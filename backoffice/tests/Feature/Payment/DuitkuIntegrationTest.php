<?php

namespace Tests\Feature\Payment;

use App\Actions\Payment\HandleDuitkuCallbackAction;
use App\Actions\Payment\ProcessDuitkuPaymentAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Payment\DuitkuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DuitkuIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Product $product;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'duitku.merchant_code' => 'D9099',
            'duitku.api_key' => 'test_duitku_secret_key',
            'duitku.environment' => 'sandbox',
            'duitku.callback_url' => 'https://malega.my.id/api/v1/webhooks/duitku',
            'duitku.return_url' => 'https://store.malega.my.id/order-confirmation',
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin@malega.id',
        ]);

        $category = Category::create([
            'name' => 'Signature T-Shirts',
            'slug' => 'signature-t-shirts',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Royal Signature Tee',
            'slug' => 'royal-signature-tee',
            'base_price' => 250000,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'MLG-ROYAL-BLK-L',
            'title' => 'Black / L',
            'price' => 250000,
            'weight_grams' => 300,
            'is_active' => true,
        ]);

        InventoryItem::create([
            'variant_id' => $this->variant->id,
            'on_hand' => 50,
            'reserved' => 0,
        ]);
    }

    protected function createSampleOrder(int $grandTotal = 265000): Order
    {
        $customer = Customer::create([
            'name' => 'Arya Bimasakti',
            'email' => 'arya@example.com',
            'phone' => '081298765432',
        ]);

        $order = Order::create([
            'order_number' => 'MLG-20260903-TEST',
            'customer_id' => $customer->id,
            'source' => 'storefront',
            'order_status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Unpaid,
            'subtotal' => 250000,
            'shipping_total' => 15000,
            'grand_total' => $grandTotal,
        ]);

        $order->address()->create([
            'recipient_name' => 'Arya Bimasakti',
            'phone' => '081298765432',
            'address_line1' => 'Jl. Boulevard Barat Raya Blok LA-1 No. 12',
            'city' => 'Jakarta Utara',
            'province' => 'DKI Jakarta',
            'postal_code' => '14240',
            'courier_name' => 'JNE',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'variant_id' => $this->variant->id,
            'product_name' => 'Royal Signature Tee',
            'variant_title' => 'Black / L',
            'sku' => 'MLG-ROYAL-BLK-L',
            'unit_price' => 250000,
            'quantity' => 1,
            'subtotal' => 250000,
        ]);

        return $order;
    }

    public function test_duitku_service_can_create_invoice_with_valid_hmac_signature(): void
    {
        Http::fake([
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
                'statusCode' => '00',
                'statusMessage' => 'SUCCESS',
                'reference' => 'D9099XYZ12345',
                'paymentUrl' => 'https://sandbox.duitku.com/pop/checkout?ref=D9099XYZ12345',
                'vaNumber' => '8808987654321',
                'qrString' => '00020101021226590014ID.LINKAJA.WWW...',
            ], 200),
        ]);

        $order = $this->createSampleOrder(265000);
        $service = app(DuitkuService::class);

        $result = $service->createInvoice($order, 'BC');

        $this->assertTrue($result['success']);
        $this->assertEquals('D9099XYZ12345', $result['reference']);
        $this->assertEquals('https://sandbox.duitku.com/pop/checkout?ref=D9099XYZ12345', $result['payment_url']);
        $this->assertEquals('8808987654321', $result['va_number']);

        Http::assertSent(function ($request) use ($order) {
            $data = $request->data();
            $expectedSignature = hash_hmac('sha256', 'D9099' . $order->order_number . '265000', 'test_duitku_secret_key');

            return $data['merchantCode'] === 'D9099' &&
                   $data['merchantOrderId'] === $order->order_number &&
                   $data['paymentAmount'] === 265000 &&
                   $data['signature'] === $expectedSignature;
        });
    }

    public function test_process_duitku_payment_action_persists_payment_record(): void
    {
        Http::fake([
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
                'statusCode' => '00',
                'statusMessage' => 'SUCCESS',
                'reference' => 'D9099REF9988',
                'paymentUrl' => 'https://sandbox.duitku.com/pop/checkout?ref=D9099REF9988',
                'vaNumber' => '880811223344',
                'qrString' => null,
            ], 200),
        ]);

        $order = $this->createSampleOrder(265000);
        $action = app(ProcessDuitkuPaymentAction::class);

        $res = $action->execute($order, 'BC');

        $this->assertTrue($res['success']);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'merchant_order_id' => $order->order_number,
            'reference' => 'D9099REF9988',
            'payment_method' => 'BC',
            'payment_method_name' => 'BCA Virtual Account',
            'status' => 'pending',
        ]);
    }

    public function test_handle_duitku_callback_updates_order_to_paid_and_processing(): void
    {
        $order = $this->createSampleOrder(265000);

        Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'duitku',
            'merchant_order_id' => $order->order_number,
            'reference' => 'D9099CALLBACK1',
            'payment_method' => 'BC',
            'amount' => 265000,
            'status' => 'pending',
        ]);

        $signature = hash_hmac('sha256', 'D9099' . '265000' . $order->order_number, 'test_duitku_secret_key');

        $payload = [
            'merchantCode' => 'D9099',
            'amount' => '265000',
            'merchantOrderId' => $order->order_number,
            'productDetail' => 'Batik Shirt',
            'paymentCode' => 'BC',
            'resultCode' => '00',
            'reference' => 'D9099CALLBACK1',
            'signature' => $signature,
        ];

        $action = app(HandleDuitkuCallbackAction::class);
        $result = $action->execute($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['http_status']);

        $order->refresh();
        $this->assertEquals(PaymentStatus::Paid, $order->payment_status);
        $this->assertEquals(OrderStatus::Processing, $order->order_status);

        $this->assertDatabaseHas('payments', [
            'merchant_order_id' => $order->order_number,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'payment:paid|order:processing',
        ]);
    }

    public function test_handle_duitku_callback_rejects_bad_signature(): void
    {
        $order = $this->createSampleOrder(265000);

        $payload = [
            'merchantCode' => 'D9099',
            'amount' => '265000',
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'D9099CALLBACK1',
            'signature' => 'invalid_tampered_signature_12345',
        ];

        $action = app(HandleDuitkuCallbackAction::class);
        $result = $action->execute($payload);

        $this->assertFalse($result['success']);
        $this->assertEquals(400, $result['http_status']);
        $this->assertEquals('Bad Signature', $result['message']);

        $this->assertEquals(PaymentStatus::Unpaid, $order->fresh()->payment_status);
    }

    public function test_handle_duitku_callback_rejects_amount_mismatch(): void
    {
        $order = $this->createSampleOrder(265000);

        $tamperedAmount = '100000';
        $signature = hash_hmac('sha256', 'D9099' . $tamperedAmount . $order->order_number, 'test_duitku_secret_key');

        $payload = [
            'merchantCode' => 'D9099',
            'amount' => $tamperedAmount,
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'D9099CALLBACK1',
            'signature' => $signature,
        ];

        $action = app(HandleDuitkuCallbackAction::class);
        $result = $action->execute($payload);

        $this->assertFalse($result['success']);
        $this->assertEquals(400, $result['http_status']);
        $this->assertEquals('Amount Mismatch', $result['message']);

        $this->assertEquals(PaymentStatus::Unpaid, $order->fresh()->payment_status);
    }

    public function test_handle_duitku_callback_is_idempotent(): void
    {
        $order = $this->createSampleOrder(265000);
        $order->payment_status = PaymentStatus::Paid;
        $order->order_status = OrderStatus::Processing;
        $order->save();

        $signature = hash_hmac('sha256', 'D9099' . '265000' . $order->order_number, 'test_duitku_secret_key');

        $payload = [
            'merchantCode' => 'D9099',
            'amount' => '265000',
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'D9099CALLBACK1',
            'signature' => $signature,
        ];

        $action = app(HandleDuitkuCallbackAction::class);
        $result = $action->execute($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['http_status']);
        $this->assertEquals('Order Already Paid (Idempotent)', $result['message']);
    }

    public function test_duitku_webhook_controller_endpoint_returns_200_success_text(): void
    {
        $order = $this->createSampleOrder(265000);
        $signature = hash_hmac('sha256', 'D9099' . '265000' . $order->order_number, 'test_duitku_secret_key');

        $response = $this->post(route('api.v1.webhooks.duitku'), [
            'merchantCode' => 'D9099',
            'amount' => '265000',
            'merchantOrderId' => $order->order_number,
            'resultCode' => '00',
            'reference' => 'D9099WEBHOOKTEST',
            'signature' => $signature,
        ]);

        $response->assertOk();
        $this->assertEquals('Success', $response->getContent());
        $this->assertEquals(PaymentStatus::Paid, $order->fresh()->payment_status);
    }

    public function test_payments_api_endpoints_work_correctly(): void
    {
        Http::fake([
            'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
                'statusCode' => '00',
                'paymentUrl' => 'https://sandbox.duitku.com/pop/checkout?ref=D9099ABC',
                'reference' => 'D9099ABC',
            ], 200),
            'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod' => Http::response([
                'paymentFee' => [
                    ['paymentMethod' => 'BC', 'paymentName' => 'BCA Virtual Account', 'totalFee' => 4000],
                    ['paymentMethod' => 'QR', 'paymentName' => 'QRIS', 'totalFee' => 1500],
                ],
            ], 200),
        ]);

        $order = $this->createSampleOrder(265000);

        // 1. Test methods
        $methodsRes = $this->getJson(route('api.v1.payments.methods', ['amount' => 265000]));
        $methodsRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');

        // 2. Test invoice creation
        $invoiceRes = $this->postJson(route('api.v1.payments.invoice'), [
            'order_number' => $order->order_number,
            'payment_method' => 'BC',
        ]);
        $invoiceRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment_url', 'https://sandbox.duitku.com/pop/checkout?ref=D9099ABC');

        // 3. Test status check
        $statusRes = $this->getJson(route('api.v1.payments.status', $order->order_number));
        $statusRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment_status.code', 'unpaid');
    }
}

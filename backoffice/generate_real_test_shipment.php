<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Actions\Logistics\CreateBiteshipShipmentAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

echo "========================================================\n";
echo "📦 MALEGA APPAREL — REAL BITESHIP API AUTO-AWB GENERATOR\n";
echo "========================================================\n";

// 1. Ensure Admin User
$admin = User::firstOrCreate(
    ['email' => 'admin@malega.id'],
    [
        'name' => 'Owner Malega',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'is_active' => true,
    ]
);

// 2. Ensure Category & Product
$category = Category::firstOrCreate(
    ['slug' => 'heritage-collection'],
    ['name' => 'Heritage Collection', 'is_active' => true]
);

$product = Product::firstOrCreate(
    ['slug' => 'malega-royal-batik-shirt'],
    [
        'category_id' => $category->id,
        'name' => 'Malega Royal Batik Signature Shirt',
        'description' => 'Kemeja Batik Sutra Eksklusif Malega Apparel',
        'status' => 'active',
    ]
);

$variant = ProductVariant::firstOrCreate(
    ['sku' => 'MLG-ROYAL-SLV-L'],
    [
        'product_id' => $product->id,
        'title' => 'Silver Obsidian / L',
        'price' => 589000,
        'weight_grams' => 350,
        'is_active' => true,
    ]
);

$inventory = InventoryItem::firstOrCreate(
    ['variant_id' => $variant->id],
    [
        'on_hand' => 100,
        'reserved' => 5,
        'low_stock_threshold' => 10,
    ]
);

// 3. Ensure Customer
$customer = Customer::firstOrCreate(
    ['phone' => '081298765432'],
    [
        'name' => 'Arya Bimasakti',
        'email' => 'arya.bimasakti@example.com',
    ]
);

// 4. Create Canonical Real Order
$today = date('Ymd');
$randomSuffix = str_pad((string) rand(100, 999), 4, '0', STR_PAD_LEFT);
$orderNumber = "MLG-{$today}-{$randomSuffix}";

$order = Order::create([
    'order_number' => $orderNumber,
    'customer_id' => $customer->id,
    'source' => 'storefront',
    'order_status' => OrderStatus::Processing,
    'payment_status' => PaymentStatus::Paid,
    'fulfillment_status' => FulfillmentStatus::Unfulfilled,
    'subtotal' => 589000,
    'shipping_total' => 18000,
    'discount_total' => 0,
    'tax_total' => 0,
    'grand_total' => 607000,
    'notes' => 'Pesanan Uji Coba Real Auto-AWB Biteship API',
]);

OrderItem::create([
    'order_id' => $order->id,
    'product_id' => $product->id,
    'variant_id' => $variant->id,
    'product_name' => $product->name,
    'variant_title' => $variant->title,
    'sku' => $variant->sku,
    'unit_price' => 589000,
    'quantity' => 1,
    'subtotal' => 589000,
]);

OrderAddress::create([
    'order_id' => $order->id,
    'recipient_name' => 'Arya Bimasakti',
    'phone' => '081298765432',
    'address_line1' => 'Jl. Boulevard Barat Raya Blok LA-1 No. 12, Kelapa Gading',
    'address_line2' => 'Komplek Grand Orchard',
    'city' => 'Jakarta Utara',
    'province' => 'DKI Jakarta',
    'postal_code' => '14240',
]);

echo "✅ 1. Order berhasil dibuat di database: {$order->order_number}\n";
echo "⚡ 2. Menghubungi Server Biteship Sandbox API...\n";

try {
    $action = app(CreateBiteshipShipmentAction::class);
    $updatedOrder = $action->execute($order, [
        'courier_company' => 'jne',
        'courier_type' => 'reg',
        'notes' => 'Tolong hati-hati paket busana sutra Malega Apparel',
        'user_id' => $admin->id,
    ]);

    $shipment = $updatedOrder->shipment;

    echo "\n========================================================\n";
    echo "🎉 RESI AUTO-AWB BERHASIL DITERBITKAN OLEH BITESHIP!\n";
    echo "========================================================\n";
    echo "• No. Pesanan (Order) : {$updatedOrder->order_number}\n";
    echo "• No. Resi (Waybill ID): {$shipment->waybill_id}\n";
    echo "• Ekspedisi Kurir     : {$shipment->courier_company} ({$shipment->courier_service_name})\n";
    echo "• Status Pengiriman   : {$shipment->status_label} ({$shipment->status})\n";
    echo "• Biteship Order ID   : {$shipment->biteship_order_id}\n";
    echo "• Biteship Tracking ID: {$shipment->biteship_tracking_id}\n";
    echo "• Link Web Tracking   : {$shipment->tracking_url}\n";
    echo "• Portal Web Kustom   : http://127.0.0.1:8000/track/{$updatedOrder->order_number}\n";
    echo "========================================================\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

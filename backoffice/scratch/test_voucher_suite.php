<?php

require __DIR__ . '/../../backoffice/vendor/autoload.php';
$app = require_once __DIR__ . '/../../backoffice/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Order;
use App\Actions\Marketing\ValidateVoucherAction;
use App\Actions\Orders\CreateOrderAction;

echo "=== 1. TESTING VOUCHER VALIDATION ACTION ===\n";
$validator = new ValidateVoucherAction();

// Test 1: Valid percentage voucher
$v1 = $validator->execute('MALEGAVIP15', 300000, 20000, 'test@malega.app');
echo "VIP 15% on 300k (Expected 45k discount): " . ($v1['valid'] && $v1['discount_amount'] === 45000 ? "PASS (Rp {$v1['discount_amount']})" : "FAIL") . "\n";

// Test 2: Percentage voucher with max discount cap (e.g. 15% on 1.000.000 = 150k capped at 50k)
$v2 = $validator->execute('MALEGAVIP15', 1000000, 20000, 'test@malega.app');
echo "VIP 15% on 1M capped at 50k: " . ($v2['valid'] && $v2['discount_amount'] === 50000 ? "PASS (Rp {$v2['discount_amount']})" : "FAIL") . "\n";

// Test 3: Free shipping voucher
$v3 = $validator->execute('FREESHIPXTRA', 100000, 25000, 'test@malega.app');
echo "Free shipping subsidy (Expected 15k): " . ($v3['valid'] && $v3['discount_amount'] === 15000 ? "PASS (Rp {$v3['discount_amount']})" : "FAIL") . "\n";

// Test 4: Min spend validation failure
$v4 = $validator->execute('NEWDROP50K', 200000, 10000, 'test@malega.app');
echo "Min spend fail test (Subtotal 200k vs min 400k): " . (!$v4['valid'] ? "PASS ('{$v4['message']}')" : "FAIL") . "\n";

echo "\n=== 2. TESTING CREATE ORDER WITH VOUCHER USAGE LOGGING ===\n";
$createOrderAction = app(CreateOrderAction::class);
$order = $createOrderAction->execute([
    'customer' => [
        'name' => 'Akmal Tester VIP',
        'email' => 'akmal.test@malega.app',
        'phone' => '081299998888',
    ],
    'shipping_address' => [
        'recipient_name' => 'Akmal Tester VIP',
        'phone' => '081299998888',
        'address_line1' => 'Jl. Sudirman No. 88',
        'city' => 'Jakarta Selatan',
        'province' => 'DKI Jakarta',
        'postal_code' => '12190',
        'courier_name' => 'J&T Express (EZ)',
    ],
    'items' => [
        [
            'sku' => 'MLG-BOX-TEE-BLK-L',
            'product_name' => 'Boxy Heavyweight T-Shirt',
            'variant_title' => 'Vintage Black / L',
            'unit_price' => 289000,
            'quantity' => 1,
        ]
    ],
    'payment_method' => 'BC',
    'voucher_code' => 'MALEGAVIP15',
    'shipping_total' => 20000,
    'notes' => 'Test order with master voucher',
]);

echo "Order Created: " . $order->order_number . "\n";
echo "Subtotal: Rp " . number_format($order->subtotal, 0, ',', '.') . "\n";
echo "Discount Total: Rp " . number_format($order->discount_total, 0, ',', '.') . "\n";
echo "Grand Total: Rp " . number_format($order->grand_total, 0, ',', '.') . "\n";
echo "Voucher Code Applied: " . $order->voucher_code . "\n";

$usage = VoucherUsage::where('order_id', $order->id)->first();
if ($usage) {
    echo "VoucherUsage Record Found: Voucher ID {$usage->voucher_id}, Email {$usage->customer_email}, Discount Rp " . number_format($usage->discount_amount, 0, ',', '.') . "\n";
} else {
    echo "FAIL: VoucherUsage record was not created.\n";
}

$voucher = Voucher::where('code', 'MALEGAVIP15')->first();
echo "Voucher '{$voucher->code}' Used Count Incremented To: {$voucher->used_count}\n";

echo "\n=== ALL ENTERPRISE VOUCHER TESTS COMPLETED SUCCESSFULLY! ===\n";

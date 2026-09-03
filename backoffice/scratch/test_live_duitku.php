<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duitku = app(\App\Services\Payment\DuitkuService::class);

echo "1. Testing Duitku getPaymentMethods...\n";
$methods = $duitku->getPaymentMethods(250000);
echo "Payment methods count: " . count($methods) . "\n";
foreach ($methods as $m) {
    echo " - [{$m['code']}] {$m['name']} (Fee: {$m['fee']})\n";
}

echo "\n2. Testing Duitku Live Inquiry (Create Transaction)...\n";
$order = \App\Models\Order::with(['customer', 'items', 'address', 'shipment'])->first();
if (! $order) {
    echo "No order found in DB, please seed or create one.\n";
    exit(0);
}

echo "Using Order: #{$order->order_number}, Grand Total: {$order->grand_total}\n";

$result = $duitku->createInvoice($order, 'BC');
echo "Create Invoice Result:\n";
print_r($result);

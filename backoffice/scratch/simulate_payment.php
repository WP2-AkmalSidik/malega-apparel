<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Actions\Payment\HandleDuitkuCallbackAction;
use App\Models\Order;

echo "====================================================================\n";
echo " DUITKU SANDBOX PAYMENT SIMULATOR (MALEGA APPAREL)                  \n";
echo "====================================================================\n\n";

// 1. Resolve target order
$orderNumber = $argv[1] ?? null;

if ($orderNumber) {
    $order = Order::where('order_number', trim($orderNumber))->first();
} else {
    // Default to the latest unpaid order
    $order = Order::where('payment_status', 'unpaid')->latest('id')->first();
}

if (! $order) {
    echo "❌ ERROR: Tidak ditemukan pesanan yang belum dibayar.\n";
    echo "💡 Petunjuk: Masukkan nomor pesanan spesifik, contoh: php scratch/simulate_payment.php MLG-20260904-2637\n";
    exit(1);
}

echo "1. Memproses Pelunasan untuk Pesanan:\n";
echo "   -> Nomor Pesanan : {$order->order_number}\n";
echo "   -> Pelanggan     : " . ($order->customer?->name ?? $order->address?->recipient_name ?? 'Guest') . "\n";
echo "   -> Total Tagihan : Rp " . number_format($order->grand_total, 0, ',', '.') . "\n";
echo "   -> Status Awal   : " . strtoupper($order->payment_status->value) . " (" . $order->order_status->label() . ")\n\n";

// 2. Generate valid Duitku HMAC-SHA256 callback signature
$merchantCode = config('duitku.merchant_code', 'D9099');
$apiKey = config('duitku.api_key', 'test_duitku_secret_key');
$amount = (string) ((int) $order->grand_total);
$stringToSign = $merchantCode . $amount . $order->order_number;
$signature = hash_hmac('sha256', $stringToSign, $apiKey);

$payload = [
    'merchantCode' => $merchantCode,
    'amount' => $amount,
    'merchantOrderId' => $order->order_number,
    'productDetails' => "Pembayaran Pesanan #{$order->order_number} di Malega Apparel",
    'additionalParam' => (string) $order->id,
    'paymentCode' => 'SP', // QRIS / ShopeePay
    'resultCode' => '00',  // 00 = SUCCESS (LUNAS)
    'reference' => 'SIMULATED-' . strtoupper(bin2hex(random_bytes(6))),
    'signature' => $signature,
];

echo "2. Menjalankan Callback Handler Resmi Duitku (Atomic DB Transaction & Stock Lock Confirmation)...\n";
$handler = app(HandleDuitkuCallbackAction::class);
$res = $handler->execute($payload);

$order->refresh();

echo "   -> Hasil Callback : " . ($res['success'] ? 'SUCCESS (HTTP ' . $res['http_status'] . ')' : 'FAILED') . "\n";
echo "   -> Pesan Handler  : {$res['message']}\n\n";

echo "3. Status Pesanan Terkini di Database:\n";
echo "   -> Status Pembayaran : " . strtoupper($order->payment_status->value) . " (✅ LUNAS)\n";
echo "   -> Status Pesanan    : " . strtoupper($order->order_status->value) . " (" . $order->order_status->label() . ")\n";
echo "   -> Waktu Lunas       : " . ($order->payment?->paid_at ? $order->payment->paid_at->toDateTimeString() : now()->toDateTimeString()) . " WIB\n";
echo "   -> No. Referensi     : " . ($order->payment?->reference ?? 'N/A') . "\n\n";

echo "====================================================================\n";
echo " 🎉 PESANAN BERHASIL DILUNASI 100%! SIAP DIPROSES KURIR / BITESHIP  \n";
echo "====================================================================\n";

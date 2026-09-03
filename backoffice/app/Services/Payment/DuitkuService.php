<?php

namespace App\Services\Payment;

use App\Models\Order;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    protected string $merchantCode;

    protected string $apiKey;

    protected string $environment;

    protected string $baseUrl;

    protected string $callbackUrl;

    protected string $returnUrl;

    protected int $expiryPeriod;

    public function __construct()
    {
        $this->merchantCode = (string) config('duitku.merchant_code', 'D9099');
        $this->apiKey = (string) config('duitku.api_key', '');
        $this->environment = (string) config('duitku.environment', 'sandbox');
        $this->baseUrl = $this->environment === 'production'
            ? (string) config('duitku.production_base_url', 'https://passport.duitku.com/webapi/api/merchant')
            : (string) config('duitku.sandbox_base_url', 'https://sandbox.duitku.com/webapi/api/merchant');

        $this->callbackUrl = (string) config('duitku.callback_url', 'https://malega.my.id/api/v1/webhooks/duitku');
        $this->returnUrl = (string) config('duitku.return_url', 'https://store.malega.my.id/order-confirmation');
        $this->expiryPeriod = (int) config('duitku.expiry_period', 1440);
    }

    /**
     * Create / Request Duitku Invoice Transaction (API v2).
     *
     * @return array{success: bool, status_code: ?string, status_message: ?string, payment_url: ?string, reference: ?string, va_number: ?string, qr_string: ?string, raw: array}
     */
    public function createInvoice(
        Order $order,
        string $paymentMethod = 'VC',
        ?string $returnUrl = null,
        ?string $callbackUrl = null
    ): array {
        $paymentAmount = (int) $order->grand_total;
        $merchantOrderId = $order->order_number;
        $returnUrl = $returnUrl ?: $this->returnUrl;
        $callbackUrl = $callbackUrl ?: $this->callbackUrl;

        // Formula HMAC-SHA256: stringToSign = merchantCode + merchantOrderId + paymentAmount
        $stringToSign = $this->merchantCode . $merchantOrderId . $paymentAmount;
        $signature = hash_hmac('sha256', $stringToSign, $this->apiKey);

        $customerName = $order->address?->recipient_name ?? $order->customer?->name ?? 'Pelanggan Malega';
        $email = $order->customer?->email ?? 'customer@malega.id';
        $phoneNumber = $order->address?->phone ?? $order->customer?->phone ?? '081234567890';

        // Split customer name
        $nameParts = explode(' ', trim($customerName), 2);
        $firstName = $nameParts[0] ?? 'Pelanggan';
        $lastName = $nameParts[1] ?? 'Malega';

        $addressData = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => $order->address?->address_line1 ?? 'Jl. Malega No. 1',
            'city' => $order->address?->city ?? 'Jakarta',
            'postalCode' => $order->address?->postal_code ?? '10000',
            'phone' => $phoneNumber,
            'countryCode' => 'ID',
        ];

        // Format Items for Duitku
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'name' => mb_substr($item->product_name . ' (' . $item->variant_title . ')', 0, 50),
                'price' => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
            ];
        }

        if ($order->shipping_total > 0) {
            $itemDetails[] = [
                'name' => 'Ongkos Kirim (' . ($order->shipment?->courier_company ?? $order->address?->courier_name ?? 'Kurir') . ')',
                'price' => (int) $order->shipping_total,
                'quantity' => 1,
            ];
        }

        if ($order->discount_total > 0) {
            $itemDetails[] = [
                'name' => 'Potongan Diskon Promo Malega',
                'price' => -((int) $order->discount_total),
                'quantity' => 1,
            ];
        }

        $payload = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => "Pembayaran Pesanan #{$order->order_number} di Malega Apparel",
            'additionalParam' => (string) $order->id,
            'merchantUserInfo' => $email,
            'customerVaName' => mb_substr($customerName, 0, 30),
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'phoneNumber' => $phoneNumber,
                'billingAddress' => $addressData,
                'shippingAddress' => $addressData,
            ],
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl . '?order_number=' . urlencode($merchantOrderId),
            'signature' => $signature,
            'expiryPeriod' => $this->expiryPeriod,
        ];

        try {
            $response = $this->client()
                ->post("{$this->baseUrl}/v2/inquiry", $payload);

            $body = $response->json() ?? [];

            Log::info('Duitku createInvoice response', [
                'order' => $merchantOrderId,
                'status' => $response->status(),
                'body' => $body,
            ]);

            $statusCode = $body['statusCode'] ?? null;
            $isSuccess = $response->successful() && ($statusCode === '00' || isset($body['paymentUrl']));

            return [
                'success' => $isSuccess,
                'status_code' => $statusCode,
                'status_message' => $body['statusMessage'] ?? null,
                'payment_url' => $body['paymentUrl'] ?? null,
                'reference' => $body['reference'] ?? null,
                'va_number' => $body['vaNumber'] ?? null,
                'qr_string' => $body['qrString'] ?? null,
                'raw' => $body,
            ];
        } catch (Exception $e) {
            Log::error('Duitku createInvoice error: ' . $e->getMessage(), [
                'order' => $merchantOrderId,
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'status_code' => '99',
                'status_message' => $e->getMessage(),
                'payment_url' => null,
                'reference' => null,
                'va_number' => null,
                'qr_string' => null,
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Fetch available payment methods and channel fee estimates from Duitku.
     *
     * @return array<int, array{code: string, name: string, image: string, fee: int}>
     */
    public function getPaymentMethods(int $amount = 100000): array
    {
        $datetime = date('Y-m-d H:i:s');
        $signature = hash('sha256', $this->merchantCode . $amount . $datetime . $this->apiKey);

        $payload = [
            'merchantcode' => $this->merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature,
        ];

        try {
            $response = $this->client()
                ->post("{$this->baseUrl}/paymentmethod/getpaymentmethod", $payload);

            $data = $response->json();
            $methods = $data['paymentFee'] ?? [];

            return array_map(fn ($m) => [
                'code' => $m['paymentMethod'] ?? '',
                'name' => $m['paymentName'] ?? '',
                'image' => $m['paymentImage'] ?? '',
                'fee' => (int) ($m['totalFee'] ?? 0),
            ], $methods);
        } catch (Exception $e) {
            Log::warning('Failed to fetch Duitku payment methods: ' . $e->getMessage());

            // Default fallback channels
            return [
                ['code' => 'BC', 'name' => 'BCA Virtual Account', 'image' => '', 'fee' => 4000],
                ['code' => 'M2', 'name' => 'Mandiri Virtual Account', 'image' => '', 'fee' => 4000],
                ['code' => 'BR', 'name' => 'BRI Virtual Account', 'image' => '', 'fee' => 3000],
                ['code' => 'B1', 'name' => 'BNI Virtual Account', 'image' => '', 'fee' => 3000],
                ['code' => 'QR', 'name' => 'QRIS (ShopeePay, GoPay, OVO, Dana)', 'image' => '', 'fee' => 1500],
                ['code' => 'VC', 'name' => 'Credit Card (Visa / Mastercard / JCB)', 'image' => '', 'fee' => 5000],
            ];
        }
    }

    /**
     * Check transaction status directly from Duitku server.
     *
     * @return array{status: string, reference: ?string, amount: ?int, raw: array}
     */
    public function checkTransactionStatus(string $merchantOrderId): array
    {
        $signature = hash('md5', $this->merchantCode . $merchantOrderId . $this->apiKey);

        $payload = [
            'merchantCode' => $this->merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature' => $signature,
        ];

        try {
            $response = $this->client()
                ->post("{$this->baseUrl}/transactionStatus", $payload);

            $data = $response->json() ?? [];
            $statusCode = $data['statusCode'] ?? null;

            $status = match ($statusCode) {
                '00' => 'success',
                '01' => 'pending',
                '02' => 'cancelled',
                default => 'unknown'
            };

            return [
                'status' => $status,
                'status_code' => $statusCode,
                'status_message' => $data['statusMessage'] ?? null,
                'reference' => $data['reference'] ?? null,
                'amount' => isset($data['amount']) ? (int) $data['amount'] : null,
                'raw' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Duitku checkTransactionStatus error: ' . $e->getMessage());

            return [
                'status' => 'unknown',
                'status_code' => '99',
                'status_message' => $e->getMessage(),
                'reference' => null,
                'amount' => null,
                'raw' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Verify Duitku Webhook Callback Signature (HMAC-SHA256 / MD5 fallback).
     */
    public function verifyCallbackSignature(array $payload): bool
    {
        $merchantCode = (string) ($payload['merchantCode'] ?? '');
        $amount = (string) ($payload['amount'] ?? '');
        $merchantOrderId = (string) ($payload['merchantOrderId'] ?? '');
        $incomingSignature = (string) ($payload['signature'] ?? '');

        if (empty($merchantCode) || empty($amount) || empty($merchantOrderId) || empty($incomingSignature)) {
            return false;
        }

        // 1. Primary Check: Official HMAC-SHA256
        $stringToSign = $merchantCode . $amount . $merchantOrderId;
        $expectedHmac = hash_hmac('sha256', $stringToSign, $this->apiKey);

        if (hash_equals(strtolower($expectedHmac), strtolower($incomingSignature))) {
            return true;
        }

        // 2. Secondary Fallback Check: MD5 legacy
        $expectedMd5 = md5($merchantCode . $amount . $merchantOrderId . $this->apiKey);
        if (hash_equals(strtolower($expectedMd5), strtolower($incomingSignature))) {
            return true;
        }

        return false;
    }

    /**
     * Configure HTTP Client instance with retry logic.
     */
    protected function client(): PendingRequest
    {
        return Http::timeout(15)
            ->retry(2, 500)
            ->asJson()
            ->acceptJson();
    }
}

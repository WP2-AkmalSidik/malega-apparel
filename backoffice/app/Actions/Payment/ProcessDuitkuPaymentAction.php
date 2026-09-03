<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\DuitkuService;

class ProcessDuitkuPaymentAction
{
    public function __construct(
        protected DuitkuService $duitkuService
    ) {}

    /**
     * Create or request Duitku payment invoice for an order.
     *
     * @return array{success: bool, payment_url: ?string, reference: ?string, va_number: ?string, qr_string: ?string, message: string}
     */
    public function execute(Order $order, string $paymentMethod = 'VC', ?string $returnUrl = null): array
    {
        $res = $this->duitkuService->createInvoice($order, $paymentMethod, $returnUrl);

        $grossAmount = (int) $order->grand_total;
        $adminFee = Payment::estimateGatewayFee($paymentMethod, $grossAmount);
        $netAmount = max(0, $grossAmount - $adminFee);

        // Record or update Payment model
        Payment::updateOrCreate(
            [
                'merchant_order_id' => $order->order_number,
            ],
            [
                'order_id' => $order->id,
                'payment_gateway' => 'duitku',
                'reference' => $res['reference'],
                'payment_method' => $paymentMethod,
                'payment_method_name' => $this->resolvePaymentMethodName($paymentMethod),
                'amount' => $grossAmount,
                'admin_fee' => $adminFee,
                'net_amount' => $netAmount,
                'status' => $res['success'] ? 'pending' : 'failed',
                'payment_url' => $res['payment_url'],
                'va_number' => $res['va_number'],
                'qr_string' => $res['qr_string'],
                'payload' => $res['raw'] ?? [],
                'expires_at' => now()->addMinutes((int) config('duitku.expiry_period', 1440)),
            ]
        );

        return [
            'success' => $res['success'],
            'payment_url' => $res['payment_url'],
            'reference' => $res['reference'],
            'va_number' => $res['va_number'],
            'qr_string' => $res['qr_string'],
            'message' => $res['status_message'] ?? ($res['success'] ? 'Invoice pembayaran berhasil dibuat.' : 'Gagal membuat invoice Duitku.'),
        ];
    }

    protected function resolvePaymentMethodName(string $code): string
    {
        return match (strtoupper($code)) {
            'VC' => 'Credit Card',
            'BC' => 'BCA Virtual Account',
            'M2' => 'Mandiri Virtual Account',
            'BR' => 'BRI Virtual Account',
            'B1' => 'BNI Virtual Account',
            'BT' => 'Permata Virtual Account',
            'NC' => 'BNC (Neo Bank) Virtual Account',
            'QR', 'SP' => 'QRIS Real-Time',
            'OV' => 'OVO Instant Pay',
            'DA' => 'DANA Wallet',
            'SA' => 'ShopeePay App',
            'LA' => 'LinkAja Wallet',
            default => "Duitku Payment ({$code})"
        };
    }
}

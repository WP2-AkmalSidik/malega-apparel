<?php

namespace App\Actions\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Services\Payment\DuitkuService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleDuitkuCallbackAction
{
    public function __construct(
        protected DuitkuService $duitkuService
    ) {}

    /**
     * Handle incoming Duitku Webhook notification with strict signature verification & idempotency.
     *
     * @return array{success: bool, message: string, http_status: int}
     */
    public function execute(array $payload): array
    {
        Log::info('Duitku Webhook Received', ['payload' => $payload]);

        // 1. Verify Signature
        if (! $this->duitkuService->verifyCallbackSignature($payload)) {
            Log::warning('Duitku Webhook: Invalid Signature', ['payload' => $payload]);

            return [
                'success' => false,
                'message' => 'Bad Signature',
                'http_status' => 400,
            ];
        }

        $merchantOrderId = (string) ($payload['merchantOrderId'] ?? '');
        $amount = (int) ($payload['amount'] ?? 0);
        $resultCode = (string) ($payload['resultCode'] ?? '');
        $reference = (string) ($payload['reference'] ?? '');

        // 2. Find Order
        $order = Order::with(['payment', 'items'])->where('order_number', $merchantOrderId)->first();

        if (! $order) {
            Log::warning("Duitku Webhook: Order {$merchantOrderId} not found", ['payload' => $payload]);

            return [
                'success' => false,
                'message' => 'Order Not Found',
                'http_status' => 404,
            ];
        }

        // 3. Validate Amount Integrity (Anti-Fraud)
        if ($amount !== (int) $order->grand_total) {
            Log::error("Duitku Webhook: Amount Mismatch for {$merchantOrderId}. Expected: {$order->grand_total}, Received: {$amount}");

            return [
                'success' => false,
                'message' => 'Amount Mismatch',
                'http_status' => 400,
            ];
        }

        // 4. Idempotency check: If already paid, return success immediately
        if ($order->payment_status === PaymentStatus::Paid && $resultCode === '00') {
            Log::info("Duitku Webhook: Order {$merchantOrderId} is already paid. Skipping redundant processing.");

            return [
                'success' => true,
                'message' => 'Order Already Paid (Idempotent)',
                'http_status' => 200,
            ];
        }

        // 5. Process Payment Status Transition inside Atomic DB Transaction
        DB::transaction(function () use ($order, $payload, $resultCode, $reference) {
            $isSuccess = ($resultCode === '00');
            $isPending = ($resultCode === '01');

            $grossAmount = (int) $order->grand_total;
            $paymentMethodCode = $payload['paymentCode'] ?? $payload['paymentMethod'] ?? 'VC';
            $adminFee = Payment::estimateGatewayFee($paymentMethodCode, $grossAmount);
            $netAmount = max(0, $grossAmount - $adminFee);

            // Update Payment record
            Payment::updateOrCreate(
                ['merchant_order_id' => $order->order_number],
                [
                    'order_id' => $order->id,
                    'payment_gateway' => 'duitku',
                    'amount' => $grossAmount,
                    'admin_fee' => $adminFee,
                    'net_amount' => $netAmount,
                    'payment_method' => $paymentMethodCode,
                    'reference' => $reference,
                    'status' => $isSuccess ? 'success' : ($isPending ? 'pending' : 'failed'),
                    'callback_payload' => $payload,
                    'paid_at' => $isSuccess ? now() : null,
                ]
            );

            if ($isSuccess) {
                $previousPaymentStatus = $order->payment_status->value;
                $previousOrderStatus = $order->order_status->value;

                $order->payment_status = PaymentStatus::Paid;

                if ($order->order_status === OrderStatus::Pending) {
                    $order->order_status = OrderStatus::Processing;
                }

                $order->save();

                // Audit Log History
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => "payment:{$previousPaymentStatus}|order:{$previousOrderStatus}",
                    'to_status' => "payment:paid|order:{$order->order_status->value}",
                    'notes' => "Pembayaran Duitku berhasil diverifikasi via Webhook (Ref: {$reference})",
                ]);

                Log::info("Duitku Webhook: Order {$order->order_number} marked as PAID.");
            } elseif (! $isPending) {
                $order->payment_status = PaymentStatus::Failed;
                $order->save();

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'from_status' => $order->payment_status->value,
                    'to_status' => 'failed',
                    'notes' => "Pembayaran Duitku gagal/dibatalkan (ResultCode: {$resultCode})",
                ]);
            }
        });

        return [
            'success' => true,
            'message' => 'Success',
            'http_status' => 200,
        ];
    }
}

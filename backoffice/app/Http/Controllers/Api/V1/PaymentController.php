<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Payment\ProcessDuitkuPaymentAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get available payment methods from Duitku.
     */
    public function methods(Request $request, DuitkuService $duitkuService): JsonResponse
    {
        $amount = (int) $request->input('amount', 100000);
        $methods = $duitkuService->getPaymentMethods($amount);

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }

    /**
     * Request Duitku payment invoice for an existing order.
     */
    public function createInvoice(Request $request, ProcessDuitkuPaymentAction $processPayment): JsonResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string'],
            'payment_method' => ['nullable', 'string', 'max:10'],
            'return_url' => ['nullable', 'url'],
        ]);

        $order = Order::with(['customer', 'items', 'address'])
            ->where('order_number', trim($validated['order_number']))
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($order->payment_status->value === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan ini sudah lunas.',
                'data' => [
                    'order_number' => $order->order_number,
                    'status' => 'paid',
                ],
            ]);
        }

        $paymentMethod = $validated['payment_method'] ?? 'VC';
        $returnUrl = $validated['return_url'] ?? null;

        $result = $processPayment->execute($order, $paymentMethod, $returnUrl);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => [
                'order_number' => $order->order_number,
                'payment_url' => $result['payment_url'],
                'reference' => $result['reference'],
                'va_number' => $result['va_number'],
                'qr_string' => $result['qr_string'],
            ],
        ], $result['success'] ? 200 : 400);
    }

    /**
     * Check payment status for an order.
     */
    public function status(string $orderNumber, DuitkuService $duitkuService): JsonResponse
    {
        $order = Order::with('payment')
            ->where('order_number', trim($orderNumber))
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // If unpaid and payment record exists, check status with Duitku
        if ($order->payment_status->value !== 'paid' && $order->payment) {
            $duitkuStatus = $duitkuService->checkTransactionStatus($order->order_number);
            if ($duitkuStatus['status'] === 'success') {
                $order->payment_status = \App\Enums\PaymentStatus::Paid;
                if ($order->order_status === \App\Enums\OrderStatus::Pending) {
                    $order->order_status = \App\Enums\OrderStatus::Processing;
                }
                $order->save();
                $order->payment->update(['status' => 'success', 'paid_at' => now()]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'payment_status' => [
                    'code' => $order->payment_status->value,
                    'label' => $order->payment_status->label(),
                ],
                'order_status' => [
                    'code' => $order->order_status->value,
                    'label' => $order->order_status->label(),
                ],
                'grand_total' => $order->grand_total,
                'formatted_grand_total' => $order->formatted_grand_total,
                'payment' => $order->payment ? [
                    'reference' => $order->payment->reference,
                    'payment_method' => $order->payment->payment_method,
                    'payment_method_name' => $order->payment->payment_method_name,
                    'payment_url' => $order->payment->payment_url,
                    'va_number' => $order->payment->va_number,
                    'status' => $order->payment->status,
                    'paid_at' => $order->payment->paid_at?->toIso8601String(),
                ] : null,
            ],
        ]);
    }
}

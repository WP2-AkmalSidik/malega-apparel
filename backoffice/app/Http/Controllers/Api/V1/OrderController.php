<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Orders\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorefrontCheckoutRequest;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Storefront Checkout endpoint (Server-Authoritative ADR-004 & ADR-006).
     */
    public function checkout(StorefrontCheckoutRequest $request, CreateOrderAction $createOrder): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['source'] = 'storefront';
            $data['address'] = $data['shipping_address'];
            unset($data['shipping_address']);

            $order = $createOrder->execute($data);

            return response()->json([
                'success' => true,
                'message' => "Pesanan #{$order->order_number} berhasil dibuat. Silakan lakukan pembayaran.",
                'data' => new OrderResource($order),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first() ?? 'Gagal membuat pesanan.',
                'errors' => $e->validator->errors(),
            ], 422);
        }
    }

    /**
     * Public Order Tracking endpoint by canonical order number (MLG-YYYYMMDD-XXXX).
     */
    public function track(string $orderNumber): JsonResponse
    {
        $order = Order::with(['customer', 'items', 'address', 'statusHistories'])
            ->where('order_number', trim($orderNumber))
            ->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan nomor tersebut tidak ditemukan di sistem Malega Apparel.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Informasi status pesanan berhasil ditemukan.',
            'data' => new OrderResource($order),
        ]);
    }
}

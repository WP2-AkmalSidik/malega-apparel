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
     * Public Order Tracking endpoint by canonical order number or AWB/Resi ID.
     */
    public function track(string $orderNumber, \App\Actions\Logistics\SyncBiteshipTrackingAction $syncAction): JsonResponse
    {
        $term = trim($orderNumber);

        // 1. Search by canonical order number
        $order = Order::with(['customer', 'items', 'address', 'shipment'])
            ->where('order_number', $term)
            ->first();

        // 2. Search by waybill_id (AWB Resi)
        if (! $order) {
            $shipment = \App\Models\Shipment::where('waybill_id', $term)->first();
            if ($shipment) {
                $order = $shipment->order()->with(['customer', 'items', 'address', 'shipment'])->first();
            }
        }

        // 3. Search by address tracking_number
        if (! $order) {
            $order = Order::with(['customer', 'items', 'address', 'shipment'])
                ->whereHas('address', fn ($a) => $a->where('tracking_number', $term))
                ->first();
        }

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan atau nomor resi tidak ditemukan di sistem Malega Apparel.',
            ], 404);
        }

        // Auto-sync real-time tracking if shipment exists
        if ($order->shipment) {
            $syncAction->execute($order);
            $order->refresh();
            $order->load(['customer', 'items', 'address', 'shipment']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Informasi status pesanan berhasil ditemukan.',
            'data' => new OrderResource($order),
        ]);
    }
}

<?php

namespace App\Actions\Logistics;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class HandleBiteshipWebhookAction
{
    /**
     * Process inbound webhook from Biteship with idempotency.
     *
     * @param array<string, mixed> $payload
     * @return array{
     *     success: bool,
     *     message: string,
     *     order_id?: int|null
     * }
     */
    public function execute(array $payload): array
    {
        $event = $payload['event'] ?? 'order.status';
        $orderId = $payload['order_id'] ?? null;
        $trackingId = $payload['courier_tracking_id'] ?? null;
        $waybillId = $payload['courier_waybill_id'] ?? null;
        $status = strtolower($payload['status'] ?? '');

        Log::info("Biteship Webhook Received: {$event}", $payload);

        // Find shipment by Biteship order id, tracking id, or waybill
        $shipment = Shipment::where('biteship_order_id', $orderId)
            ->orWhere('biteship_tracking_id', $trackingId)
            ->orWhere('waybill_id', $waybillId)
            ->first();

        if (! $shipment) {
            Log::warning("Biteship Webhook: Shipment not found for order_id={$orderId}, waybill={$waybillId}");

            return [
                'success' => false,
                'message' => 'Shipment record not found in system.',
            ];
        }

        // Idempotency check: if status hasn't changed, return early
        if ($shipment->status === $status && ! empty($status)) {
            return [
                'success' => true,
                'message' => 'Event already processed (Idempotent).',
                'order_id' => $shipment->order_id,
            ];
        }

        $history = $shipment->tracking_history ?: [];
        $note = $payload['status_note'] ?? "Status updated to: {$status}";
        $history[] = [
            'status' => $status,
            'note' => $note,
            'updated_at' => now()->toIso8601String(),
        ];

        $updateData = [
            'status' => $status ?: $shipment->status,
            'tracking_history' => $history,
        ];

        if ($waybillId && empty($shipment->waybill_id)) {
            $updateData['waybill_id'] = $waybillId;
        }

        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $shipment->update($updateData);

        // Update Order model if delivered
        $order = $shipment->order;
        if ($order) {
            if ($waybillId) {
                $order->address?->update(['tracking_number' => $waybillId]);
            }

            if ($status === 'delivered') {
                $order->update([
                    'fulfillment_status' => FulfillmentStatus::Delivered,
                    'order_status' => OrderStatus::Completed,
                ]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => null,
                    'from_status' => 'fulfilled',
                    'to_status' => 'delivered',
                    'notes' => 'Pesanan terkirim via Webhook Biteship',
                ]);
            }
        }

        return [
            'success' => true,
            'message' => "Webhook processed successfully for Order #{$order?->order_number}.",
            'order_id' => $shipment->order_id,
        ];
    }
}

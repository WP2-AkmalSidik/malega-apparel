<?php

namespace App\Actions\Logistics;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Services\Logistics\BiteshipService;
use Exception;
use Illuminate\Support\Facades\Log;

class SyncBiteshipTrackingAction
{
    public function __construct(
        protected BiteshipService $biteshipService
    ) {}

    /**
     * Fetch latest tracking information from Biteship and update shipment record.
     *
     * @return array{
     *     success: bool,
     *     shipment: Shipment|null,
     *     tracking: array<string, mixed>|null,
     *     message: string
     * }
     */
    public function execute(Order $order): array
    {
        $order->loadMissing('shipment');
        $shipment = $order->shipment;

        if (! $shipment || (! $shipment->biteship_tracking_id && ! $shipment->biteship_order_id)) {
            return [
                'success' => false,
                'shipment' => $shipment,
                'tracking' => null,
                'message' => 'Pesanan belum memiliki data pengiriman Biteship yang valid.',
            ];
        }

        try {
            $trackingData = null;

            if ($shipment->biteship_tracking_id) {
                $trackingData = $this->biteshipService->getTracking($shipment->biteship_tracking_id);
            } elseif ($shipment->biteship_order_id) {
                $orderData = $this->biteshipService->getOrder($shipment->biteship_order_id);
                $trackingData = [
                    'status' => $orderData['status'] ?? $shipment->status,
                    'history' => $orderData['courier']['history'] ?? $shipment->tracking_history,
                    'link' => $orderData['courier']['link'] ?? $shipment->tracking_url,
                ];
            }

            if (! $trackingData) {
                return [
                    'success' => false,
                    'shipment' => $shipment,
                    'tracking' => null,
                    'message' => 'Tidak dapat mengambil status pelacakan dari Biteship.',
                ];
            }

            $newStatus = $trackingData['status'] ?? $shipment->status;
            $history = $trackingData['history'] ?? $shipment->tracking_history;
            $trackingLink = $trackingData['link'] ?? $shipment->tracking_url;

            $shipment->update([
                'status' => $newStatus,
                'tracking_history' => $history,
                'tracking_url' => $trackingLink,
                'delivered_at' => $newStatus === 'delivered' ? ($shipment->delivered_at ?: now()) : $shipment->delivered_at,
            ]);

            // If shipment is delivered, optionally transition order
            if ($newStatus === 'delivered' && $order->fulfillment_status !== FulfillmentStatus::Delivered) {
                $order->update([
                    'fulfillment_status' => FulfillmentStatus::Delivered,
                    'order_status' => OrderStatus::Completed,
                ]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'from_status' => 'fulfilled',
                    'to_status' => 'delivered',
                    'notes' => 'Paket berhasil terkirim ke customer (Dikonfirmasi oleh sistem pelacakan Biteship)',
                ]);
            }

            return [
                'success' => true,
                'shipment' => $shipment->fresh(),
                'tracking' => $trackingData,
                'message' => 'Data pelacakan berhasil disinkronkan.',
            ];
        } catch (Exception $e) {
            Log::error('SyncBiteshipTrackingAction error: ' . $e->getMessage());

            return [
                'success' => false,
                'shipment' => $shipment,
                'tracking' => null,
                'message' => 'Gagal menyinkronkan pelacakan: ' . $e->getMessage(),
            ];
        }
    }
}

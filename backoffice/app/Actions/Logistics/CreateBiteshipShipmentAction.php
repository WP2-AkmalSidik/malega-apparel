<?php

namespace App\Actions\Logistics;

use App\Actions\Inventory\FulfillStockAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Services\Logistics\BiteshipService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateBiteshipShipmentAction
{
    public function __construct(
        protected BiteshipService $biteshipService,
        protected FulfillStockAction $fulfillStock
    ) {}

    /**
     * Create Auto-AWB shipment via Biteship, deduct stock, and update order.
     *
     * @param array{
     *     courier_company: string,
     *     courier_type: string,
     *     notes?: string|null,
     *     user_id?: int|null
     * } $data
     *
     * @throws ValidationException|Exception
     */
    public function execute(Order $order, array $data): Order
    {
        $order->loadMissing(['address', 'customer', 'items.variant']);

        $address = $order->address;
        if (! $address) {
            throw ValidationException::withMessages([
                'address' => 'Alamat pengiriman pesanan belum lengkap.',
            ]);
        }

        $courierCompany = strtolower($data['courier_company'] ?? 'jne');
        $courierType = strtolower($data['courier_type'] ?? 'reg');

        // 1. Prepare Biteship API Payload
        $origin = config('biteship.origin');

        $items = [];
        foreach ($order->items as $item) {
            $weight = $item->variant?->weight_grams ?: 250; // default 250g per apparel
            $items[] = [
                'name' => "{$item->product_name} - {$item->variant_title}",
                'description' => 'Apparel / Fashion Product',
                'category' => 'fashion',
                'sku' => $item->sku,
                'value' => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'weight' => (int) $weight,
                'length' => 30,
                'width' => 20,
                'height' => 2,
            ];
        }

        $payload = [
            'origin_contact_name' => $origin['contact_name'],
            'origin_contact_phone' => $origin['contact_phone'],
            'origin_address' => $origin['address'],
            'origin_note' => $origin['note'] ?? 'Gudang Utama',
            'origin_postal_code' => (int) $origin['postal_code'],
            'destination_contact_name' => $address->recipient_name,
            'destination_contact_phone' => $address->phone,
            'destination_contact_email' => $order->customer?->email ?? 'customer@malegaapparel.com',
            'destination_address' => $address->address_line1 . ($address->address_line2 ? ', ' . $address->address_line2 : '') . ', ' . $address->city . ', ' . $address->province,
            'destination_postal_code' => (int) $address->postal_code,
            'courier_company' => $courierCompany,
            'courier_type' => $courierType,
            'delivery_type' => 'now',
            'order_note' => $data['notes'] ?? $order->notes ?? 'Malega Apparel Package',
            'items' => $items,
        ];

        // 2. Call Biteship Create Order API
        $biteshipRes = $this->biteshipService->createOrder($payload);

        $courierData = $biteshipRes['courier'] ?? [];
        $waybillId = $courierData['waybill_id'] ?? null;
        $trackingId = $courierData['tracking_id'] ?? null;
        $trackingUrl = $courierData['link'] ?? null;
        $biteshipOrderId = $biteshipRes['id'] ?? null;
        $shipmentFee = (int) ($courierData['shipment_fee'] ?? $biteshipRes['price'] ?? 0);
        $status = $biteshipRes['status'] ?? 'confirmed';
        $history = $courierData['history'] ?? [
            [
                'service_type' => $courierType,
                'status' => $status,
                'note' => "Resi otomatis dibuat via {$courierCompany} ({$courierType}). No. Resi: {$waybillId}",
                'updated_at' => now()->toIso8601String(),
            ]
        ];

        if (empty($waybillId)) {
            throw new Exception('Biteship tidak mengembalikan nomor resi (waybill_id). Silakan periksa kembali data kurir.');
        }

        // 3. Atomically persist Shipment, update Order, and deduct stock
        return DB::transaction(function () use ($order, $courierCompany, $courierType, $waybillId, $trackingId, $trackingUrl, $biteshipOrderId, $shipmentFee, $status, $history, $data, $origin, $address) {
            // Create or update Shipment record
            $shipment = Shipment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'biteship_order_id' => $biteshipOrderId,
                    'biteship_tracking_id' => $trackingId,
                    'courier_company' => strtoupper($courierCompany),
                    'courier_service_name' => strtoupper($courierType),
                    'waybill_id' => $waybillId,
                    'tracking_url' => $trackingUrl,
                    'shipment_fee' => $shipmentFee,
                    'status' => $status,
                    'shipper_snapshot' => $origin,
                    'destination_snapshot' => $address->toArray(),
                    'tracking_history' => $history,
                    'shipped_at' => now(),
                    'notes' => $data['notes'] ?? null,
                ]
            );

            // Update order address courier details
            $order->address?->update([
                'courier_name' => strtoupper($courierCompany) . ' (' . strtoupper($courierType) . ')',
                'tracking_number' => $waybillId,
            ]);

            // Deduct physical inventory stock if not already fulfilled
            if ($order->fulfillment_status !== FulfillmentStatus::Fulfilled) {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $inv = InventoryItem::where('variant_id', $item->variant_id)->first();
                        if ($inv) {
                            $this->fulfillStock->execute($inv, [
                                'quantity' => $item->quantity,
                                'order_id' => $order->id,
                                'reference_note' => "Auto-AWB Biteship ({$courierCompany} {$courierType}) No. Resi: {$waybillId}",
                                'user_id' => $data['user_id'] ?? auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // Update Order Status
            $order->fulfillment_status = FulfillmentStatus::Fulfilled;
            if ($order->order_status === OrderStatus::Pending) {
                $order->order_status = OrderStatus::Processing;
            }
            $order->save();

            // Record Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'from_status' => 'unfulfilled',
                'to_status' => 'fulfilled',
                'notes' => "Auto-AWB Biteship terbit: {$shipment->courier_company} ({$shipment->courier_service_name}) - Resi: {$waybillId}",
            ]);

            return $order->fresh(['customer', 'items', 'address', 'shipment', 'statusHistories']);
        });
    }
}

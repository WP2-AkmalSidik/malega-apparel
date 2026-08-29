<?php

namespace App\Actions\Orders;

use App\Actions\Inventory\FulfillStockAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateFulfillmentAction
{
    public function __construct(
        protected FulfillStockAction $fulfillStock
    ) {}

    /**
     * Update courier details, fulfill physical stock, and update fulfillment status.
     *
     * @param array{
     *     courier_name: string,
     *     tracking_number: string,
     *     user_id?: int|null
     * } $data
     *
     * @throws ValidationException
     */
    public function execute(Order $order, array $data): Order
    {
        if (empty($data['tracking_number'])) {
            throw ValidationException::withMessages([
                'tracking_number' => 'Nomor resi pengiriman wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($order, $data) {
            // Update address courier info
            $order->address?->update([
                'courier_name' => $data['courier_name'],
                'tracking_number' => $data['tracking_number'],
            ]);

            // Deduct physical stock on fulfillment if not already fulfilled
            if ($order->fulfillment_status !== FulfillmentStatus::Fulfilled) {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $inv = InventoryItem::where('variant_id', $item->variant_id)->first();
                        if ($inv) {
                            $this->fulfillStock->execute($inv, [
                                'quantity' => $item->quantity,
                                'order_id' => $order->id,
                                'reference_note' => "Pengiriman {$data['courier_name']} resi {$data['tracking_number']}",
                                'user_id' => $data['user_id'] ?? auth()->id(),
                            ]);
                        }
                    }
                }
            }

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
                'notes' => "Pesanan dikirim via {$data['courier_name']} (No. Resi: {$data['tracking_number']})",
            ]);

            return $order->fresh(['customer', 'items', 'address', 'statusHistories']);
        });
    }
}

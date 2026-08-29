<?php

namespace App\Actions\Orders;

use App\Actions\Inventory\ReleaseStockAction;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusAction
{
    public function __construct(
        protected ReleaseStockAction $releaseStock
    ) {}

    /**
     * Transition order status, handle stock release if cancelled, and record audit history.
     *
     * @param array{
     *     order_status?: OrderStatus|string|null,
     *     payment_status?: PaymentStatus|string|null,
     *     notes?: string|null,
     *     user_id?: int|null
     * } $data
     */
    public function execute(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $fromStatus = $order->order_status->value;
            $toStatus = isset($data['order_status'])
                ? ($data['order_status'] instanceof OrderStatus ? $data['order_status'] : OrderStatus::from($data['order_status']))
                : $order->order_status;

            // Handle Order Cancellation (Release Reserved Stock)
            if ($toStatus === OrderStatus::Cancelled && $order->order_status !== OrderStatus::Cancelled) {
                // If not yet fulfilled, release reserved stock
                if ($order->fulfillment_status->value === 'unfulfilled') {
                    foreach ($order->items as $item) {
                        if ($item->variant_id) {
                            $inv = InventoryItem::where('variant_id', $item->variant_id)->first();
                            if ($inv) {
                                $this->releaseStock->execute($inv, [
                                    'quantity' => $item->quantity,
                                    'order_id' => $order->id,
                                    'reference_note' => "Pelepasan stok pembatalan pesanan {$order->order_number}",
                                    'user_id' => $data['user_id'] ?? auth()->id(),
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle Order Completion (Update Customer Total Spend)
            if ($toStatus === OrderStatus::Completed && $order->order_status !== OrderStatus::Completed) {
                $order->customer?->increment('total_spend_amount', $order->grand_total);
            }

            $order->order_status = $toStatus;

            if (isset($data['payment_status'])) {
                $order->payment_status = $data['payment_status'] instanceof PaymentStatus
                    ? $data['payment_status']
                    : PaymentStatus::from($data['payment_status']);
            }

            $order->save();

            // Record Status History
            if ($fromStatus !== $toStatus->value || isset($data['payment_status'])) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'user_id' => $data['user_id'] ?? auth()->id(),
                    'from_status' => $fromStatus,
                    'to_status' => $toStatus->value,
                    'notes' => $data['notes'] ?? "Status pesanan diubah ke {$toStatus->label()}",
                ]);
            }

            return $order->fresh(['customer', 'items', 'address', 'statusHistories']);
        });
    }
}

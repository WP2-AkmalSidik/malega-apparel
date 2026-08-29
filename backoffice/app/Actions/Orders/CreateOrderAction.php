<?php

namespace App\Actions\Orders;

use App\Actions\Inventory\ReserveStockAction;
use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrderAction
{
    public function __construct(
        protected ReserveStockAction $reserveStock
    ) {}

    /**
     * Create an order with server-authoritative calculations and snapshots.
     *
     * @param array{
     *     customer: array{name: string, email: string, phone: string},
     *     items: array<int, array{variant_id: int, quantity: int}>,
     *     address: array{
     *         recipient_name: string,
     *         phone: string,
     *         address_line1: string,
     *         address_line2?: string|null,
     *         city: string,
     *         province: string,
     *         postal_code: string,
     *         courier_name?: string|null
     *     },
     *     source?: string,
     *     discount_total?: int,
     *     shipping_total?: int,
     *     tax_total?: int,
     *     notes?: string|null,
     *     user_id?: int|null
     * } $data
     *
     * @throws ValidationException
     */
    public function execute(array $data): Order
    {
        if (empty($data['items'])) {
            throw ValidationException::withMessages([
                'items' => 'Pesanan wajib memiliki minimal satu item produk.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            // 1. Find or create customer
            $customer = Customer::firstOrCreate(
                ['email' => trim(strtolower($data['customer']['email']))],
                [
                    'name' => $data['customer']['name'],
                    'phone' => $data['customer']['phone'],
                ]
            );

            // 2. Generate canonical Order Number (MLG-YYYYMMDD-XXXX)
            $datePrefix = date('Ymd');
            $randomDigits = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $orderNumber = "MLG-{$datePrefix}-{$randomDigits}";

            while (Order::where('order_number', $orderNumber)->exists()) {
                $randomDigits = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                $orderNumber = "MLG-{$datePrefix}-{$randomDigits}";
            }

            // 3. Server-Authoritative Price Calculation & Variant Verification (ADR-004)
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($data['items'] as $itemData) {
                $quantity = max(1, (int) $itemData['quantity']);

                /** @var ProductVariant $variant */
                $variant = ProductVariant::with(['product', 'inventoryItem'])->findOrFail($itemData['variant_id']);

                $unitPrice = (int) $variant->price;
                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                $itemsToCreate[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discountTotal = max(0, (int) ($data['discount_total'] ?? 0));
            $shippingTotal = max(0, (int) ($data['shipping_total'] ?? 0));
            $taxTotal = max(0, (int) ($data['tax_total'] ?? 0));
            $grandTotal = max(0, ($subtotal - $discountTotal) + $shippingTotal + $taxTotal);

            // 4. Create Order Record
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'source' => $data['source'] ?? 'backoffice',
                'order_status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
                'fulfillment_status' => FulfillmentStatus::Unfulfilled,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_total' => $shippingTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'notes' => $data['notes'] ?? null,
            ]);

            // 5. Create Snapshot Line Items (ADR-006) & Reserve Inventory Stock (ADR-001)
            foreach ($itemsToCreate as $item) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];

                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'product_name' => $variant->product->name, // Snapshot
                    'variant_title' => $variant->title,        // Snapshot
                    'sku' => $variant->sku,                    // Snapshot
                    'unit_price' => $item['unit_price'],       // Snapshot
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Atomically reserve inventory stock
                $inventoryItem = $variant->inventoryItem ?? InventoryItem::firstOrCreate(
                    ['variant_id' => $variant->id],
                    ['on_hand' => 0, 'reserved' => 0, 'low_stock_threshold' => 5]
                );

                $this->reserveStock->execute($inventoryItem, [
                    'quantity' => $item['quantity'],
                    'order_id' => $order->id,
                    'reference_note' => "Reservasi pesanan {$order->order_number}",
                    'user_id' => $data['user_id'] ?? auth()->id(),
                ]);
            }

            // 6. Create Shipping Address
            $order->address()->create([
                'recipient_name' => $data['address']['recipient_name'],
                'phone' => $data['address']['phone'],
                'address_line1' => $data['address']['address_line1'],
                'address_line2' => $data['address']['address_line2'] ?? null,
                'city' => $data['address']['city'],
                'province' => $data['address']['province'],
                'postal_code' => $data['address']['postal_code'],
                'courier_name' => $data['address']['courier_name'] ?? null,
                'tracking_number' => null,
            ]);

            // 7. Record Initial Status History
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'from_status' => 'none',
                'to_status' => OrderStatus::Pending->value,
                'notes' => 'Pesanan berhasil dibuat di sistem Backoffice',
            ]);

            // 8. Increment Customer order count
            $customer->increment('total_orders_count');

            return $order->fresh(['customer', 'items', 'address', 'statusHistories']);
        });
    }
}

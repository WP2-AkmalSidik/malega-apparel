<?php

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FulfillStockAction
{
    /**
     * Deduct physical stock on order fulfillment.
     *
     * @param  array{quantity: int, order_id?: int|null, reference_note?: string|null, user_id?: int|null}  $data
     *
     * @throws ValidationException
     */
    public function execute(InventoryItem $item, array $data): InventoryItem
    {
        $quantity = (int) $data['quantity'];

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah kuantitas fulfillment harus lebih dari 0.',
            ]);
        }

        return DB::transaction(function () use ($item, $quantity, $data) {
            /** @var InventoryItem $lockedItem */
            $lockedItem = InventoryItem::where('id', $item->id)->lockForUpdate()->firstOrFail();

            if ($lockedItem->on_hand < $quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Stok fisik ({$lockedItem->on_hand}) tidak mencukupi untuk diproses keluar {$quantity} unit.",
                ]);
            }

            $onHandBefore = $lockedItem->on_hand;
            $reservedBefore = $lockedItem->reserved;

            $lockedItem->on_hand -= $quantity;
            $lockedItem->reserved = max(0, $lockedItem->reserved - $quantity);
            $lockedItem->save();

            StockMovement::create([
                'inventory_item_id' => $lockedItem->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'order_id' => $data['order_id'] ?? null,
                'type' => StockMovementType::Fulfilled,
                'quantity_change' => -$quantity,
                'on_hand_before' => $onHandBefore,
                'on_hand_after' => $lockedItem->on_hand,
                'reserved_before' => $reservedBefore,
                'reserved_after' => $lockedItem->reserved,
                'reference_note' => $data['reference_note'] ?? 'Pemotongan stok pengiriman pesanan (Fulfillment)',
            ]);

            return $lockedItem->fresh(['variant', 'movements']);
        });
    }
}

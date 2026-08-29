<?php

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReleaseStockAction
{
    /**
     * Release previously reserved stock back to available pool.
     *
     * @param  array{quantity: int, order_id?: int|null, reference_note?: string|null, user_id?: int|null}  $data
     */
    public function execute(InventoryItem $item, array $data): InventoryItem
    {
        $quantity = max(1, (int) $data['quantity']);

        return DB::transaction(function () use ($item, $quantity, $data) {
            /** @var InventoryItem $lockedItem */
            $lockedItem = InventoryItem::where('id', $item->id)->lockForUpdate()->firstOrFail();

            $releasedQty = min($lockedItem->reserved, $quantity);
            $reservedBefore = $lockedItem->reserved;
            $lockedItem->reserved -= $releasedQty;
            $lockedItem->save();

            StockMovement::create([
                'inventory_item_id' => $lockedItem->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'order_id' => $data['order_id'] ?? null,
                'type' => StockMovementType::Released,
                'quantity_change' => $releasedQty, // Available stock restored
                'on_hand_before' => $lockedItem->on_hand,
                'on_hand_after' => $lockedItem->on_hand,
                'reserved_before' => $reservedBefore,
                'reserved_after' => $lockedItem->reserved,
                'reference_note' => $data['reference_note'] ?? 'Pelepasan reservasi stok (Pembatalan pesanan)',
            ]);

            return $lockedItem->fresh(['variant', 'movements']);
        });
    }
}

<?php

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddStockInboundAction
{
    /**
     * Add inbound stock units from production/vendor.
     *
     * @param  array{quantity: int, reference_note?: string|null, user_id?: int|null}  $data
     *
     * @throws ValidationException
     */
    public function execute(InventoryItem $item, array $data): InventoryItem
    {
        $quantity = (int) $data['quantity'];

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah stok masuk wajib lebih besar dari 0.',
            ]);
        }

        return DB::transaction(function () use ($item, $quantity, $data) {
            /** @var InventoryItem $lockedItem */
            $lockedItem = InventoryItem::where('id', $item->id)->lockForUpdate()->firstOrFail();

            $onHandBefore = $lockedItem->on_hand;
            $lockedItem->on_hand += $quantity;
            $lockedItem->save();

            StockMovement::create([
                'inventory_item_id' => $lockedItem->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'order_id' => null,
                'type' => StockMovementType::Inbound,
                'quantity_change' => $quantity,
                'on_hand_before' => $onHandBefore,
                'on_hand_after' => $lockedItem->on_hand,
                'reserved_before' => $lockedItem->reserved,
                'reserved_after' => $lockedItem->reserved,
                'reference_note' => $data['reference_note'] ?? 'Penerimaan stok masuk (Inbound)',
            ]);

            return $lockedItem->fresh(['variant', 'movements']);
        });
    }
}

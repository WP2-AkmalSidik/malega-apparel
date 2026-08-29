<?php

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustStockAction
{
    /**
     * Adjust physical on_hand stock (Stock Opname) with strict no-negative stock protection.
     *
     * @param  array{new_on_hand: int, low_stock_threshold?: int|null, reference_note?: string|null, user_id?: int|null}  $data
     *
     * @throws ValidationException
     */
    public function execute(InventoryItem $item, array $data): InventoryItem
    {
        $newOnHand = (int) $data['new_on_hand'];

        if ($newOnHand < 0) {
            throw ValidationException::withMessages([
                'new_on_hand' => 'Jumlah stok fisik tidak boleh bernilai negatif (ADR-001).',
            ]);
        }

        return DB::transaction(function () use ($item, $newOnHand, $data) {
            /** @var InventoryItem $lockedItem */
            $lockedItem = InventoryItem::where('id', $item->id)->lockForUpdate()->firstOrFail();

            if ($newOnHand < $lockedItem->reserved) {
                throw ValidationException::withMessages([
                    'new_on_hand' => "Stok fisik baru ({$newOnHand}) tidak boleh lebih kecil dari stok yang sedang direservasi pesanan ({$lockedItem->reserved}).",
                ]);
            }

            $onHandBefore = $lockedItem->on_hand;
            $quantityChange = $newOnHand - $onHandBefore;

            $lockedItem->on_hand = $newOnHand;

            if (isset($data['low_stock_threshold'])) {
                $lockedItem->low_stock_threshold = max(1, (int) $data['low_stock_threshold']);
            }

            $lockedItem->save();

            // Record immutable audit movement if stock count actually changed
            if ($quantityChange !== 0) {
                StockMovement::create([
                    'inventory_item_id' => $lockedItem->id,
                    'user_id' => $data['user_id'] ?? auth()->id(),
                    'order_id' => null,
                    'type' => StockMovementType::Adjustment,
                    'quantity_change' => $quantityChange,
                    'on_hand_before' => $onHandBefore,
                    'on_hand_after' => $newOnHand,
                    'reserved_before' => $lockedItem->reserved,
                    'reserved_after' => $lockedItem->reserved,
                    'reference_note' => $data['reference_note'] ?? 'Penyesuaian stok fisik (Stock Opname)',
                ]);
            }

            return $lockedItem->fresh(['variant', 'movements']);
        });
    }
}

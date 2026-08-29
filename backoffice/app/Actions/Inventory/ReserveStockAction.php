<?php

namespace App\Actions\Inventory;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReserveStockAction
{
    /**
     * Atomically reserve stock for an active customer order.
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
                'quantity' => 'Jumlah kuantitas reservasi harus lebih dari 0.',
            ]);
        }

        return DB::transaction(function () use ($item, $quantity, $data) {
            /** @var InventoryItem $lockedItem */
            $lockedItem = InventoryItem::where('id', $item->id)->lockForUpdate()->firstOrFail();

            $available = $lockedItem->on_hand - $lockedItem->reserved;

            if ($available < $quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Stok tersedia ({$available}) tidak mencukupi untuk memesan {$quantity} unit (SKU: {$lockedItem->variant->sku}).",
                ]);
            }

            $reservedBefore = $lockedItem->reserved;
            $lockedItem->reserved += $quantity;
            $lockedItem->save();

            StockMovement::create([
                'inventory_item_id' => $lockedItem->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'order_id' => $data['order_id'] ?? null,
                'type' => StockMovementType::Reserved,
                'quantity_change' => -$quantity, // Available stock decreases
                'on_hand_before' => $lockedItem->on_hand,
                'on_hand_after' => $lockedItem->on_hand,
                'reserved_before' => $reservedBefore,
                'reserved_after' => $lockedItem->reserved,
                'reference_note' => $data['reference_note'] ?? 'Reservasi stok untuk pesanan',
            ]);

            return $lockedItem->fresh(['variant', 'movements']);
        });
    }
}

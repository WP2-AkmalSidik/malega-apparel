<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * Disable updated_at column since stock movements are immutable append-only records.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'inventory_item_id',
        'user_id',
        'order_id',
        'type',
        'quantity_change',
        'on_hand_before',
        'on_hand_after',
        'reserved_before',
        'reserved_after',
        'reference_note',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity_change' => 'integer',
            'on_hand_before' => 'integer',
            'on_hand_after' => 'integer',
            'reserved_before' => 'integer',
            'reserved_after' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Parent inventory item.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Staff user who authorized or recorded this movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

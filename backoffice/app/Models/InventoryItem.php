<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'variant_id',
        'on_hand',
        'reserved',
        'low_stock_threshold',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'on_hand' => 'integer',
            'reserved' => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    /**
     * Product variant linked to this stock record.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Immutable audit ledger movements.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest('created_at');
    }

    /**
     * Available stock accessor (on_hand - reserved).
     */
    protected function available(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->on_hand - $this->reserved)
        );
    }

    /**
     * Low stock flag accessor.
     */
    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->available > 0 && $this->available <= $this->low_stock_threshold
        );
    }

    /**
     * Out of stock flag accessor.
     */
    protected function isOutOfStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->available <= 0
        );
    }

    /**
     * Scope for items with low stock.
     */
    public function scopeLowStock(Builder $query): void
    {
        $query->whereRaw('(on_hand - reserved) > 0')
            ->whereRaw('(on_hand - reserved) <= low_stock_threshold');
    }

    /**
     * Scope for items with zero or negative available stock.
     */
    public function scopeOutOfStock(Builder $query): void
    {
        $query->whereRaw('(on_hand - reserved) <= 0');
    }
}

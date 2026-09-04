<?php

namespace App\Models;

use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'amount',
        'max_discount_amount',
        'min_order_amount',
        'usage_limit_total',
        'used_count',
        'usage_limit_per_user',
        'valid_from',
        'valid_until',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'type' => VoucherType::class,
        'amount' => 'integer',
        'max_discount_amount' => 'integer',
        'min_order_amount' => 'integer',
        'usage_limit_total' => 'integer',
        'used_count' => 'integer',
        'usage_limit_per_user' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = now();

        return $query->active()
            ->where('valid_from', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit_total')
                    ->orWhereColumn('used_count', '<', 'usage_limit_total');
            });
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();
        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit_total !== null && $this->used_count >= $this->usage_limit_total) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(int $subtotal, int $shippingCost = 0): int
    {
        if ($subtotal < $this->min_order_amount) {
            return 0;
        }

        switch ($this->type) {
            case VoucherType::Percentage:
                $discount = (int) round(($subtotal * $this->amount) / 100);
                if ($this->max_discount_amount && $this->max_discount_amount > 0) {
                    $discount = min($discount, $this->max_discount_amount);
                }

                return max(0, $discount);

            case VoucherType::FixedAmount:
                return min($subtotal, $this->amount);

            case VoucherType::FreeShipping:
                return min($shippingCost > 0 ? $shippingCost : 15000, $this->amount > 0 ? $this->amount : 15000);

            default:
                return 0;
        }
    }

    public function formattedDiscount(): string
    {
        return match ($this->type) {
            VoucherType::Percentage => "{$this->amount}% OFF" . ($this->max_discount_amount ? " (Maks. Rp " . number_format($this->max_discount_amount, 0, ',', '.') . ")" : ''),
            VoucherType::FixedAmount => 'Potongan Rp ' . number_format($this->amount, 0, ',', '.'),
            VoucherType::FreeShipping => 'Gratis Ongkir s.d. Rp ' . number_format($this->amount > 0 ? $this->amount : 15000, 0, ',', '.'),
        };
    }
}

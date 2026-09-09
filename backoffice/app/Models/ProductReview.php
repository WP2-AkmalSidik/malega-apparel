<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'order_item_id',
        'rating',
        'headline',
        'review',
        'fit_rating',
        'is_verified_purchase',
        'status',
        'admin_reply',
        'admin_replied_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'admin_replied_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function (ProductReview $review) {
            $review->product?->recalculateRating();
        });

        static::deleted(function (ProductReview $review) {
            $review->product?->recalculateRating();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->latest();
    }

    public function scopeWithRating(Builder $query, int $stars): Builder
    {
        return $query->where('rating', $stars);
    }

    public function fitLabel(): ?string
    {
        return match ($this->fit_rating) {
            'true_to_size' => 'Pas di Badan',
            'runs_small' => 'Agak Kecil',
            'runs_large' => 'Agak Besar',
            default => null,
        };
    }
}

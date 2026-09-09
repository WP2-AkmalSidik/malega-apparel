<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'min_spend',
        'voucher_id',
        'badge_text',
        'badge_color',
        'discount_label',
        'description',
        'perks',
        'order',
        'is_active',
    ];

    protected $casts = [
        'min_spend' => 'integer',
        'perks' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tier) {
            if (empty($tier->slug)) {
                $tier->slug = Str::slug($tier->name);
            }
        });
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')->orderBy('min_spend', 'asc');
    }

    public function getFormattedMinSpendAttribute(): string
    {
        return 'Rp ' . number_format($this->min_spend, 0, ',', '.');
    }

    public function badgeClasses(): string
    {
        return match ($this->badge_color) {
            'gold' => 'bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/50',
            'amber' => 'bg-amber-500/20 text-amber-300 border border-amber-500/40',
            'rose' => 'bg-rose-500/20 text-rose-300 border border-rose-500/40',
            'emerald' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
            default => 'bg-slate-800 text-slate-300 border border-slate-700',
        };
    }
}

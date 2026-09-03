<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'fabric_spec_id',
        'name',
        'subtitle',
        'badge',
        'rating',
        'review_count',
        'sold_count',
        'slug',
        'description',
        'material',
        'gsm',
        'fit',
        'origin',
        'features',
        'specifications',
        'status',
        'featured_image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'rating' => 'float',
            'review_count' => 'integer',
            'sold_count' => 'integer',
            'gsm' => 'integer',
            'features' => 'array',
            'specifications' => 'array',
        ];
    }

    public function fabricSpecification(): BelongsTo
    {
        return $this->belongsTo(FabricSpecification::class, 'fabric_spec_id');
    }

    /**
     * Category that this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Collections containing this product.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    /**
     * Variants (SKUs) of this product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Media / Gallery images of this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Formatted price range accessor (e.g. "Rp 299.000" or "Rp 299.000 - Rp 349.000").
     */
    protected function formattedPriceRange(): Attribute
    {
        return Attribute::make(
            get: function () {
                $min = $this->variants->min('price');
                $max = $this->variants->max('price');

                if ($min === null) {
                    return 'Rp 0';
                }

                if ($min === $max) {
                    return 'Rp '.number_format($min, 0, ',', '.');
                }

                return 'Rp '.number_format($min, 0, ',', '.').' - Rp '.number_format($max, 0, ',', '.');
            }
        );
    }

    /**
     * Get distinct colors available across variants.
     */
    protected function colors(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->variants
                    ->filter(fn ($v) => ! empty($v->color_name))
                    ->unique('color_name')
                    ->map(fn ($v) => [
                        'name' => $v->color_name,
                        'hex' => $v->color_hex ?? '#0B132B',
                        'image' => $v->image_url ?? $this->featured_image_url,
                    ])
                    ->values()
                    ->all();
            }
        );
    }

    /**
     * Get distinct sizes available across variants.
     */
    protected function sizes(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->variants
                    ->filter(fn ($v) => ! empty($v->size))
                    ->pluck('size')
                    ->unique()
                    ->values()
                    ->all();
            }
        );
    }

    /**
     * Featured Image full URL accessor.
     */
    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->featured_image) {
                    return null;
                }
                if (str_starts_with($this->featured_image, 'http://') || str_starts_with($this->featured_image, 'https://')) {
                    return $this->featured_image;
                }

                return asset('storage/'.$this->featured_image);
            }
        );
    }

    /**
     * Total stock units across all variants.
     */
    protected function totalStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (int) $this->variants->sum(fn ($v) => $v->inventoryItem?->on_hand ?? 0);
            }
        );
    }

    /**
     * Total available stock units across all variants.
     */
    protected function availableStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (int) $this->variants->sum(fn ($v) => $v->inventoryItem?->available ?? 0);
            }
        );
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', ProductStatus::Active);
    }
}

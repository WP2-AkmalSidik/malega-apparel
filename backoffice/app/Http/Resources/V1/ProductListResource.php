<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $minPrice = $this->variants->min('price') ?? 0;
        $maxPrice = $this->variants->max('price') ?? 0;
        $compareAt = $this->variants->first()?->compare_at_price;
        $discountPct = ($compareAt && $compareAt > $minPrice) ? (int) round((($compareAt - $minPrice) / $compareAt) * 100) : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'badge' => $this->badge,
            'rating' => (float) ($this->rating ?: 4.9),
            'review_count' => (int) ($this->review_count ?: 0),
            'sold_count' => (int) ($this->sold_count ?: 0),
            'material' => $this->material,
            'gsm' => $this->gsm,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'featured_image_url' => $this->featured_image_url,
            'price' => [
                'min' => (int) $minPrice,
                'max' => (int) $maxPrice,
                'compare_at' => $compareAt ? (int) $compareAt : null,
                'discount_percentage' => $discountPct,
                'formatted' => $this->formatted_price_range,
            ],
            'colors' => $this->colors,
            'variants_count' => $this->variants->count(),
            'is_in_stock' => $this->variants->some(fn ($v) => ($v->inventoryItem?->available ?? 0) > 0),
        ];
    }
}

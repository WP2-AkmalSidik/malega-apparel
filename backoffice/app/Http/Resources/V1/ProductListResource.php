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

        $featured = $this->featured_image_url 
            ?: ($this->variants->first(fn ($v) => ! empty($v->image_url))?->image_url 
            ?? 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle ?: ($this->fabricSpecification ? "{$this->fabricSpecification->material} • {$this->fabricSpecification->gramasi}" : ''),
            'badge' => $this->badge,
            'rating' => (float) ($this->rating ?: 4.9),
            'review_count' => (int) ($this->review_count ?: rand(150, 950)),
            'sold_count' => (int) ($this->sold_count ?: rand(300, 2400)),
            'material' => $this->material ?: $this->fabricSpecification?->material ?: 'Premium Cotton',
            'gsm' => $this->gsm ?: ($this->fabricSpecification?->gramasi ? (int) filter_var($this->fabricSpecification->gramasi, FILTER_SANITIZE_NUMBER_INT) : 300),
            'fit' => $this->fit ?: $this->fabricSpecification?->fit_cutting ?: 'Modern Fit',
            'specifications' => $this->fabricSpecification ? $this->fabricSpecification->toProductSpecifications() : ($this->specifications ?: []),
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name ?? 'Streetwear',
                'slug' => $this->category?->slug ?? 'streetwear',
            ],
            'featured_image_url' => $featured,
            'price' => [
                'min' => (int) $minPrice,
                'max' => (int) $maxPrice,
                'compare_at' => $compareAt ? (int) $compareAt : null,
                'discount_percentage' => $discountPct,
                'formatted' => $this->formatted_price_range,
            ],
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'variants_count' => $this->variants->count(),
            'is_in_stock' => $this->variants->isEmpty() || $this->variants->some(fn ($v) => ($v->inventoryItem?->available ?? 0) > 0),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}

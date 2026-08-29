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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'featured_image_url' => $this->featured_image ? asset('storage/'.$this->featured_image) : null,
            'price' => [
                'min' => (int) $minPrice,
                'max' => (int) $maxPrice,
                'formatted' => $this->formatted_price_range,
            ],
            'variants_count' => $this->variants->count(),
            'is_in_stock' => $this->variants->some(fn ($v) => ($v->inventoryItem?->available ?? 0) > 0),
        ];
    }
}

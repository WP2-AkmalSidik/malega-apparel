<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductDetailResource extends JsonResource
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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'badge' => $this->badge,
            'rating' => (float) ($this->rating ?: 4.9),
            'review_count' => (int) ($this->review_count ?: 0),
            'sold_count' => (int) ($this->sold_count ?: 0),
            'description' => $this->description,
            'material' => $this->material,
            'gsm' => $this->gsm,
            'fit' => $this->fit,
            'features' => $this->features ?: [],
            'specifications' => $this->fabricSpecification ? $this->fabricSpecification->toProductSpecifications() : ($this->specifications ?: []),
            'fabric_specification' => $this->fabricSpecification ? [
                'id' => $this->fabricSpecification->id,
                'name' => $this->fabricSpecification->name,
                'brand' => $this->fabricSpecification->brand,
                'gramasi' => $this->fabricSpecification->gramasi,
                'material' => $this->fabricSpecification->material,
                'fit_cutting' => $this->fabricSpecification->fit_cutting,
                'collar_hood' => $this->fabricSpecification->collar_hood,
                'care_instructions' => $this->fabricSpecification->care_instructions,
            ] : null,
            'status' => $this->status->value,
            'featured_image_url' => $this->featured_image_url,
            'price' => [
                'min' => (int) $minPrice,
                'max' => (int) $maxPrice,
                'compare_at' => $compareAt ? (int) $compareAt : null,
                'formatted' => $this->formatted_price_range,
            ],
            'colors' => $this->colors,
            'sizes' => $this->sizes,
            'total_stock' => $this->total_stock,
            'available_stock' => $this->available_stock,
            'is_in_stock' => $this->available_stock > 0,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'collections' => CollectionResource::collection($this->whenLoaded('collections')),
            'gallery_images' => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'image_url' => str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/'.$img->image_path),
                'is_primary' => (bool) $img->is_primary,
            ]),
            'variants' => ProductVariantResource::collection($this->variants),
        ];
    }
}

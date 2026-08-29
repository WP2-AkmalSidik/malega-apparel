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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status->value,
            'featured_image_url' => $this->featured_image ? asset('storage/'.$this->featured_image) : null,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'collections' => CollectionResource::collection($this->whenLoaded('collections')),
            'gallery_images' => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'image_url' => asset('storage/'.$img->image_path),
                'is_primary' => $img->is_primary,
            ]),
            'variants' => ProductVariantResource::collection($this->variants),
        ];
    }
}

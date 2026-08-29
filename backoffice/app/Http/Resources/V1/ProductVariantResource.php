<?php

namespace App\Http\Resources\V1;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProductVariant
 */
class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $availableStock = $this->inventoryItem ? $this->inventoryItem->available : 0;

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'title' => $this->title,
            'price' => (int) $this->price,
            'formatted_price' => $this->formatted_price,
            'compare_at_price' => $this->compare_at_price ? (int) $this->compare_at_price : null,
            'weight_grams' => (int) $this->weight_grams,
            'is_in_stock' => $availableStock > 0,
            'available_stock' => $availableStock,
        ];
    }
}

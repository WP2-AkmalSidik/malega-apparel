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
        $onHand = $this->inventoryItem ? $this->inventoryItem->on_hand : 0;

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'title' => $this->title,
            'color' => [
                'name' => $this->color_name,
                'hex' => $this->color_hex ?: '#0B132B',
                'image' => $this->image_url,
            ],
            'size' => $this->size,
            'price' => (int) $this->price,
            'formatted_price' => $this->formatted_price,
            'compare_at_price' => $this->compare_at_price ? (int) $this->compare_at_price : null,
            'weight_grams' => (int) $this->weight_grams,
            'on_hand_stock' => (int) $onHand,
            'available_stock' => (int) $availableStock,
            'is_in_stock' => $availableStock > 0,
        ];
    }
}

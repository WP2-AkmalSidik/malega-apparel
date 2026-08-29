<?php

namespace App\Http\Resources\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'created_at' => $this->created_at->toIso8601String(),
            'order_status' => [
                'code' => $this->order_status->value,
                'label' => $this->order_status->label(),
            ],
            'payment_status' => [
                'code' => $this->payment_status->value,
                'label' => $this->payment_status->label(),
            ],
            'fulfillment_status' => [
                'code' => $this->fulfillment_status->value,
                'label' => $this->fulfillment_status->label(),
            ],
            'pricing' => [
                'subtotal' => (int) $this->subtotal,
                'discount_total' => (int) $this->discount_total,
                'shipping_total' => (int) $this->shipping_total,
                'tax_total' => (int) $this->tax_total,
                'grand_total' => (int) $this->grand_total,
                'formatted_grand_total' => $this->formatted_grand_total,
            ],
            'customer' => [
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
                'phone' => $this->customer?->phone,
            ],
            'shipping_address' => [
                'recipient_name' => $this->address?->recipient_name,
                'phone' => $this->address?->phone,
                'address_line1' => $this->address?->address_line1,
                'address_line2' => $this->address?->address_line2,
                'city' => $this->address?->city,
                'province' => $this->address?->province,
                'postal_code' => $this->address?->postal_code,
                'courier_name' => $this->address?->courier_name,
                'tracking_number' => $this->address?->tracking_number,
            ],
            'items' => $this->items->map(fn ($item) => [
                'sku' => $item->sku,
                'product_name' => $item->product_name,
                'variant_title' => $item->variant_title,
                'unit_price' => (int) $item->unit_price,
                'formatted_unit_price' => $item->formatted_unit_price,
                'quantity' => (int) $item->quantity,
                'subtotal' => (int) $item->subtotal,
                'formatted_subtotal' => $item->formatted_subtotal,
            ]),
        ];
    }
}

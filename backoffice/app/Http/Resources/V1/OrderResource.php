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
        $isPublicTrack = $request->routeIs('api.v1.orders.track');
        $authHeader = $request->header('Authorization');
        $bearerToken = ($authHeader && str_starts_with($authHeader, 'Bearer ')) ? trim(substr($authHeader, 7)) : null;
        $isCustomerOwner = false;

        if ($this->customer_id) {
            if ($bearerToken && $this->customer && $this->customer->remember_token === $bearerToken) {
                $isCustomerOwner = true;
            } elseif (auth('customer')->check() && auth('customer')->id() === $this->customer_id) {
                $isCustomerOwner = true;
            }
        }

        $isOwnerOrAdmin = auth('sanctum')->check() || auth('web')->check() || $isCustomerOwner;
        $shouldMask = $isPublicTrack && ! $isOwnerOrAdmin;

        $maskPhone = function (?string $phone) use ($shouldMask) {
            if (! $phone || ! $shouldMask) {
                return $phone;
            }
            $len = strlen($phone);
            if ($len <= 6) {
                return '***'.substr($phone, -2);
            }
            return substr($phone, 0, 4).'****'.substr($phone, -3);
        };

        $maskEmail = function (?string $email) use ($shouldMask) {
            if (! $email || ! $shouldMask) {
                return $email;
            }
            $parts = explode('@', $email, 2);
            if (count($parts) < 2) {
                return '***@malega.id';
            }
            $name = $parts[0];
            $domain = $parts[1];
            $maskedName = strlen($name) > 2 ? substr($name, 0, 2).'***' : substr($name, 0, 1).'***';
            return $maskedName.'@'.$domain;
        };

        $maskAddress = function (?string $addr) use ($shouldMask) {
            if (! $addr || ! $shouldMask) {
                return $addr;
            }
            return strlen($addr) > 12 ? substr($addr, 0, 10).' **** (Disamarkan demi privasi)' : '****';
        };

        $maskName = function (?string $name) use ($shouldMask) {
            if (! $name || ! $shouldMask) {
                return $name;
            }
            $parts = explode(' ', trim($name));
            if (count($parts) > 1) {
                return substr($parts[0], 0, 2).'*** '.substr(end($parts), 0, 1).'***';
            }
            return strlen($name) > 2 ? substr($name, 0, 2).'***' : substr($name, 0, 1).'***';
        };

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
                'service_fee' => (int) ($this->service_fee ?? 0),
                'tax_total' => (int) $this->tax_total,
                'grand_total' => (int) $this->grand_total,
                'formatted_grand_total' => $this->formatted_grand_total,
            ],
            'customer' => [
                'name' => $maskName($this->customer?->name),
                'email' => $maskEmail($this->customer?->email),
                'phone' => $maskPhone($this->customer?->phone),
            ],
            'shipping_address' => [
                'recipient_name' => $maskName($this->address?->recipient_name),
                'phone' => $maskPhone($this->address?->phone),
                'address_line1' => $maskAddress($this->address?->address_line1),
                'address_line2' => $shouldMask ? null : $this->address?->address_line2,
                'city' => $this->address?->city,
                'province' => $this->address?->province,
                'postal_code' => $shouldMask ? '*****' : $this->address?->postal_code,
                'courier_name' => $this->address?->courier_name,
                'tracking_number' => $this->address?->tracking_number,
            ],
            'shipment' => $this->shipment ? [
                'courier' => $this->shipment->courier_company,
                'service' => $this->shipment->courier_service_name,
                'waybill_id' => $this->shipment->waybill_id,
                'status' => $this->shipment->status,
                'status_label' => $this->shipment->status_label,
                'tracking_url' => $this->shipment->tracking_url,
                'tracking_history' => $this->shipment->tracking_history,
                'shipped_at' => $this->shipment->shipped_at?->toIso8601String(),
                'delivered_at' => $this->shipment->delivered_at?->toIso8601String(),
            ] : null,
            'payment' => $this->payment ? [
                'reference' => $this->payment->reference,
                'payment_method' => $this->payment->payment_method,
                'payment_method_name' => $this->payment->payment_method_name,
                'payment_url' => $this->payment->payment_url,
                'va_number' => $this->payment->va_number,
                'qr_string' => $this->payment->qr_string,
                'status' => $this->payment->status,
                'paid_at' => $this->payment->paid_at?->toIso8601String(),
                'expires_at' => $this->payment->expires_at?->toIso8601String(),
            ] : null,
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

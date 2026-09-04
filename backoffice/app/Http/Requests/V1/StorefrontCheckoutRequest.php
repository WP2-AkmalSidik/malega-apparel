<?php

namespace App\Http\Requests\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorefrontCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.email' => ['required', 'email', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:30'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['nullable'],
            'items.*.sku' => ['nullable', 'string', 'max:100'],
            'items.*.product_name' => ['nullable', 'string', 'max:255'],
            'items.*.variant_title' => ['nullable', 'string', 'max:255'],
            'items.*.unit_price' => ['nullable', 'integer', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],

            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_method_name' => ['nullable', 'string', 'max:100'],

            'shipping_address.recipient_name' => ['required', 'string', 'max:255'],
            'shipping_address.phone' => ['required', 'string', 'max:30'],
            'shipping_address.address_line1' => ['required', 'string', 'max:255'],
            'shipping_address.address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.province' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.courier_name' => ['nullable', 'string', 'max:100'],

            'shipping_total' => ['nullable', 'integer', 'min:0'],
            'discount_total' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}

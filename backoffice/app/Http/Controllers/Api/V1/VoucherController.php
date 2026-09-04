<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Marketing\ValidateVoucherAction;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Validate a promo / voucher code for checkout.
     */
    public function validateCode(Request $request, ValidateVoucherAction $validateVoucher): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'integer', 'min:0'],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $subtotal = (int) $validated['subtotal'];
        $shippingCost = (int) ($validated['shipping_cost'] ?? 0);
        $email = $validated['email'] ?? null;

        $result = $validateVoucher->execute($validated['code'], $subtotal, $shippingCost, $email);

        return response()->json([
            'success' => $result['valid'],
            'message' => $result['message'],
            'data' => $result['valid'] ? [
                'discount_amount' => $result['discount_amount'],
                'voucher' => $result['voucher'],
            ] : null,
        ], $result['valid'] ? 200 : 422);
    }

    /**
     * Get list of active and public vouchers for customer discovery.
     */
    public function publicList(): JsonResponse
    {
        $vouchers = Voucher::active()
            ->public()
            ->valid()
            ->orderBy('min_order_amount', 'asc')
            ->get();

        $data = $vouchers->map(fn (Voucher $v) => [
            'id' => $v->id,
            'code' => $v->code,
            'title' => $v->name,
            'name' => $v->name,
            'description' => $v->description,
            'type' => $v->type->value,
            'amount' => $v->amount,
            'discount' => $v->amount,
            'formatted_discount' => $v->formattedDiscount(),
            'min_spend' => $v->min_order_amount,
            'minSpend' => $v->min_order_amount,
            'max_discount' => $v->max_discount_amount,
            'valid_until' => $v->valid_until?->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\JsonResponse;

class MembershipTierController extends Controller
{
    /**
     * Get active membership tiers with linked vouchers and perks.
     */
    public function index(): JsonResponse
    {
        $tiers = MembershipTier::with(['voucher' => function ($q) {
            $q->where('is_active', true);
        }])
            ->active()
            ->ordered()
            ->get();

        $data = $tiers->map(function (MembershipTier $tier) {
            return [
                'id' => $tier->id,
                'name' => $tier->name,
                'slug' => $tier->slug,
                'min_spend' => $tier->min_spend,
                'formatted_min_spend' => $tier->formatted_min_spend,
                'badge_text' => $tier->badge_text ?? "★ {$tier->name} Member",
                'badge_color' => $tier->badge_color,
                'discount_label' => $tier->discount_label,
                'description' => $tier->description,
                'perks' => $tier->perks ?? [],
                'order' => $tier->order,
                'voucher' => $tier->voucher ? [
                    'id' => $tier->voucher->id,
                    'code' => $tier->voucher->code,
                    'name' => $tier->voucher->name,
                    'type' => $tier->voucher->type->value,
                    'amount' => $tier->voucher->amount,
                    'formatted_discount' => $tier->voucher->type->value === 'percentage'
                        ? "Diskon {$tier->voucher->amount}%"
                        : "Potongan Rp " . number_format($tier->voucher->amount, 0, ',', '.'),
                    'min_order_amount' => $tier->voucher->min_order_amount,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

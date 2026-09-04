<?php

namespace App\Actions\Marketing;

use App\Models\Voucher;
use App\Models\VoucherUsage;

class ValidateVoucherAction
{
    /**
     * Validate a voucher code against business rules, minimum spend, customer limits, and dates.
     *
     * @return array{
     *     valid: bool,
     *     message: string,
     *     discount_amount: int,
     *     voucher: ?array
     * }
     */
    public function execute(string $code, int $subtotal, int $shippingCost = 0, ?string $customerEmail = null): array
    {
        $codeClean = strtoupper(trim($code));

        if (empty($codeClean)) {
            return [
                'valid' => false,
                'message' => 'Kode voucher tidak boleh kosong.',
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        $voucher = Voucher::where('code', $codeClean)->first();

        if (! $voucher) {
            return [
                'valid' => false,
                'message' => "Kode voucher \"{$codeClean}\" tidak ditemukan.",
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        if (! $voucher->is_active) {
            return [
                'valid' => false,
                'message' => "Voucher \"{$voucher->name}\" saat ini sedang dinonaktifkan.",
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        $now = now();
        if ($voucher->valid_from && $voucher->valid_from->isFuture()) {
            return [
                'valid' => false,
                'message' => "Voucher ini baru dapat digunakan mulai tanggal {$voucher->valid_from->format('d M Y H:i')}.",
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        if ($voucher->valid_until && $voucher->valid_until->isPast()) {
            return [
                'valid' => false,
                'message' => "Voucher telah kedaluwarsa pada tanggal {$voucher->valid_until->format('d M Y H:i')}.",
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        if ($voucher->usage_limit_total !== null && $voucher->used_count >= $voucher->usage_limit_total) {
            return [
                'valid' => false,
                'message' => 'Kuota penggunaan voucher ini telah habis.',
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        if ($subtotal < $voucher->min_order_amount) {
            $minFormatted = 'Rp ' . number_format($voucher->min_order_amount, 0, ',', '.');

            return [
                'valid' => false,
                'message' => "Minimal pembelanjaan untuk voucher ini adalah {$minFormatted}.",
                'discount_amount' => 0,
                'voucher' => null,
            ];
        }

        if (! empty($customerEmail) && $voucher->usage_limit_per_user > 0) {
            $emailClean = strtolower(trim($customerEmail));
            $userUsageCount = VoucherUsage::where('voucher_id', $voucher->id)
                ->where('customer_email', $emailClean)
                ->count();

            if ($userUsageCount >= $voucher->usage_limit_per_user) {
                return [
                    'valid' => false,
                    'message' => "Anda telah mencapai batas maksimal pemakaian ({$voucher->usage_limit_per_user}x) untuk voucher ini.",
                    'discount_amount' => 0,
                    'voucher' => null,
                ];
            }
        }

        $discount = $voucher->calculateDiscount($subtotal, $shippingCost);

        return [
            'valid' => true,
            'message' => "Voucher {$voucher->code} berhasil diterapkan ({$voucher->formattedDiscount()}).",
            'discount_amount' => $discount,
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'title' => $voucher->name,
                'description' => $voucher->description,
                'type' => $voucher->type->value,
                'amount' => $voucher->amount,
                'formatted_discount' => $voucher->formattedDiscount(),
                'min_spend' => $voucher->min_order_amount,
                'discount' => $discount,
            ],
        ];
    }
}

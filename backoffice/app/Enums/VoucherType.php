<?php

namespace App\Enums;

enum VoucherType: string
{
    case Percentage = 'percentage';
    case FixedAmount = 'fixed_amount';
    case FreeShipping = 'free_shipping';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Diskon Persentase (%)',
            self::FixedAmount => 'Potongan Nominal (Rp)',
            self::FreeShipping => 'Gratis / Subsidi Ongkir',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Percentage => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
            self::FixedAmount => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
            self::FreeShipping => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
        };
    }
}

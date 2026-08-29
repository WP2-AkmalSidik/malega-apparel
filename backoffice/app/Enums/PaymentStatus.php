<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case Failed = 'failed';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::Paid => 'Lunas',
            self::Refunded => 'Dikembalikan (Refund)',
            self::Failed => 'Gagal',
        };
    }

    /**
     * Tailwind CSS classes for status badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unpaid => 'text-amber-400 bg-amber-500/10 border border-amber-500/30',
            self::Paid => 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/30',
            self::Refunded => 'text-purple-400 bg-purple-500/10 border border-purple-500/30',
            self::Failed => 'text-rose-400 bg-rose-500/10 border border-rose-500/30',
        };
    }
}

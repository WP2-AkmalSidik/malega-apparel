<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Processing => 'Sedang Diproses',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Tailwind CSS classes for status badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'text-amber-400 bg-amber-500/10 border border-amber-500/30',
            self::Processing => 'text-sky-400 bg-sky-500/10 border border-sky-500/30',
            self::Completed => 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/30',
            self::Cancelled => 'text-rose-400 bg-rose-500/10 border border-rose-500/30',
        };
    }
}

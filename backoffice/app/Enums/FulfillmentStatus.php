<?php

namespace App\Enums;

enum FulfillmentStatus: string
{
    case Unfulfilled = 'unfulfilled';
    case Partial = 'partial';
    case Fulfilled = 'fulfilled';
    case Delivered = 'delivered';
    case Returned = 'returned';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unfulfilled => 'Belum Dikirim',
            self::Partial => 'Dikirim Sebagian',
            self::Fulfilled => 'Sedang Dikirim',
            self::Delivered => 'Terkirim',
            self::Returned => 'Retur',
        };
    }

    /**
     * Tailwind CSS classes for status badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Unfulfilled => 'text-slate-400 bg-slate-500/10 border border-slate-500/30',
            self::Partial => 'text-amber-400 bg-amber-500/10 border border-amber-500/30',
            self::Fulfilled => 'text-sky-400 bg-sky-500/10 border border-sky-500/30',
            self::Delivered => 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/30',
            self::Returned => 'text-rose-400 bg-rose-500/10 border border-rose-500/30',
        };
    }
}

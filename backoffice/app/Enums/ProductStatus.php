<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';

    /**
     * Human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Inactive => 'Nonaktif',
            self::Archived => 'Diarsipkan',
        };
    }

    /**
     * Tailwind CSS classes for badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Active => 'text-emerald-400 border border-emerald-400/40 bg-emerald-400/10',
            self::Draft => 'text-amber-300 border border-amber-300/40 bg-amber-400/10',
            self::Inactive => 'text-slate-400 border border-slate-400/40 bg-slate-400/10',
            self::Archived => 'text-rose-400 border border-rose-400/40 bg-rose-400/10',
        };
    }
}

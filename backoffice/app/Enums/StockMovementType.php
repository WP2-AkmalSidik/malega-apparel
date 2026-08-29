<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Inbound = 'inbound';
    case Adjustment = 'adjustment';
    case Reserved = 'reserved';
    case Released = 'released';
    case Fulfilled = 'fulfilled';
    case Returned = 'returned';

    /**
     * Human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Inbound => 'Stok Masuk (Inbound)',
            self::Adjustment => 'Penyesuaian (Stock Opname)',
            self::Reserved => 'Reservasi Pesanan',
            self::Released => 'Pelepasan Reservasi',
            self::Fulfilled => 'Pengiriman (Fulfillment)',
            self::Returned => 'Retur Pelanggan',
        };
    }

    /**
     * Tailwind CSS classes for ledger badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Inbound => 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/30',
            self::Adjustment => 'text-[#CBAC70] bg-[#CBAC70]/10 border border-[#CBAC70]/30',
            self::Reserved => 'text-amber-400 bg-amber-500/10 border border-amber-500/30',
            self::Released => 'text-sky-400 bg-sky-500/10 border border-sky-500/30',
            self::Fulfilled => 'text-indigo-400 bg-indigo-500/10 border border-indigo-500/30',
            self::Returned => 'text-purple-400 bg-purple-500/10 border border-purple-500/30',
        };
    }
}

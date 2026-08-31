<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'biteship_order_id',
        'biteship_tracking_id',
        'courier_company',
        'courier_service_name',
        'waybill_id',
        'tracking_url',
        'shipment_fee',
        'status',
        'shipper_snapshot',
        'destination_snapshot',
        'tracking_history',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'shipment_fee' => 'integer',
        'shipper_snapshot' => 'array',
        'destination_snapshot' => 'array',
        'tracking_history' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Relationship: Shipment belongs to an Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Helper: Format shipment fee in Indonesian Rupiah.
     */
    public function getFormattedFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->shipment_fee, 0, ',', '.');
    }

    /**
     * Helper: Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'confirmed' => 'Menunggu Pickup',
            'allocated' => 'Kurir Ditugaskan',
            'picking_up' => 'Kurir Menjemput',
            'picked' => 'Paket Diambil',
            'dropping_off', 'in_transit' => 'Sedang Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            'rejected' => 'Ditolak',
            'returned' => 'Dikembalikan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Helper: Status badge color classes for Tailwind UI.
     */
    public function getStatusBadgeClassesAttribute(): string
    {
        return match ($this->status) {
            'confirmed', 'allocated' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            'picking_up', 'picked', 'dropping_off', 'in_transit' => 'bg-sky-500/10 text-sky-400 border border-sky-500/30',
            'delivered' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            'cancelled', 'rejected', 'returned' => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
            default => 'bg-slate-800 text-slate-400 border border-slate-700',
        };
    }
}

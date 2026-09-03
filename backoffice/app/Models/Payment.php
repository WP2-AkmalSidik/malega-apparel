<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'payment_gateway',
        'merchant_order_id',
        'reference',
        'payment_method',
        'payment_method_name',
        'amount',
        'admin_fee',
        'net_amount',
        'status',
        'payment_url',
        'va_number',
        'qr_string',
        'payload',
        'callback_payload',
        'paid_at',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'admin_fee' => 'integer',
            'net_amount' => 'integer',
            'payload' => 'array',
            'callback_payload' => 'array',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Order associated with this payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Formatted Gross Amount Accessor.
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->amount, 0, ',', '.')
        );
    }

    /**
     * Formatted Admin Fee Accessor.
     */
    protected function formattedAdminFee(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->admin_fee, 0, ',', '.')
        );
    }

    /**
     * Formatted Real Net Amount Accessor.
     */
    protected function formattedNetAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format($this->net_amount, 0, ',', '.')
        );
    }

    /**
     * Calculate standard Duitku gateway admin fee based on payment method code.
     */
    public static function estimateGatewayFee(string $methodCode, int $grossAmount): int
    {
        $code = strtoupper(trim($methodCode));

        return match ($code) {
            // Virtual Accounts standard fixed fee
            'BC', 'M2', 'BT', 'B1', 'A1' => 4000,
            'BR', 'I1', 'NC', 'BV', 'AG', 'S1' => 3000,

            // QRIS (0.7% MDR standard Bank Indonesia)
            'QR', 'SP', 'LQ', 'NQ', 'GQ' => max(1000, (int) round($grossAmount * 0.007)),

            // Credit Cards (2.0% + Rp 2.000)
            'VC' => (int) round($grossAmount * 0.02) + 2000,

            // E-Wallets (1.5%)
            'OV', 'DA', 'SA', 'LA', 'JP' => (int) round($grossAmount * 0.015),

            // Retail / Paylater
            'FT', 'IR' => 5000,
            'DN' => (int) round($grossAmount * 0.025),

            // COD / Manual transfer
            'COD', 'MANUAL' => 0,

            default => 4000
        };
    }
}

<?php

namespace App\Models;

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
}

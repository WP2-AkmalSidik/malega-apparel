<?php

namespace App\Models;

use App\Enums\FulfillmentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'source',
        'order_status',
        'payment_status',
        'fulfillment_status',
        'subtotal',
        'discount_total',
        'shipping_total',
        'service_fee',
        'tax_total',
        'grand_total',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'fulfillment_status' => FulfillmentStatus::class,
            'subtotal' => 'integer',
            'discount_total' => 'integer',
            'shipping_total' => 'integer',
            'service_fee' => 'integer',
            'tax_total' => 'integer',
            'grand_total' => 'integer',
        ];
    }

    /**
     * Customer who placed the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Voucher usages applied on this order.
     */
    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Line items snapshotted in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Shipping address and courier info.
     */
    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    /**
     * Logistics shipment record (Biteship auto-AWB).
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class)->latestOfMany();
    }

    /**
     * All shipment records.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Latest payment record (Duitku transaction).
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * All payment transaction logs.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Status transition audit history.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('created_at');
    }

    /**
     * Formatted grand total accessor.
     */
    protected function formattedGrandTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->grand_total, 0, ',', '.')
        );
    }

    /**
     * Formatted subtotal accessor.
     */
    protected function formattedSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->subtotal, 0, ',', '.')
        );
    }

    /**
     * Formatted service fee accessor.
     */
    protected function formattedServiceFee(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->service_fee, 0, ',', '.')
        );
    }

    /**
     * Scopes for order status filtering.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('order_status', OrderStatus::Pending);
    }

    public function scopeProcessing(Builder $query): void
    {
        $query->where('order_status', OrderStatus::Processing);
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('order_status', OrderStatus::Completed);
    }

    public function scopeCancelled(Builder $query): void
    {
        $query->where('order_status', OrderStatus::Cancelled);
    }
}

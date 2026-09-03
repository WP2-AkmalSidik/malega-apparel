<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'is_active',
        'marketing_opt_in',
        'membership_tier',
        'total_orders_count',
        'total_spend_amount',
        'saved_addresses',
        'wishlist',
        'last_login_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'marketing_opt_in' => 'boolean',
            'total_orders_count' => 'integer',
            'total_spend_amount' => 'integer',
            'saved_addresses' => 'array',
            'wishlist' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Orders placed by this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Formatted total spend accessor.
     */
    protected function formattedTotalSpend(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->total_spend_amount, 0, ',', '.')
        );
    }

    /**
     * Direct WhatsApp URL for marketing/customer support.
     */
    protected function whatsappUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62'.substr($cleanPhone, 1);
                }

                $msg = urlencode("Halo Kak {$this->name}, terima kasih telah menjadi pelanggan setia Malega Apparel! Kami punya penawaran eksklusif untuk koleksi terbaru SS26.");

                return "https://wa.me/{$cleanPhone}?text={$msg}";
            }
        );
    }

    /**
     * Recalculate membership tier based on lifetime spend.
     */
    public function updateMembershipTier(): void
    {
        if ($this->total_spend_amount >= 1500000) {
            $this->membership_tier = 'VIP Platinum';
        } elseif ($this->total_spend_amount >= 500000) {
            $this->membership_tier = 'Gold';
        } else {
            $this->membership_tier = 'Silver';
        }
        $this->saveQuietly();
    }
}

<?php

namespace Database\Seeders;

use App\Enums\VoucherType;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'MALEGAVIP15',
                'name' => 'VIP Gold Member 15% OFF',
                'description' => 'Diskon spesial 15% untuk member loyal Malega Apparel dengan minimal transaksi Rp 200.000.',
                'type' => VoucherType::Percentage,
                'amount' => 15,
                'max_discount_amount' => 50000,
                'min_order_amount' => 200000,
                'usage_limit_total' => 1000,
                'used_count' => 0,
                'usage_limit_per_user' => 1,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addMonths(6),
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'code' => 'FREESHIPXTRA',
                'name' => 'Gratis Pengiriman Seluruh ID',
                'description' => 'Subsidi ongkos kirim s.d. Rp 15.000 tanpa minimal belanja ke seluruh Indonesia.',
                'type' => VoucherType::FreeShipping,
                'amount' => 15000,
                'max_discount_amount' => 15000,
                'min_order_amount' => 0,
                'usage_limit_total' => 2500,
                'used_count' => 0,
                'usage_limit_per_user' => 3,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addMonths(12),
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'code' => 'NEWDROP50K',
                'name' => 'Potongan Langsung Rp 50.000',
                'description' => 'Potongan langsung Rp 50.000 untuk koleksi artikel New Drop dengan minimal belanja Rp 400.000.',
                'type' => VoucherType::FixedAmount,
                'amount' => 50000,
                'max_discount_amount' => 50000,
                'min_order_amount' => 400000,
                'usage_limit_total' => 500,
                'used_count' => 0,
                'usage_limit_per_user' => 1,
                'valid_from' => now()->subDays(5),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
                'is_public' => true,
            ],
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Atelier 10% OFF',
                'description' => 'Diskon sambutan pembeli pertama 10% tanpa minimal belanja.',
                'type' => VoucherType::Percentage,
                'amount' => 10,
                'max_discount_amount' => 30000,
                'min_order_amount' => 0,
                'usage_limit_total' => 5000,
                'used_count' => 0,
                'usage_limit_per_user' => 1,
                'valid_from' => now()->subDays(1),
                'valid_until' => now()->addYear(),
                'is_active' => true,
                'is_public' => true,
            ],
        ];

        foreach ($vouchers as $v) {
            Voucher::updateOrCreate(['code' => $v['code']], $v);
        }
    }
}

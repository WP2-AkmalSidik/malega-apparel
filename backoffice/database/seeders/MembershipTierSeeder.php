<?php

namespace Database\Seeders;

use App\Models\MembershipTier;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class MembershipTierSeeder extends Seeder
{
    public function run(): void
    {
        $welcomeVoucher = Voucher::where('code', 'WELCOME10')->first();
        $goldVoucher = Voucher::where('code', 'MALEGAVIP15')->first();
        $platinumVoucher = Voucher::where('code', 'MEMBERONLY20')->first();

        // 1. Silver Member
        MembershipTier::updateOrCreate(
            ['slug' => 'silver'],
            [
                'name' => 'Silver',
                'min_spend' => 0,
                'voucher_id' => $welcomeVoucher?->id,
                'badge_text' => '★ Silver Member',
                'badge_color' => 'slate',
                'discount_label' => 'Diskon 10%',
                'description' => 'Tingkat keanggotaan awal untuk seluruh pelanggan terdaftar Malega Apparel.',
                'order' => 1,
                'is_active' => true,
                'perks' => [
                    [
                        'icon' => 'gift',
                        'title' => 'Voucher Sambutan 10%',
                        'desc' => 'Kupon WELCOME10 untuk potongan 10% pesanan pertama.',
                    ],
                    [
                        'icon' => 'zap',
                        'title' => 'Realtime Live Tracking',
                        'desc' => 'Pantau status pesanan dan pergerakan resi ekspedisi langsung.',
                    ],
                    [
                        'icon' => 'sparkles',
                        'title' => 'Akses Rilis Resmi',
                        'desc' => 'Dapatkan pemberitahuan pertama saat katalog baru diluncurkan.',
                    ],
                ],
            ]
        );

        // 2. Gold Member
        MembershipTier::updateOrCreate(
            ['slug' => 'gold'],
            [
                'name' => 'Gold',
                'min_spend' => 500000,
                'voucher_id' => $goldVoucher?->id,
                'badge_text' => '★ Gold Member',
                'badge_color' => 'amber',
                'discount_label' => 'Diskon 15%',
                'description' => 'Tingkat member reguler bagi pelanggan yang telah berbelanja minimal Rp 500.000.',
                'order' => 2,
                'is_active' => true,
                'perks' => [
                    [
                        'icon' => 'gift',
                        'title' => 'Diskon 15% New Drop',
                        'desc' => 'Kupon MALEGAVIP15 untuk diskon 15% setiap peluncuran artikel baru.',
                    ],
                    [
                        'icon' => 'truck',
                        'title' => 'Bebas / Subsidi Ongkir',
                        'desc' => 'Gratis atau potongan ongkos kirim reguler ke seluruh kota di Indonesia.',
                    ],
                    [
                        'icon' => 'zap',
                        'title' => 'Priority Dispatch Packing',
                        'desc' => 'Paket diprioritaskan untuk proses QC dan pengiriman oleh tim gudang.',
                    ],
                ],
            ]
        );

        // 3. VIP Platinum Member
        MembershipTier::updateOrCreate(
            ['slug' => 'vip-platinum'],
            [
                'name' => 'VIP Platinum',
                'min_spend' => 1500000,
                'voucher_id' => $platinumVoucher?->id,
                'badge_text' => '👑 VIP Platinum Member',
                'badge_color' => 'gold',
                'discount_label' => 'Diskon 20%',
                'description' => 'Tingkat eksklusif tertinggi bagi pelanggan loyal dengan akumulasi belanja minimal Rp 1.500.000.',
                'order' => 3,
                'is_active' => true,
                'perks' => [
                    [
                        'icon' => 'crown',
                        'title' => 'Diskon 20% Seumur Hidup',
                        'desc' => 'Kupon MEMBERONLY20 untuk potongan 20% all-item tanpa batas kuota.',
                    ],
                    [
                        'icon' => 'clock',
                        'title' => 'Early-Bird Drop 24 Jam',
                        'desc' => 'Beli koleksi limited edition 24 jam lebih awal sebelum dirilis ke publik.',
                    ],
                    [
                        'icon' => 'sparkles',
                        'title' => 'VIP WhatsApp Concierge',
                        'desc' => 'Akses nomor WhatsApp personal assist khusus konsultasi size & fast-track order.',
                    ],
                ],
            ]
        );
    }
}

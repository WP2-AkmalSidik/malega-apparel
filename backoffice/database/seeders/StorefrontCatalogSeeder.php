<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Collection;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StorefrontCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Categories
            $catTshirts = Category::firstOrCreate(['slug' => 't-shirts'], [
                'name' => 'T-Shirts',
                'description' => 'Kaos heavyweight 300GSM dan vintage washed drop-shoulder.',
                'is_active' => true,
            ]);

            $catOuterwear = Category::firstOrCreate(['slug' => 'outerwear'], [
                'name' => 'Outerwear',
                'description' => 'Hoodies French Terry 380GSM dan overshirt denim 14oz.',
                'is_active' => true,
            ]);

            $catBottoms = Category::firstOrCreate(['slug' => 'bottoms'], [
                'name' => 'Bottoms',
                'description' => 'Celana cargo ripstop taktis dan selvedge denim jeans.',
                'is_active' => true,
            ]);

            $catAccessories = Category::firstOrCreate(['slug' => 'accessories'], [
                'name' => 'Accessories',
                'description' => 'Topi monogram bordir emas dan tas modular tactical.',
                'is_active' => true,
            ]);

            // 2. Collections / Lookbooks
            $col300Gsm = Collection::firstOrCreate(['slug' => 'heavyweight-boxy-tees-300gsm'], [
                'name' => 'Heavyweight Boxy Tees (300GSM)',
                'subtitle' => 'Siluet Boxy Drop-Shoulder & Kerah Rib 3.5cm Kokoh',
                'season' => 'Spring / Summer',
                'release_year' => '2026',
                'badge' => '300GSM HEAVYWEIGHT',
                'cover_image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => '100% Combed Cotton Heavyweight',
                'gsm_weight' => 300,
                'description' => 'Koleksi kaos esensial streetwear berkarakter kuat dengan gramasi 300GSM padat murni, tidak terawang, dan anti-susut pasca pencucian berulang.',
                'storytelling' => 'Dirancang di Atelier Malega dengan presisi potongan bahu drop-shoulder kontemporer serta teknik pewarnaan Reactive Eco-Dye pekat abadi.',
                'palette' => ['#0B132B', '#1C2541', '#CBAC70', '#E2E8F0'],
                'tags' => ['300GSM', 'Drop Shoulder', 'Rib 3.5cm', 'Anti-Melar'],
                'is_active' => true,
            ]);

            $colOuter = Collection::firstOrCreate(['slug' => 'french-terry-outerwear-series'], [
                'name' => 'French Terry Hoodies & Outerwear',
                'subtitle' => '380GSM Heavy French Terry & Double Layered Hood',
                'season' => 'Autumn / Winter',
                'release_year' => '2026',
                'badge' => '380GSM FRENCH TERRY',
                'cover_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => '100% Cotton French Terry Fleece',
                'gsm_weight' => 380,
                'description' => 'Rilisan jaket hoodie dan outerwear premium berbahan rajutan French Terry 380GSM dengan siluet boxy bervolume elegan.',
                'storytelling' => 'Menawarkan insulasi termal optimal yang tetap sejuk di iklim tropis, dilengkapi lubang tali eyelet matte gold dan saku kanguru tersembunyi.',
                'palette' => ['#0B132B', '#334155', '#CBAC70'],
                'tags' => ['380GSM', 'French Terry', 'Double Hood', 'Boxy Outer'],
                'is_active' => true,
            ]);

            $colTactical = Collection::firstOrCreate(['slug' => 'tactical-utility-bottoms'], [
                'name' => 'Utility Cargo & Selvedge Denim',
                'subtitle' => 'Ripstop Multi-Pocket & 14oz Raw Selvedge Denim',
                'season' => 'All-Year Capsule',
                'release_year' => '2026',
                'badge' => 'TACTICAL & RAW DENIM',
                'cover_image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Water-Repellent Ripstop & 14oz Raw Denim',
                'description' => 'Celana fungsional dengan kompartemen taktis serbaguna dan denim selvedge kaku yang akan membentuk garis fade personal seiring pemakaian.',
                'storytelling' => 'Memadukan ketahanan bahan militer dengan siluet relaxed straight modern untuk kenyamanan mobilitas urban harian.',
                'palette' => ['#0B132B', '#1E293B', '#475569'],
                'tags' => ['Ripstop', '14oz Denim', 'Multi-Pocket', 'YKK Zippers'],
                'is_active' => true,
            ]);

            $colAtelier = Collection::firstOrCreate(['slug' => 'atelier-luxury-accessories'], [
                'name' => 'Monogram Caps & Atelier Bags',
                'subtitle' => 'Bespoke Gold Embroidery & Cordura 1000D',
                'season' => 'Permanent Essentials',
                'release_year' => '2026',
                'badge' => 'GOLD EMBROIDERY',
                'cover_image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Heavy Cotton Twill & Cordura Water-Resistant',
                'description' => 'Kurasi aksesoris pelengkap gaya berpakaian dengan bordir monogram geometris benang emas dan konstruksi gesper logam brass solid.',
                'storytelling' => 'Sentuhan akhir penyempurna busana streetwear mewah yang fungsional dan berdaya tahan jangka panjang.',
                'palette' => ['#0B132B', '#CBAC70', '#FDFCFF'],
                'tags' => ['Monogram', 'Gold Stitch', 'Cordura', 'Solid Brass'],
                'is_active' => true,
            ]);

            $colObsidian = Collection::firstOrCreate(['slug' => 'ss26-capsule-drop-the-obsidian'], [
                'name' => 'SS26 Capsule: The Obsidian Drop',
                'subtitle' => 'Limited Production Run • Rilisan Musim 2026',
                'season' => 'Limited Capsule SS26',
                'release_year' => '2026',
                'badge' => 'LIMITED CAPSULE',
                'cover_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Signature Heavy Combed & Corduroy Accents',
                'gsm_weight' => 300,
                'description' => 'Rilisan kapsul bertema Obsidian yang memadukan kegelapan pekat Cosmic Navy dengan aksen emas Champagne Bronze.',
                'storytelling' => 'Setiap artikel diproduksi dalam kuota terbatas dengan nomor seri keaslian sertifikat otentik dari Malega Studio.',
                'palette' => ['#0B132B', '#CBAC70', '#111D42'],
                'tags' => ['Limited Drop', 'Obsidian Series', 'Numbered Edition'],
                'is_active' => true,
            ]);

            $colCore = Collection::firstOrCreate(['slug' => 'core-heritage-bestsellers'], [
                'name' => 'Core Heritage Bestsellers Archive',
                'subtitle' => 'Arsip Produk Terlaris Sepanjang Masa Malega',
                'season' => 'Permanent Lineup',
                'release_year' => '2026',
                'badge' => 'HERITAGE ARCHIVE',
                'cover_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Heritage Combed & Raw Denim',
                'description' => 'Koleksi busana dengan angka repeat order tertinggi yang menjadi fondasi kualitas pakaian Malega Apparel sejak awal berdiri.',
                'storytelling' => 'Diverifikasi oleh ribuan ulasan bintang 5 pembeli di seluruh Indonesia.',
                'palette' => ['#0B132B', '#CBAC70'],
                'tags' => ['Bestseller', 'Heritage', 'Iconic Cut'],
                'is_active' => true,
            ]);

            // 3. Define 8 Real Products Data
            $productsData = [
                // --- PRODUCT 1: Obsidian Heavyweight Boxy Tee ---
                [
                    'name' => 'Obsidian Heavyweight Boxy Tee 300GSM',
                    'slug' => 'obsidian-heavyweight-boxy-tee-300gsm',
                    'subtitle' => 'Signature Drop Shoulder • Combed 300GSM Cotton • High Density Stitching',
                    'badge' => 'SS26 DROP',
                    'rating' => 4.90,
                    'review_count' => 1420,
                    'sold_count' => 3840,
                    'category_id' => $catTshirts->id,
                    'material' => '100% Combed Heavy Cotton 300 GSM',
                    'gsm' => 300,
                    'fit' => 'Modern Boxy Drop Shoulder Oversized',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Obsidian Heavyweight Boxy Tee adalah karya esensial streetwear dari Malega Apparel. Dikonstruksi khusus menggunakan 100% Combed Heavy Cotton 300GSM murni yang menghadirkan tekstur kokoh, jatuh kain yang rapi, dan kenyamanan termal untuk gaya hidup aktif perkotaan.',
                    'features' => [
                        'Material 300GSM Heavy Cotton bebas susut & tebal',
                        'Kerah Rib 3.5cm ganda anti-melar',
                        'Pola cutting drop-shoulder boxy proporsional',
                        'Pewarnaan Reactive Dye ramah lingkungan dan awet',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Gramasi' => '300 GSM Heavyweight',
                        'Material' => '100% Pure Combed Cotton',
                        'Cutting' => 'Boxy Fit / Drop Shoulder',
                        'Kerah' => '3.5cm Reinforced Rib Collar',
                        'Perawatan' => 'Cuci dengan air dingin, setrika temperatur sedang',
                    ],
                    'collections' => [$col300Gsm->id, $colObsidian->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 229000,
                    'compare_at_price' => 289000,
                    'weight_grams' => 350,
                    'colors' => [
                        ['name' => 'Onyx Black', 'hex' => '#111827', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Washed Olive', 'hex' => '#3f4834', 'code' => 'OLV', 'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Slate Charcoal', 'hex' => '#334155', 'code' => 'CHA', 'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Vintage Acid Wash', 'hex' => '#64748b', 'code' => 'ACD', 'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'stock_per_sku' => 5,
                ],

                // --- PRODUCT 2: Minimalist Boxy Fleece Hoodie ---
                [
                    'name' => 'Minimalist Boxy Fleece Hoodie 380GSM',
                    'slug' => 'minimalist-fleece-boxy-hoodie-380gsm',
                    'subtitle' => 'Heavy French Terry • Double Layer Hood • Hidden Pouch',
                    'badge' => 'SS26 DROP',
                    'rating' => 5.00,
                    'review_count' => 890,
                    'sold_count' => 1950,
                    'category_id' => $catOuterwear->id,
                    'material' => '100% French Terry Cotton 380 GSM',
                    'gsm' => 380,
                    'fit' => 'Boxy Relaxed Silhouette',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Dibuat untuk ketahanan dan siluet modern yang bersih, Minimalist Boxy Fleece Hoodie memadukan kenyamanan kain French Terry premium dengan potongan tegas pada bahu dan torso.',
                    'features' => [
                        'Material 380GSM French Terry ultra-lembut',
                        'Double-layer hood tanpa tali untuk tampilan ultra-clean',
                        'Kantung kangguru seamless internal',
                        'Manset rib elastis tebal',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Gramasi' => '380 GSM Heavy French Terry',
                        'Material' => '100% Cotton French Terry',
                        'Hood' => 'Double Layer Seamless',
                        'Fit' => 'Structured Boxy Cut',
                    ],
                    'collections' => [$colOuter->id, $colObsidian->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 449000,
                    'compare_at_price' => 549000,
                    'weight_grams' => 650,
                    'colors' => [
                        ['name' => 'Midnight Black', 'hex' => '#0f172a', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Washed Taupe', 'hex' => '#78716c', 'code' => 'TPE', 'image' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Forest Olive', 'hex' => '#2e3828', 'code' => 'OLV', 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'stock_per_sku' => 3,
                ],

                // --- PRODUCT 3: Tactical Ripstop Utility Cargo Pants ---
                [
                    'name' => 'Tactical Ripstop Utility Cargo Pants',
                    'slug' => 'tactical-ripstop-utility-cargo-pants',
                    'subtitle' => 'Reinforced Diamond Ripstop • 6 Modular Pockets • Adjustable Cuff',
                    'badge' => 'BEST SELLER',
                    'rating' => 4.80,
                    'review_count' => 650,
                    'sold_count' => 1420,
                    'category_id' => $catBottoms->id,
                    'material' => 'Cotton Twill Ripstop 280 GSM',
                    'gsm' => 280,
                    'fit' => 'Tapered Utility Fit',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Celana kargo utilitas yang menggabungkan fungsionalitas outdoor dengan siluet streetwear modern. Dilengkapi konstruksi jahitan bar-tack pada area bertekanan tinggi.',
                    'features' => [
                        'Bahan Ripstop tahan robek & breathable',
                        '6 saku utilitas ergonomis dengan penutup magnetik',
                        'Karet pinggang elastis dengan buckle slider',
                        'Tali serut pergelangan kaki (adjustable cuff)',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Material' => 'Cotton Twill Diamond Ripstop',
                        'Pockets' => '6 Utility Compartments',
                        'Hardware' => 'YKK Zippers & D-Ring',
                    ],
                    'collections' => [$colTactical->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 399000,
                    'compare_at_price' => 489000,
                    'weight_grams' => 500,
                    'colors' => [
                        ['name' => 'Slate Charcoal', 'hex' => '#1e293b', 'code' => 'CHA', 'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Desert Khaki', 'hex' => '#a89f91', 'code' => 'KHK', 'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'stock_per_sku' => 5,
                ],

                // --- PRODUCT 4: Selvedge Denim Workwear Overshirt 14oz ---
                [
                    'name' => 'Selvedge Denim Workwear Overshirt 14oz',
                    'slug' => 'selvedge-denim-workwear-overshirt-14oz',
                    'subtitle' => 'Raw Indigo Selvedge • Antique Brass Hardware • Boxy Fit',
                    'badge' => 'NEW DROP',
                    'rating' => 4.90,
                    'review_count' => 310,
                    'sold_count' => 780,
                    'category_id' => $catOuterwear->id,
                    'material' => '14oz Raw Rigid Selvedge Denim',
                    'gsm' => 420,
                    'fit' => 'Structured Boxy Overshirt',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Workwear overshirt dengan selvedge denim murni yang akan menghasilkan fade unik dan personal seiring pemakaian Anda.',
                    'features' => [
                        'Bahan 14oz Pure Selvedge Denim',
                        'Kancing logam antik berukir logo Malega',
                        'Dual chest flap pockets',
                        'Aksen selvedge line merah pada bagian placket dalam',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Berat Kain' => '14 oz Selvedge',
                        'Fitting' => 'Relaxed Overshirt',
                        'Hardware' => 'Custom Antique Brass',
                    ],
                    'collections' => [$colOuter->id, $colTactical->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1543087903-1ac2ec7aa8c5?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 529000,
                    'compare_at_price' => 629000,
                    'weight_grams' => 600,
                    'colors' => [
                        ['name' => 'Raw Deep Indigo', 'hex' => '#1e3a8a', 'code' => 'IND', 'image' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Acid Washed Grey', 'hex' => '#475569', 'code' => 'GRY', 'image' => 'https://images.unsplash.com/photo-1543087903-1ac2ec7aa8c5?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'stock_per_sku' => 4,
                ],

                // --- PRODUCT 5: Vintage Washed Drop-Shoulder Tee 280GSM ---
                [
                    'name' => 'Vintage Washed Drop-Shoulder Tee 280GSM',
                    'slug' => 'vintage-washed-drop-shoulder-tee',
                    'subtitle' => 'Sun-Faded Wash • Distressed Edge • 280GSM Combed',
                    'badge' => 'TOP SELLER',
                    'rating' => 4.80,
                    'review_count' => 920,
                    'sold_count' => 2450,
                    'category_id' => $catTshirts->id,
                    'material' => '100% Combed Cotton Vintage Wash',
                    'gsm' => 280,
                    'fit' => 'Drop Shoulder Boxy',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Memberikan estetika vintage autentik dengan kelembutan maksimal. Setiap lembar memiliki gradasi wash yang unik dan berkarakter.',
                    'features' => [
                        'Proses stone wash & acid wash eksklusif',
                        'Sentuhan lembut bertekstur vintage',
                        'Pola fitting proporsional',
                        'Kerah rib anti-melar',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Gramasi' => '280 GSM Vintage',
                        'Wash' => 'Garment Stone Wash',
                    ],
                    'collections' => [$col300Gsm->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 219000,
                    'compare_at_price' => 269000,
                    'weight_grams' => 320,
                    'colors' => [
                        ['name' => 'Washed Charcoal', 'hex' => '#334155', 'code' => 'CHA', 'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Washed Moss', 'hex' => '#3d4a36', 'code' => 'MOS', 'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'stock_per_sku' => 6,
                ],

                // --- PRODUCT 6: Structured Minimal 6-Panel Gold Monogram Cap ---
                [
                    'name' => 'Structured Minimal 6-Panel Gold Monogram Cap',
                    'slug' => 'structured-minimal-6-panel-cap',
                    'subtitle' => 'Heavy Cotton Twill • 3D Gold Embroidered MA • Brass Buckle',
                    'badge' => 'SS26 DROP',
                    'rating' => 4.90,
                    'review_count' => 410,
                    'sold_count' => 980,
                    'category_id' => $catAccessories->id,
                    'material' => 'Premium Heavy Cotton Twill',
                    'gsm' => 320,
                    'fit' => 'Adjustable Unstructured / Semi-Structured',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Aksesori esensial harian dengan detail monogram emas eksklusif yang menyempurnakan outfit streetwear Anda.',
                    'features' => [
                        'Bordir 3D High-Density Monogram MA Emas (#CBAC70)',
                        'Buckle belakang kuningan solid tahan karat',
                        'Visor lengkung presisi dengan 6 jahitan',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Material' => 'Heavy Cotton Twill',
                        'Hardware' => 'Antique Solid Brass',
                    ],
                    'collections' => [$colAtelier->id, $col300Gsm->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 189000,
                    'compare_at_price' => 229000,
                    'weight_grams' => 150,
                    'colors' => [
                        ['name' => 'Onyx Black / Gold', 'hex' => '#0f172a', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80'],
                        ['name' => 'Navy Sand', 'hex' => '#1e293b', 'code' => 'NAV', 'image' => 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['All Size (Adjustable)'],
                    'stock_per_sku' => 20,
                ],

                // --- PRODUCT 7: Modular Matte Leather & Cordura Crossbody Bag ---
                [
                    'name' => 'Modular Matte Leather & Cordura Crossbody Bag',
                    'slug' => 'modular-matte-leather-crossbody-bag',
                    'subtitle' => 'Waterproof YKK Hardware • Vegan Leather & Cordura 1000D',
                    'badge' => 'SS26 DROP',
                    'rating' => 5.00,
                    'review_count' => 280,
                    'sold_count' => 650,
                    'category_id' => $catAccessories->id,
                    'material' => 'Matte Vegan Leather + Cordura 1000D',
                    'gsm' => 450,
                    'fit' => 'Ergonomic Crossbody',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Tas selempang berdesain taktis dan modern untuk membawa kebutuhan harian esensial dengan aman dan penuh gaya.',
                    'features' => [
                        'Kompartemen utama dengan slot tablet 8-inch',
                        'Ritsleting YKK Aquaguard tahan air',
                        'Strap nylon tebal dengan quick-release magnetic buckle',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Material' => 'Cordura 1000D + Vegan Leather',
                        'Dimensi' => '26 cm x 18 cm x 7 cm',
                    ],
                    'collections' => [$colAtelier->id, $colObsidian->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 279000,
                    'compare_at_price' => 349000,
                    'weight_grams' => 400,
                    'colors' => [
                        ['name' => 'Matte Obsidian', 'hex' => '#090d16', 'code' => 'OBS', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['One Size'],
                    'stock_per_sku' => 30,
                ],

                // --- PRODUCT 8: Raw Indigo Relaxed Straight Jeans 13.5oz ---
                [
                    'name' => 'Raw Indigo Relaxed Straight Jeans 13.5oz',
                    'slug' => 'raw-indigo-relaxed-straight-jeans',
                    'subtitle' => 'Sanforized Raw Denim • Chainstitch Hem • Custom Gold Rivets',
                    'badge' => 'HERITAGE',
                    'rating' => 4.80,
                    'review_count' => 190,
                    'sold_count' => 480,
                    'category_id' => $catBottoms->id,
                    'material' => '13.5oz Sanforized Raw Denim',
                    'gsm' => 400,
                    'fit' => 'Relaxed Straight Leg',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Celana jeans raw indigo potongan relaxed straight klasik yang memberikan ruang gerak leluasa dan siluet kokoh.',
                    'features' => [
                        'Denim murni kaku yang siap fading',
                        'Jahitan rantai Union Special pada hem bawah',
                        'Rivet tembaga emas berlogo Malega',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Berat Kain' => '13.5 oz Raw Denim',
                        'Cut' => 'Relaxed Straight',
                    ],
                    'collections' => [$colTactical->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 439000,
                    'compare_at_price' => 529000,
                    'weight_grams' => 650,
                    'colors' => [
                        ['name' => 'Deep Raw Indigo', 'hex' => '#1e3a8a', 'code' => 'IND', 'image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80'],
                    ],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'stock_per_sku' => 7,
                ],
            ];

            // 4. Seed Products, Variants, Inventory Items, and Media Images
            foreach ($productsData as $pData) {
                $product = Product::updateOrCreate(
                    ['slug' => $pData['slug']],
                    [
                        'name' => $pData['name'],
                        'subtitle' => $pData['subtitle'],
                        'badge' => $pData['badge'],
                        'rating' => $pData['rating'],
                        'review_count' => $pData['review_count'],
                        'sold_count' => $pData['sold_count'],
                        'category_id' => $pData['category_id'],
                        'description' => $pData['description'],
                        'material' => $pData['material'],
                        'gsm' => $pData['gsm'],
                        'fit' => $pData['fit'],
                        'origin' => $pData['origin'],
                        'features' => $pData['features'],
                        'specifications' => $pData['specifications'],
                        'status' => ProductStatus::Active,
                        'featured_image' => $pData['featured_image'],
                    ]
                );

                // Sync collection relations
                if (! empty($pData['collections'])) {
                    $product->collections()->sync($pData['collections']);
                }

                // Seed Gallery Images
                $product->images()->delete();
                foreach ($pData['gallery'] as $index => $imageUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imageUrl,
                        'is_primary' => ($index === 0),
                        'sort_order' => $index,
                    ]);
                }

                // Seed Matrix of Variants (Colors x Sizes)
                $skuPrefix = 'MLG-'.strtoupper(substr(str_replace('-', '', $pData['slug']), 0, 4));

                foreach ($pData['colors'] as $color) {
                    foreach ($pData['sizes'] as $size) {
                        $sizeClean = Str::slug($size);
                        $sku = "{$skuPrefix}-{$color['code']}-".strtoupper($sizeClean);
                        $variantTitle = "{$product->name} - {$color['name']} / {$size}";

                        $variant = ProductVariant::updateOrCreate(
                            ['sku' => $sku],
                            [
                                'product_id' => $product->id,
                                'title' => $variantTitle,
                                'color_name' => $color['name'],
                                'color_hex' => $color['hex'],
                                'size' => $size,
                                'image_url' => $color['image'],
                                'price' => $pData['price'],
                                'compare_at_price' => $pData['compare_at_price'],
                                'cost_price' => (int) round($pData['price'] * 0.45),
                                'weight_grams' => $pData['weight_grams'],
                                'is_active' => true,
                            ]
                        );

                        // Seed or Update Inventory Balance
                        $stockQty = $pData['stock_per_sku'] ?? 10;

                        $inv = InventoryItem::updateOrCreate(
                            ['variant_id' => $variant->id],
                            [
                                'on_hand' => $stockQty,
                                'reserved' => 0,
                            ]
                        );

                        // Record Initial Stock Movement
                        StockMovement::create([
                            'inventory_item_id' => $inv->id,
                            'type' => \App\Enums\StockMovementType::Inbound,
                            'quantity_change' => $stockQty,
                            'on_hand_before' => 0,
                            'on_hand_after' => $stockQty,
                            'reserved_before' => 0,
                            'reserved_after' => 0,
                            'reference_note' => 'Inisialisasi stok fisik katalog toko Malega SS26.',
                        ]);
                    }
                }
            }
        });
    }
}

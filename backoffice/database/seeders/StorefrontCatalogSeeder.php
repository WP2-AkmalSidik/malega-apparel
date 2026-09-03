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
                'banner_image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Cotton Ripstop & Raw Rigid Selvedge',
                'gsm_weight' => 400,
                'description' => 'Koleksi celana kargo taktis dan selvedge jeans konstruksi heavy-duty dengan jahitan bar-tack penguat di setiap titik stres kain.',
                'storytelling' => 'Mengawinkan fungsionalitas kompartemen taktis serbaguna dengan potongan modern yang pas untuk sepatu chunky maupun boots.',
                'palette' => ['#1E293B', '#334155', '#A89F91', '#CBAC70'],
                'tags' => ['Ripstop', 'Raw Denim', 'Modular Pockets', 'YKK Hardware'],
                'is_active' => true,
            ]);

            $colObsidian = Collection::firstOrCreate(['slug' => 'ss26-the-brutalist-monolith'], [
                'name' => 'SS26: The Brutalist Monolith',
                'subtitle' => 'Edisi Terbatas Monokromatik & Siluet Arsitektural',
                'season' => 'SS26 Limited Release',
                'release_year' => '2026',
                'badge' => 'LIMITED CAPSULE',
                'cover_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Ultra-Dense Heavy Combed Cotton',
                'gsm_weight' => 300,
                'description' => 'Kapsul terbatas bertema arsitektur brutalisme dengan gradasi warna pekat, bordir monokrom halus, dan double packaging mewah.',
                'storytelling' => 'Hanya diproduksi sebanyak 250 unit per artikel secara bespoke di pabrik garmen rekanan Malega.',
                'palette' => ['#050914', '#0B132B', '#CBAC70'],
                'tags' => ['Limited Drop', 'Brutalist', 'SS26', 'Numbered Series'],
                'is_active' => true,
            ]);

            $colCore = Collection::firstOrCreate(['slug' => 'signature-bestsellers-archive'], [
                'name' => 'Signature Bestsellers Archive',
                'subtitle' => 'Artikel Terfavorit Pilihan Komunitas Streetwear',
                'season' => 'Core Permanent Line',
                'release_year' => '2026',
                'badge' => 'COMMUNITY CHOICE',
                'cover_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
                'banner_image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=1200&auto=format&fit=crop&q=80',
                'featured_material' => 'Heritage Combed & Raw Denim',
                'description' => 'Koleksi busana dengan angka repeat order tertinggi yang menjadi fondasi kualitas pakaian Malega Apparel sejak awal berdiri.',
                'storytelling' => 'Diverifikasi oleh ribuan ulasan bintang 5 pembeli di seluruh Indonesia.',
                'palette' => ['#0B132B', '#CBAC70'],
                'tags' => ['Bestseller', 'Heritage', 'Iconic Cut'],
                'is_active' => true,
            ]);

            // 3. Define 8 Real Products Data with Explicit Variant Pricing Matrix
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
                        ['name' => 'Onyx Black', 'hex' => '#111827', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Washed Olive', 'hex' => '#3f4834', 'code' => 'OLV', 'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80', 'price_extra' => 10000],
                        ['name' => 'Slate Charcoal', 'hex' => '#334155', 'code' => 'CHA', 'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Vintage Acid Wash', 'hex' => '#64748b', 'code' => 'ACD', 'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80', 'price_extra' => 20000],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'size_price_extra' => [
                        'S' => 0,
                        'M' => 0,
                        'L' => 0,
                        'XL' => 0,
                        'XXL' => 15000,
                    ],
                    'stock_per_sku' => 8,
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
                        ['name' => 'Midnight Black', 'hex' => '#0f172a', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Washed Taupe', 'hex' => '#78716c', 'code' => 'TPE', 'image' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=900&auto=format&fit=crop&q=80', 'price_extra' => 15000],
                        ['name' => 'Forest Olive', 'hex' => '#2e3828', 'code' => 'OLV', 'image' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80', 'price_extra' => 10000],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'size_price_extra' => [
                        'S' => 0,
                        'M' => 0,
                        'L' => 0,
                        'XL' => 0,
                        'XXL' => 25000,
                    ],
                    'stock_per_sku' => 6,
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
                        ['name' => 'Slate Charcoal', 'hex' => '#1e293b', 'code' => 'CHA', 'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Desert Khaki', 'hex' => '#a89f91', 'code' => 'KHK', 'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=80', 'price_extra' => 10000],
                    ],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'size_price_extra' => [
                        '28' => 0,
                        '30' => 0,
                        '32' => 0,
                        '34' => 15000,
                        '36' => 20000,
                    ],
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
                        ['name' => 'Raw Deep Indigo', 'hex' => '#1e3a8a', 'code' => 'IND', 'image' => 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Acid Washed Grey', 'hex' => '#475569', 'code' => 'GRY', 'image' => 'https://images.unsplash.com/photo-1543087903-1ac2ec7aa8c5?w=900&auto=format&fit=crop&q=80', 'price_extra' => 30000],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL'],
                    'size_price_extra' => [
                        'S' => 0,
                        'M' => 0,
                        'L' => 0,
                        'XL' => 15000,
                    ],
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
                    'description' => 'Melalui proses pencucian enzym khusus untuk menghasilkan efek usang (vintage look) yang otentik dan tekstur lembut sejak pemakaian pertama.',
                    'features' => [
                        'Tekstur soft hand-feel berkat enzym wash',
                        'Jahitan rantai ganda kokoh di pundak',
                        'Efek pudar warna alami',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Gramasi' => '280 GSM Cotton Combed',
                        'Finishing' => 'Enzyme Washed',
                    ],
                    'collections' => [$col300Gsm->id, $colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
                        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 219000,
                    'compare_at_price' => 269000,
                    'weight_grams' => 320,
                    'colors' => [
                        ['name' => 'Washed Carbon', 'hex' => '#374151', 'code' => 'CRB', 'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Sun Faded Clay', 'hex' => '#9a3412', 'code' => 'CLY', 'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80', 'price_extra' => 15000],
                    ],
                    'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                    'size_price_extra' => [
                        'S' => 0,
                        'M' => 0,
                        'L' => 0,
                        'XL' => 0,
                        'XXL' => 15000,
                    ],
                    'stock_per_sku' => 6,
                ],

                // --- PRODUCT 6: Atelier Monogram Embroidered Cap ---
                [
                    'name' => 'Atelier Monogram Embroidered Cap',
                    'slug' => 'atelier-monogram-embroidered-cap',
                    'subtitle' => 'High-Density 3D Gold Embroidery • Brushed Cotton Twill',
                    'badge' => 'ACCESSORIES',
                    'rating' => 4.90,
                    'review_count' => 410,
                    'sold_count' => 1100,
                    'category_id' => $catAccessories->id,
                    'material' => '100% Brushed Cotton Twill',
                    'gsm' => 260,
                    'fit' => 'Unstructured 6-Panel Low Profile',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Topi 6-panel dengan bordir timbul monogram MALEGA benang emas metalik, dilengkapi buckle besi kuningan di bagian belakang.',
                    'features' => [
                        'Bordir 3D High Density benang emas',
                        'Strap penyesuai logam kuningan antik',
                        'Lapisan sweatband penyerap keringat',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Model' => '6-Panel Dad Cap',
                        'Closure' => 'Antique Brass Buckle',
                    ],
                    'collections' => [$colCore->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 179000,
                    'compare_at_price' => 219000,
                    'weight_grams' => 150,
                    'colors' => [
                        ['name' => 'Obsidian Black', 'hex' => '#090d16', 'code' => 'BLK', 'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Gold Monogram Navy', 'hex' => '#0c1b33', 'code' => 'NVY', 'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80', 'price_extra' => 10000],
                    ],
                    'sizes' => ['One Size'],
                    'size_price_extra' => [
                        'One Size' => 0,
                    ],
                    'stock_per_sku' => 15,
                ],

                // --- PRODUCT 7: Double Knee Heavy Canvas Carpenter Pants ---
                [
                    'name' => 'Double Knee Heavy Canvas Carpenter Pants',
                    'slug' => 'double-knee-heavy-canvas-carpenter-pants',
                    'subtitle' => '12oz Heavy Duck Canvas • Reinforced Rivets • Hammer Loop',
                    'badge' => 'WORKWEAR',
                    'rating' => 4.85,
                    'review_count' => 480,
                    'sold_count' => 980,
                    'category_id' => $catBottoms->id,
                    'material' => '12oz Cotton Duck Canvas',
                    'gsm' => 390,
                    'fit' => 'Relaxed Straight Fit',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Konstruksi panel lutut ganda (double-knee) dengan paku keling tembaga yang menghadirkan siluet workwear tangguh khas tradisi pekerja.',
                    'features' => [
                        'Panel lutut ganda tahan gesekan',
                        'Hammer loop dan saku penggaris samping',
                        'Triple-needle stitch di setiap sambungan utama',
                    ],
                    'specifications' => [
                        'Brand' => 'Malega Apparel',
                        'Material' => '12oz Heavy Duck Canvas',
                        'Cut' => 'Straight Leg Carpenter',
                    ],
                    'collections' => [$colTactical->id],
                    'gallery' => [
                        'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=900&auto=format&fit=crop&q=80',
                    ],
                    'price' => 419000,
                    'compare_at_price' => 509000,
                    'weight_grams' => 600,
                    'colors' => [
                        ['name' => 'Off-White Natural', 'hex' => '#f1f5f9', 'code' => 'NAT', 'image' => 'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                        ['name' => 'Mocha Brown', 'hex' => '#451a03', 'code' => 'MCH', 'image' => 'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=900&auto=format&fit=crop&q=80', 'price_extra' => 15000],
                    ],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'size_price_extra' => [
                        '28' => 0,
                        '30' => 0,
                        '32' => 0,
                        '34' => 15000,
                        '36' => 20000,
                    ],
                    'stock_per_sku' => 5,
                ],

                // --- PRODUCT 8: Heritage 13.5oz Relaxed Raw Denim Jeans ---
                [
                    'name' => 'Heritage 13.5oz Relaxed Raw Denim Jeans',
                    'slug' => 'heritage-relaxed-raw-denim-jeans',
                    'subtitle' => '13.5oz Sanforized Raw Denim • Button Fly • Copper Rivets',
                    'badge' => 'HERITAGE',
                    'rating' => 4.95,
                    'review_count' => 760,
                    'sold_count' => 1650,
                    'category_id' => $catBottoms->id,
                    'material' => '13.5oz 100% Cotton Raw Denim',
                    'gsm' => 400,
                    'fit' => 'Relaxed Straight Cut',
                    'origin' => 'Bandung, Indonesia',
                    'featured_image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80',
                    'description' => 'Jeans raw denim berpotongan relaxed klasik yang kaku pada awal pemakaian dan akan membentuk kontur tubuh serta pola fading menawan.',
                    'features' => [
                        'Bahan 13.5oz Sanforized Raw Denim murni',
                        'Button fly dengan kancing kuningan kustom',
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
                        ['name' => 'Deep Raw Indigo', 'hex' => '#1e3a8a', 'code' => 'IND', 'image' => 'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80', 'price_extra' => 0],
                    ],
                    'sizes' => ['28', '30', '32', '34', '36'],
                    'size_price_extra' => [
                        '28' => 0,
                        '30' => 0,
                        '32' => 0,
                        '34' => 15000,
                        '36' => 25000,
                    ],
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

                // Seed Matrix of Variants (Colors x Sizes with Variant-Specific Pricing)
                $skuPrefix = 'MLG-'.strtoupper(substr(str_replace('-', '', $pData['slug']), 0, 4));

                foreach ($pData['colors'] as $color) {
                    $colorExtra = $color['price_extra'] ?? 0;

                    foreach ($pData['sizes'] as $size) {
                        $sizeExtra = $pData['size_price_extra'][$size] ?? 0;
                        $finalVariantPrice = $pData['price'] + $colorExtra + $sizeExtra;
                        $finalCompareAt = $pData['compare_at_price'] ? ($pData['compare_at_price'] + $colorExtra + $sizeExtra) : null;

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
                                'price' => $finalVariantPrice,
                                'compare_at_price' => $finalCompareAt,
                                'cost_price' => (int) round($finalVariantPrice * 0.45),
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
                        StockMovement::firstOrCreate(
                            [
                                'inventory_item_id' => $inv->id,
                                'reference_note' => 'Storefront Launch Initial Stock',
                            ],
                            [
                                'type' => \App\Enums\StockMovementType::Inbound,
                                'quantity_change' => $stockQty,
                                'on_hand_before' => 0,
                                'on_hand_after' => $stockQty,
                                'reserved_before' => 0,
                                'reserved_after' => 0,
                                'created_at' => now(),
                            ]
                        );
                    }
                }
            }
        });
    }
}

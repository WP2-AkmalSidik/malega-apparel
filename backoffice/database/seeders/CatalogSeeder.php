<?php

namespace Database\Seeders;

use App\Actions\Catalog\CreateCategoryAction;
use App\Actions\Catalog\CreateProductAction;
use App\Enums\ProductStatus;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(CreateCategoryAction $createCategory, CreateProductAction $createProduct): void
    {
        // 1. Categories
        $catTops = $createCategory->execute([
            'name' => 'Kemeja & Atasan',
            'slug' => 'kemeja-atasan',
            'description' => 'Koleksi kemeja formal, casual, dan koko modern dengan bahan premium.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $catTshirts = $createCategory->execute([
            'name' => 'T-Shirts & Polos',
            'slug' => 't-shirts-polos',
            'description' => 'Kaos katun combed 24s/30s heavy weight dan polo shirt elegan.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $catBottoms = $createCategory->execute([
            'name' => 'Celana & Chino',
            'slug' => 'celana-chino',
            'description' => 'Celana panjang chino tailored, ankle pants, dan formal trousers.',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $catOuter = $createCategory->execute([
            'name' => 'Outerwear & Jaket',
            'slug' => 'outerwear-jaket',
            'description' => 'Blazer santai, overcoat, bomber jacket, dan cardigan rajut.',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $catAcc = $createCategory->execute([
            'name' => 'Aksesoris & Kulit',
            'slug' => 'aksesoris-kulit',
            'description' => 'Ikat pinggang kulit sapi asli, dompet, dan dasi sutra.',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        // 2. Sample Products with Variants
        $createProduct->execute([
            'category_id' => $catTops->id,
            'name' => 'Malega Oxford Signature Shirt — Obsidian Navy',
            'slug' => 'oxford-signature-navy',
            'description' => 'Kemeja katun oxford 100% premium dengan sentuhan warna obsidian navy beraksen kancing mutiara.',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'MLG-OXF-NVY-S', 'title' => 'Ukuran S', 'price' => 349000, 'compare_at_price' => 399000, 'cost_price' => 175000, 'weight_grams' => 280, 'is_active' => true],
                ['sku' => 'MLG-OXF-NVY-M', 'title' => 'Ukuran M', 'price' => 349000, 'compare_at_price' => 399000, 'cost_price' => 175000, 'weight_grams' => 290, 'is_active' => true],
                ['sku' => 'MLG-OXF-NVY-L', 'title' => 'Ukuran L', 'price' => 349000, 'compare_at_price' => 399000, 'cost_price' => 175000, 'weight_grams' => 300, 'is_active' => true],
                ['sku' => 'MLG-OXF-NVY-XL', 'title' => 'Ukuran XL', 'price' => 369000, 'compare_at_price' => 419000, 'cost_price' => 185000, 'weight_grams' => 320, 'is_active' => true],
            ],
        ]);

        $createProduct->execute([
            'category_id' => $catBottoms->id,
            'name' => 'Tailored Slim Chino Trousers — Charcoal Khaki',
            'slug' => 'tailored-chino-charcoal',
            'description' => 'Celana chino katun stretch dengan pola slim-fit modern dan jahitan presisi.',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'MLG-CHN-KHK-29', 'title' => 'Size 29', 'price' => 429000, 'compare_at_price' => null, 'cost_price' => 210000, 'weight_grams' => 450, 'is_active' => true],
                ['sku' => 'MLG-CHN-KHK-30', 'title' => 'Size 30', 'price' => 429000, 'compare_at_price' => null, 'cost_price' => 210000, 'weight_grams' => 460, 'is_active' => true],
                ['sku' => 'MLG-CHN-KHK-32', 'title' => 'Size 32', 'price' => 429000, 'compare_at_price' => null, 'cost_price' => 210000, 'weight_grams' => 480, 'is_active' => true],
                ['sku' => 'MLG-CHN-KHK-34', 'title' => 'Size 34', 'price' => 449000, 'compare_at_price' => null, 'cost_price' => 220000, 'weight_grams' => 500, 'is_active' => true],
            ],
        ]);

        $createProduct->execute([
            'category_id' => $catAcc->id,
            'name' => 'Genuine Leather Belt Classic — Vintage Brass Buckle',
            'slug' => 'leather-belt-vintage-brass',
            'description' => 'Ikat pinggang kulit sapi full-grain dengan kepala sabuk kuningan antik warna emas gold.',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'MLG-BLT-BRN-STD', 'title' => 'Dark Brown / 115cm', 'price' => 219000, 'compare_at_price' => 269000, 'cost_price' => 110000, 'weight_grams' => 200, 'is_active' => true],
                ['sku' => 'MLG-BLT-BLK-STD', 'title' => 'Obsidian Black / 115cm', 'price' => 219000, 'compare_at_price' => 269000, 'cost_price' => 110000, 'weight_grams' => 200, 'is_active' => true],
            ],
        ]);

        $createProduct->execute([
            'category_id' => $catOuter->id,
            'name' => 'Malega Cashmere Wool Blend Overcoat',
            'slug' => 'cashmere-wool-overcoat',
            'description' => 'Mantel wol premium dengan potongan tailored mewah, cocok untuk acara formal atau cuaca dingin.',
            'status' => ProductStatus::Active,
            'variants' => [
                ['sku' => 'MLG-OVC-BLK-M', 'title' => 'Black / M', 'price' => 1200000, 'compare_at_price' => 1450000, 'cost_price' => 600000, 'weight_grams' => 850, 'is_active' => true],
                ['sku' => 'MLG-OVC-BLK-L', 'title' => 'Black / L', 'price' => 1200000, 'compare_at_price' => 1450000, 'cost_price' => 600000, 'weight_grams' => 900, 'is_active' => true],
            ],
        ]);
    }
}

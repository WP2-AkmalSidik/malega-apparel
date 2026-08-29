<?php

namespace App\Actions\Catalog;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateProductAction
{
    /**
     * Create a product with its variants in an atomic transaction.
     *
     * @param array{
     *     category_id: int,
     *     name: string,
     *     slug?: string|null,
     *     description?: string|null,
     *     status?: ProductStatus|string,
     *     featured_image?: string|null,
     *     collection_ids?: array<int>,
     *     variants: array<int, array{
     *         sku: string,
     *         title: string,
     *         price: int,
     *         compare_at_price?: int|null,
     *         cost_price?: int|null,
     *         weight_grams?: int,
     *         is_active?: bool
     *     }>
     * } $data
     *
     * @throws ValidationException
     */
    public function execute(array $data): Product
    {
        if (empty($data['variants'])) {
            throw ValidationException::withMessages([
                'variants' => 'Produk wajib memiliki minimal satu varian (SKU).',
            ]);
        }

        // Validate SKU uniqueness within the incoming payload
        $skus = array_column($data['variants'], 'sku');
        if (count($skus) !== count(array_unique($skus))) {
            throw ValidationException::withMessages([
                'variants' => 'Terdapat duplikasi kode SKU pada varian yang dimasukkan.',
            ]);
        }

        // Check SKU uniqueness in the database
        $existing = ProductVariant::whereIn('sku', $skus)->pluck('sku')->first();
        if ($existing) {
            throw ValidationException::withMessages([
                'variants' => "Kode SKU '{$existing}' sudah terdaftar di sistem. Gunakan SKU lain.",
            ]);
        }

        return DB::transaction(function () use ($data) {
            $slug = ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }

            $product = Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? ProductStatus::Active,
                'featured_image' => $data['featured_image'] ?? null,
            ]);

            if (! empty($data['collection_ids'])) {
                $product->collections()->sync($data['collection_ids']);
            }

            foreach ($data['variants'] as $variantData) {
                $product->variants()->create([
                    'sku' => strtoupper(trim($variantData['sku'])),
                    'title' => $variantData['title'],
                    'price' => (int) $variantData['price'],
                    'compare_at_price' => ! empty($variantData['compare_at_price']) ? (int) $variantData['compare_at_price'] : null,
                    'cost_price' => ! empty($variantData['cost_price']) ? (int) $variantData['cost_price'] : null,
                    'weight_grams' => ! empty($variantData['weight_grams']) ? (int) $variantData['weight_grams'] : 250,
                    'is_active' => $variantData['is_active'] ?? true,
                ]);
            }

            return $product->load(['category', 'collections', 'variants', 'images']);
        });
    }
}

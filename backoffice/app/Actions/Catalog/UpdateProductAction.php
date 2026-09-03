<?php

namespace App\Actions\Catalog;

use App\Enums\ProductStatus;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateProductAction
{
    /**
     * Update a product and sync its variants and inventory items.
     *
     * @param array{
     *     category_id?: int,
     *     name?: string,
     *     slug?: string|null,
     *     description?: string|null,
     *     status?: ProductStatus|string,
     *     featured_image?: string|null,
     *     collection_ids?: array<int>,
     *     variants?: array<int, array{
     *         id?: int|null,
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
    public function execute(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (isset($data['name']) && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            } elseif (isset($data['slug'])) {
                $data['slug'] = Str::slug($data['slug']);
            }

            if (isset($data['slug']) && $data['slug'] !== $product->slug) {
                $slug = $data['slug'];
                $originalSlug = $slug;
                $count = 1;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }
                $data['slug'] = $slug;
            }

            $productData = array_diff_key($data, array_flip(['collection_ids', 'variants']));
            $product->update($productData);

            if (isset($data['collection_ids'])) {
                $product->collections()->sync($data['collection_ids']);
            }

            if (isset($data['variants'])) {
                $keptVariantIds = [];

                foreach ($data['variants'] as $variantData) {
                    $sku = strtoupper(trim($variantData['sku']));

                    // Ensure SKU uniqueness outside of this product's current variants
                    $existingVariant = ProductVariant::where('sku', $sku)
                        ->where('product_id', '!=', $product->id)
                        ->first();

                    if ($existingVariant) {
                        throw ValidationException::withMessages([
                            'variants' => "Kode SKU '{$sku}' sudah digunakan oleh produk lain.",
                        ]);
                    }

                    if (! empty($variantData['id'])) {
                        $variant = ProductVariant::where('product_id', $product->id)->find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $sku,
                                'title' => $variantData['title'] ?? 'Standard Variant',
                                'color_name' => ! empty($variantData['color_name']) ? trim($variantData['color_name']) : null,
                                'color_hex' => ! empty($variantData['color_hex']) ? trim($variantData['color_hex']) : null,
                                'size' => ! empty($variantData['size']) ? trim($variantData['size']) : null,
                                'image_url' => ! empty($variantData['image_url']) ? trim($variantData['image_url']) : null,
                                'price' => (int) $variantData['price'],
                                'compare_at_price' => ! empty($variantData['compare_at_price']) ? (int) $variantData['compare_at_price'] : null,
                                'cost_price' => ! empty($variantData['cost_price']) ? (int) $variantData['cost_price'] : null,
                                'weight_grams' => ! empty($variantData['weight_grams']) ? (int) $variantData['weight_grams'] : 250,
                                'is_active' => $variantData['is_active'] ?? true,
                            ]);
                            $keptVariantIds[] = $variant->id;

                            // Ensure inventory item exists
                            InventoryItem::firstOrCreate(
                                ['variant_id' => $variant->id],
                                ['on_hand' => 0, 'reserved' => 0, 'low_stock_threshold' => 5]
                            );
                        }
                    } else {
                        $newVariant = $product->variants()->create([
                            'sku' => $sku,
                            'title' => $variantData['title'] ?? 'Standard Variant',
                            'color_name' => ! empty($variantData['color_name']) ? trim($variantData['color_name']) : null,
                            'color_hex' => ! empty($variantData['color_hex']) ? trim($variantData['color_hex']) : null,
                            'size' => ! empty($variantData['size']) ? trim($variantData['size']) : null,
                            'image_url' => ! empty($variantData['image_url']) ? trim($variantData['image_url']) : null,
                            'price' => (int) $variantData['price'],
                            'compare_at_price' => ! empty($variantData['compare_at_price']) ? (int) $variantData['compare_at_price'] : null,
                            'cost_price' => ! empty($variantData['cost_price']) ? (int) $variantData['cost_price'] : null,
                            'weight_grams' => ! empty($variantData['weight_grams']) ? (int) $variantData['weight_grams'] : 250,
                            'is_active' => $variantData['is_active'] ?? true,
                        ]);
                        $keptVariantIds[] = $newVariant->id;

                        // Initialize inventory item for new variant
                        InventoryItem::firstOrCreate(
                            ['variant_id' => $newVariant->id],
                            ['on_hand' => 0, 'reserved' => 0, 'low_stock_threshold' => 5]
                        );
                    }
                }

                // Delete variants removed from the list
                if (! empty($keptVariantIds)) {
                    $product->variants()->whereNotIn('id', $keptVariantIds)->delete();
                }
            }

            return $product->fresh(['category', 'collections', 'variants.inventoryItem', 'images']);
        });
    }
}

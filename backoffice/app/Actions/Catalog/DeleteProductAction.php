<?php

namespace App\Actions\Catalog;

use App\Models\Product;

class DeleteProductAction
{
    /**
     * Delete a product and its associated variants and images.
     */
    public function execute(Product $product): bool
    {
        return (bool) $product->delete();
    }
}

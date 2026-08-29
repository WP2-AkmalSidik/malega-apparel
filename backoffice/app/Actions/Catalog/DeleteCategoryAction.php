<?php

namespace App\Actions\Catalog;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class DeleteCategoryAction
{
    /**
     * Delete a category, ensuring no products are linked.
     *
     * @throws ValidationException
     */
    public function execute(Category $category): bool
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Kategori "'.$category->name.'" tidak dapat dihapus karena masih memiliki produk terkait.',
            ]);
        }

        return (bool) $category->delete();
    }
}

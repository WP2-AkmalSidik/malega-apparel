<?php

namespace App\Actions\Catalog;

use App\Models\Category;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    /**
     * Update an existing category.
     *
     * @param  array{name?: string, slug?: string|null, description?: string|null, sort_order?: int, is_active?: bool}  $data
     */
    public function execute(Category $category, array $data): Category
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } elseif (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if (isset($data['slug']) && $data['slug'] !== $category->slug) {
            $slug = $data['slug'];
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $data['slug'] = $slug;
        }

        $category->update($data);

        return $category->fresh();
    }
}

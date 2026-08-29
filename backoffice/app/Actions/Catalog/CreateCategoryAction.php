<?php

namespace App\Actions\Catalog;

use App\Models\Category;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    /**
     * Create a new category.
     *
     * @param  array{name: string, slug?: string|null, description?: string|null, sort_order?: int, is_active?: bool}  $data
     */
    public function execute(array $data): Category
    {
        $slug = ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        // Ensure unique slug
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return Category::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
}

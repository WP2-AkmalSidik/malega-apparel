<?php

namespace App\Http\Resources\V1;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Collection
 */
class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cover = $this->cover_image ? (str_starts_with($this->cover_image, 'http') ? $this->cover_image : asset('storage/'.$this->cover_image)) : null;
        $banner = $this->banner_image ? (str_starts_with($this->banner_image, 'http') ? $this->banner_image : asset('storage/'.$this->banner_image)) : ($this->banner_path ? asset('storage/'.$this->banner_path) : null);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->name,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'season' => $this->season,
            'release_year' => $this->release_year ?: '2026',
            'badge' => $this->badge,
            'description' => $this->description,
            'storytelling' => $this->storytelling,
            'featured_material' => $this->featured_material,
            'gsm_weight' => $this->gsm_weight,
            'palette' => $this->palette ?: [],
            'tags' => $this->tags ?: [],
            'cover_image' => $cover,
            'banner_image' => $banner,
            'banner_url' => $banner,
            'products_count' => $this->products_count ?? $this->products()->count(),
        ];
    }
}

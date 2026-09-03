<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'subtitle',
        'slug',
        'description',
        'season',
        'release_year',
        'badge',
        'cover_image',
        'banner_image',
        'banner_path',
        'featured_material',
        'gsm_weight',
        'storytelling',
        'palette',
        'tags',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'gsm_weight' => 'integer',
            'palette' => 'array',
            'tags' => 'array',
        ];
    }

    /**
     * Products belonging to this collection.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Scope a query to only include active collections.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}

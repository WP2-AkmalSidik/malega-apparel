<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FabricSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'gramasi',
        'material',
        'fit_cutting',
        'collar_hood',
        'care_instructions',
        'extra_specs',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extra_specs' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'fabric_spec_id');
    }

    /**
     * Convert to structured associative specifications array for Product->specifications.
     *
     * @return array<string, string>
     */
    public function toProductSpecifications(): array
    {
        $specs = [
            'Brand' => $this->brand ?: 'Malega Apparel',
            'Gramasi' => $this->gramasi,
            'Material' => $this->material,
            'Cutting' => $this->fit_cutting,
            'Kerah' => $this->collar_hood,
            'Perawatan' => $this->care_instructions,
        ];

        if (! empty($this->extra_specs)) {
            foreach ($this->extra_specs as $key => $value) {
                if (! empty($key) && ! empty($value)) {
                    $specs[$key] = $value;
                }
            }
        }

        return array_filter($specs, fn ($v) => ! is_null($v) && $v !== '');
    }
}

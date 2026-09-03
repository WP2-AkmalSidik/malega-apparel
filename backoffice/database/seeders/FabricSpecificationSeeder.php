<?php

namespace Database\Seeders;

use App\Models\FabricSpecification;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FabricSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specs = [
            [
                'name' => 'Heavyweight Cotton 300 GSM (T-Shirt & Tops)',
                'brand' => 'Malega Apparel',
                'gramasi' => '300 GSM Heavyweight',
                'material' => '100% Pure Combed Cotton',
                'fit_cutting' => 'Boxy Fit / Drop Shoulder',
                'collar_hood' => '3.5cm Reinforced Rib Collar',
                'care_instructions' => 'Cuci dengan air dingin, setrika temperatur sedang',
                'is_active' => true,
            ],
            [
                'name' => 'Heavy French Terry 380 GSM (Hoodie & Outerwear)',
                'brand' => 'Malega Apparel',
                'gramasi' => '380 GSM Heavy French Terry',
                'material' => '100% Cotton French Terry',
                'fit_cutting' => 'Structured Boxy Cut',
                'collar_hood' => 'Double Layer Seamless Hood',
                'care_instructions' => 'Cuci dengan air dingin, jangan gunakan pemutih',
                'is_active' => true,
            ],
            [
                'name' => 'Raw Selvedge Denim 14oz (Jeans & Denim)',
                'brand' => 'Malega Apparel',
                'gramasi' => '14oz Heavyweight Selvedge',
                'material' => '100% Cotton Ring Spun',
                'fit_cutting' => 'Relaxed Straight Fit',
                'collar_hood' => 'YKK Heavy Duty Brass Rivets & Hardware',
                'care_instructions' => 'Cuci setelah 6 bulan pemakaian untuk natural fading',
                'is_active' => true,
            ],
            [
                'name' => 'Heavy Cotton Twill 280 GSM (Cargo & Pants)',
                'brand' => 'Malega Apparel',
                'gramasi' => '280 GSM Heavy Cotton Twill',
                'material' => '100% Cotton Twill',
                'fit_cutting' => 'Wide Leg Loose Cut',
                'collar_hood' => 'Reinforced Pocket & Double Stitching',
                'care_instructions' => 'Cuci dengan mesin putaran rendah',
                'is_active' => true,
            ],
        ];

        foreach ($specs as $specData) {
            $spec = FabricSpecification::firstOrCreate(
                ['name' => $specData['name']],
                $specData
            );

            // Connect to relevant existing products based on name keywords
            if (str_contains($spec->name, 'T-Shirt')) {
                Product::where('name', 'like', '%Tee%')->orWhere('name', 'like', '%T-Shirt%')->update([
                    'fabric_spec_id' => $spec->id,
                    'specifications' => $spec->toProductSpecifications(),
                ]);
            } elseif (str_contains($spec->name, 'Hoodie')) {
                Product::where('name', 'like', '%Hoodie%')->orWhere('name', 'like', '%Jacket%')->orWhere('name', 'like', '%Outer%')->update([
                    'fabric_spec_id' => $spec->id,
                    'specifications' => $spec->toProductSpecifications(),
                ]);
            } elseif (str_contains($spec->name, 'Denim')) {
                Product::where('name', 'like', '%Denim%')->orWhere('name', 'like', '%Jeans%')->update([
                    'fabric_spec_id' => $spec->id,
                    'specifications' => $spec->toProductSpecifications(),
                ]);
            } elseif (str_contains($spec->name, 'Cargo')) {
                Product::where('name', 'like', '%Cargo%')->orWhere('name', 'like', '%Pants%')->update([
                    'fabric_spec_id' => $spec->id,
                    'specifications' => $spec->toProductSpecifications(),
                ]);
            }
        }
    }
}

<?php

namespace Darvis\MantaProduct\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Darvis\MantaProduct\Models\{Product, Attribute, AttributeValue, ProductVariant, ProductVariantValue, ProductAttribute, Room, PriceRule, OpeningHour};
use Darvis\MantaProduct\Services\VariantMatrixService;

class MantaProductsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Attributes
        $color = Attribute::firstOrCreate(['code' => 'color'], ['name' => 'Kleur', 'type' => 'color_swatches', 'sort' => 0]);
        $size  = Attribute::firstOrCreate(['code' => 'size'],  ['name' => 'Maat', 'type' => 'select', 'sort' => 1]);

        $reds = [
            ['value' => 'Rood', 'code' => 'red', 'hex' => '#ff0000', 'sort' => 0],
            ['value' => 'Blauw', 'code' => 'blue', 'hex' => '#0000ff', 'sort' => 1],
        ];
        foreach ($reds as $rd) {
            AttributeValue::firstOrCreate(['attribute_id' => $color->id, 'code' => $rd['code']], $rd);
        }

        foreach (['s', 'm', 'l'] as $i => $sz) {
            AttributeValue::firstOrCreate(['attribute_id' => $size->id, 'code' => $sz], ['value' => strtoupper($sz), 'code' => $sz, 'sort' => $i]);
        }

        // Product
        $product = Product::firstOrCreate(['slug' => 'demo-profiel'], [
            'active' => true,
            'title' => 'Demo profiel',
            'product_type' => 'both',
            'time_unit' => 'minute',
            'block_size' => 30,
            'capacity' => 4,
            'unit_type' => 'meter',
            'price_per_unit' => 12.50,
            'tax_rate' => 21.00,
            'calc_mode' => 'direct_length',
            'unit_step' => 0.1,
            'length_mm' => 2500,
            'width_mm' => 50,
            'height_mm' => 20,
            'dimension_unit' => 'mm',
        ]);

        // Attach attributes to product (order matters)
        $product->attributes()->sync([
            $color->id => ['is_required' => true, 'sort' => 0],
            $size->id  => ['is_required' => true, 'sort' => 1],
        ]);

        // Generate variants
        $service = new VariantMatrixService();
        $service->generate($product, [
            'color' => ['red', 'blue'],
            'size'  => ['s', 'm', 'l'],
        ]);

        // Price rule
        PriceRule::firstOrCreate(
            ['product_id' => $product->id, 'name' => 'Basis per periode'],
            ['rule_type' => 'per_period', 'amount' => 5.00, 'tax_rate' => 21.00, 'priority' => 100, 'combinable' => true]
        );

        // Opening hours for product (Mon-Fri 9-17)
        foreach (range(1, 5) as $wd) {
            OpeningHour::firstOrCreate([
                'owner_type' => 'products',
                'owner_id' => $product->id,
                'weekday' => $wd,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }

        // Room example
        Room::firstOrCreate(['slug' => 'zaal-a'], ['name' => 'Zaal A', 'capacity' => 2, 'active' => true]);
    }
}

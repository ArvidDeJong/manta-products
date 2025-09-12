<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\Product;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $title = 'Test Product ' . rand(1, 1000);
        return [
            'active'         => true,
            'title'          => $title,
            'slug'           => Str::slug($title) . '-' . rand(100,999),
            'product_type'   => 'sellable',
            'time_unit'      => 'day',
            'block_size'     => 30,
            'capacity'       => 4,
            'unit_type'      => 'm2',
            'price_per_unit' => 10.50,
            'tax_rate'       => 21.00,
            'unit_step'      => 0.01,
            'calc_mode'      => 'dimensions_2d',
            'length_mm'      => 1000,
            'width_mm'       => 500,
            'height_mm'      => 100,
            'dimension_unit' => 'mm',
            'meta'           => [],
        ];
    }
}

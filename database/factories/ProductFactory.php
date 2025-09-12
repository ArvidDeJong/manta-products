<?php

namespace Manta\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Manta\Products\Models\Product;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $title = $this->faker->words(3, true);
        return [
            'active'         => true,
            'title'          => ucfirst($title),
            'slug'           => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(100,999),
            'product_type'   => $this->faker->randomElement(['sellable','bookable','both']),
            'time_unit'      => $this->faker->randomElement(['minute','day']),
            'block_size'     => $this->faker->randomElement([15,30,60,1]),
            'capacity'       => $this->faker->numberBetween(1, 8),
            'unit_type'      => $this->faker->randomElement(['piece','meter','m2','m3']),
            'price_per_unit' => $this->faker->randomFloat(2, 2, 99),
            'tax_rate'       => 21.00,
            'unit_step'      => 0.01,
            'calc_mode'      => $this->faker->randomElement(['direct_length','dimensions_2d','dimensions_3d']),
            'length_mm'      => $this->faker->numberBetween(500, 3000),
            'width_mm'       => $this->faker->numberBetween(100, 1500),
            'height_mm'      => $this->faker->numberBetween(10, 800),
            'dimension_unit' => 'mm',
            'meta'           => [],
        ];
    }
}

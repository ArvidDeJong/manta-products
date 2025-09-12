<?php

namespace Manta\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Manta\Products\Models\ProductVariant;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'sku'        => 'SKU-' . $this->faker->unique()->bothify('??##??'),
            'title'      => $this->faker->words(2, true),
            'active'     => true,
            'stock_qty'  => $this->faker->numberBetween(0, 50),
            'meta'       => [],
        ];
    }
}

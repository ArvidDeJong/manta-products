<?php

namespace Manta\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Manta\Products\Models\AttributeValue;

class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    public function definition()
    {
        return [
            'value' => $this->faker->colorName(),
            'code'  => $this->faker->unique()->safeColorName(),
            'hex'   => $this->faker->hexColor(),
            'sort'  => 0,
        ];
    }
}

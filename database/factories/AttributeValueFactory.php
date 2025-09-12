<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\AttributeValue;

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

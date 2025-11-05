<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\Attribute;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition()
    {
        $name = $this->faker->unique()->randomElement(['Kleur', 'Maat', 'Materiaal', 'Uitvoering']);
        return [
            'name' => $name,
            'code' => strtolower($name),
            'type' => $name === 'Kleur' ? 'color_swatches' : 'select',
            'sort' => 0,
            'config' => [],
        ];
    }
}

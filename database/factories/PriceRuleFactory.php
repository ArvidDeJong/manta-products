<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\PriceRule;

class PriceRuleFactory extends Factory
{
    protected $model = PriceRule::class;

    public function definition()
    {
        return [
            'name'        => 'Basis',
            'rule_type'   => 'per_period',
            'amount'      => $this->faker->randomFloat(2, 5, 50),
            'tax_rate'    => 21.00,
            'priority'    => 100,
            'combinable'  => true,
            'metadata'    => [],
        ];
    }
}

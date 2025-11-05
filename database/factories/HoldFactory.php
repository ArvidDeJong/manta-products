<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\Hold;
use Illuminate\Support\Str;

class HoldFactory extends Factory
{
    protected $model = Hold::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('now', '+5 days');
        $end   = (clone $start);
        $end->modify('+30 minutes');

        return [
            'starts_at'  => $start,
            'ends_at'    => $end,
            'quantity'   => 1,
            'token'      => Str::uuid()->toString(),
            'expires_at' => $this->faker->dateTimeBetween('+5 minutes', '+30 minutes'),
        ];
    }
}

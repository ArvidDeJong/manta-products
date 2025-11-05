<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\ReservationItem;

class ReservationItemFactory extends Factory
{
    protected $model = ReservationItem::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $end   = (clone $start);
        $end->modify('+1 hour');

        return [
            'blocks'           => 1,
            'persons'          => 1,
            'quantity'         => 1,
            'unit_price_excl'  => $this->faker->randomFloat(2, 5, 30),
            'tax_rate'         => 21.00,
            'line_total_excl'  => 0,
            'line_total_tax'   => 0,
            'line_total_incl'  => 0,
            'starts_at'        => $start,
            'ends_at'          => $end,
            'price_breakdown'  => [],
        ];
    }
}

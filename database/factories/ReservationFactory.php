<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\Reservation;
use Illuminate\Support\Str;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $end   = (clone $start);
        $end->modify('+2 hours');

        return [
            'status'     => 'confirmed',
            'currency'   => 'EUR',
            'starts_at'  => $start,
            'ends_at'    => $end,
            'total_excl' => 0,
            'total_tax'  => 0,
            'total_incl' => 0,
            'channel'    => 'web',
            'reference'  => strtoupper(Str::random(8)),
            'meta'       => [],
        ];
    }
}

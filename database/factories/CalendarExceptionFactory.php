<?php

namespace Manta\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Manta\Products\Models\CalendarException;

class CalendarExceptionFactory extends Factory
{
    protected $model = CalendarException::class;

    public function definition()
    {
        return [
            'date'      => $this->faker->dateTimeBetween('+1 days', '+20 days'),
            'is_closed' => true,
            'note'      => 'Feestdag',
        ];
    }
}

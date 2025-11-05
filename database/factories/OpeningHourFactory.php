<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\OpeningHour;

class OpeningHourFactory extends Factory
{
    protected $model = OpeningHour::class;

    public function definition()
    {
        return [
            'owner_type' => 'products',
            'weekday'    => $this->faker->numberBetween(1, 7),
            'start_time' => '09:00:00',
            'end_time'   => '17:00:00',
        ];
    }
}

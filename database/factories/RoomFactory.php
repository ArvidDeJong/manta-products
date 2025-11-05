<?php

namespace Darvis\MantaProduct\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Darvis\MantaProduct\Models\Room;
use Illuminate\Support\Str;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition()
    {
        $name = 'Room ' . $this->faker->unique()->lexify('???');
        return [
            'active'   => true,
            'capacity' => $this->faker->numberBetween(1, 6),
            'name'     => $name,
            'slug'     => Str::slug($name),
            'meta'     => [],
        ];
    }
}

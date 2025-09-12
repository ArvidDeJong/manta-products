<?php

namespace Manta\Products\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Manta\Products\Models\Resource;

class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition()
    {
        return [
            'active'   => true,
            'capacity' => $this->faker->numberBetween(1, 10),
            'title'    => 'Resource ' . $this->faker->unique()->bothify('##'),
            'location' => $this->faker->city(),
            'meta'     => [],
        ];
    }
}

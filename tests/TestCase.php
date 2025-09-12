<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Darvis\MantaProduct\ServiceProvider;
use Livewire\LivewireServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set up manta-products config
        config()->set('manta-products.default_tax_rate', 21.00);
        config()->set('manta-products.default_unit_step', 0.01);
        config()->set('manta-products.default_rounding_mode', 'round');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}

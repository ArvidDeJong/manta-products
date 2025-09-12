<?php

namespace Darvis\MantaProduct;

use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/manta-products.php', 'manta-products');
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/manta-products.php' => config_path('manta-products.php'),
        ], 'manta-products-config');

        // Load migrations directly from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Views (for demo)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'manta-products');


        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}

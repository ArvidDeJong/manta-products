<?php

namespace Manta\Products;

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

        // Routes (demo - optional)
        if (config('manta-products.enable_demo', false)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/manta-products-demo.php');
        }

        // Publish demo stubs (controllers/livewire could be copied into app if desired)
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/manta-products'),
            __DIR__ . '/../routes/manta-products-demo.php' => base_path('routes/manta-products-demo.php'),
        ], 'manta-products-demo');
    }
}

<?php

namespace Darvis\MantaProduct;

use Darvis\MantaProduct\Console\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Livewire\Livewire;

class ServiceProvider extends BaseProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/manta-product.php', 'manta-product');
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/manta-product.php' => config_path('manta-product.php'),
        ], 'manta-product-config');

        // Publish settings
        $this->publishes([
            __DIR__ . '/../export/settings-product.php' => base_path('export/settings-product.php'),
        ], 'manta-product-settings');

        // Load migrations directly from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Views (for demo)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'manta-product');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Register Livewire components
        Livewire::component('products.product-list', \Darvis\MantaProduct\Livewire\Products\ProductList::class);
        Livewire::component('products.product-create', \Darvis\MantaProduct\Livewire\Products\ProductCreate::class);
        Livewire::component('products.product-update', \Darvis\MantaProduct\Livewire\Products\ProductUpdate::class);
        Livewire::component('products.product-upload', \Darvis\MantaProduct\Livewire\Products\ProductUpload::class);
        Livewire::component('products.product-read', \Darvis\MantaProduct\Livewire\Products\ProductRead::class);

        // Register Attribute Livewire components
        Livewire::component('attributes.attribute-list', \Darvis\MantaProduct\Livewire\Attributes\AttributeList::class);
        Livewire::component('attributes.attribute-create', \Darvis\MantaProduct\Livewire\Attributes\AttributeCreate::class);
        Livewire::component('attributes.attribute-update', \Darvis\MantaProduct\Livewire\Attributes\AttributeUpdate::class);
        Livewire::component('attributes.attribute-upload', \Darvis\MantaProduct\Livewire\Attributes\AttributeUpload::class);
        Livewire::component('attributes.attribute-read', \Darvis\MantaProduct\Livewire\Attributes\AttributeRead::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}

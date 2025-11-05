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
            __DIR__ . '/../export/settings-variant.php' => base_path('export/settings-variant.php'),
            __DIR__ . '/../export/settings-category.php' => base_path('export/settings-category.php'),
        ], 'manta-product-settings');

        // Load migrations directly from package
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Views (for demo)
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'manta-product');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Register Livewire components
        Livewire::component('darvis.manta-product.livewire.products.product-list', \Darvis\MantaProduct\Livewire\Products\ProductList::class);
        Livewire::component('darvis.manta-product.livewire.products.product-create', \Darvis\MantaProduct\Livewire\Products\ProductCreate::class);
        Livewire::component('darvis.manta-product.livewire.products.product-update', \Darvis\MantaProduct\Livewire\Products\ProductUpdate::class);
        Livewire::component('darvis.manta-product.livewire.products.product-upload', \Darvis\MantaProduct\Livewire\Products\ProductUpload::class);
        Livewire::component('darvis.manta-product.livewire.products.product-read', \Darvis\MantaProduct\Livewire\Products\ProductRead::class);

        // Register Attribute Livewire components
        Livewire::component('darvis.manta-product.livewire.attributes.attribute-list', \Darvis\MantaProduct\Livewire\Attributes\AttributeList::class);
        Livewire::component('darvis.manta-product.livewire.attributes.attribute-create', \Darvis\MantaProduct\Livewire\Attributes\AttributeCreate::class);
        Livewire::component('darvis.manta-product.livewire.attributes.attribute-update', \Darvis\MantaProduct\Livewire\Attributes\AttributeUpdate::class);
        Livewire::component('darvis.manta-product.livewire.attributes.attribute-upload', \Darvis\MantaProduct\Livewire\Attributes\AttributeUpload::class);
        Livewire::component('darvis.manta-product.livewire.attributes.attribute-read', \Darvis\MantaProduct\Livewire\Attributes\AttributeRead::class);

        // Register Variant Livewire components
        Livewire::component('darvis.manta-product.livewire.variants.variant-list', \Darvis\MantaProduct\Livewire\Variants\VariantList::class);
        Livewire::component('darvis.manta-product.livewire.variants.variant-create', \Darvis\MantaProduct\Livewire\Variants\VariantCreate::class);
        Livewire::component('darvis.manta-product.livewire.variants.variant-update', \Darvis\MantaProduct\Livewire\Variants\VariantUpdate::class);
        Livewire::component('darvis.manta-product.livewire.variants.variant-upload', \Darvis\MantaProduct\Livewire\Variants\VariantUpload::class);
        Livewire::component('darvis.manta-product.livewire.variants.variant-read', \Darvis\MantaProduct\Livewire\Variants\VariantRead::class);

        // Register Category Livewire components
        Livewire::component('darvis.manta-product.livewire.categories.category-list', \Darvis\MantaProduct\Livewire\Categories\CategoryList::class);
        Livewire::component('darvis.manta-product.livewire.categories.category-create', \Darvis\MantaProduct\Livewire\Categories\CategoryCreate::class);
        Livewire::component('darvis.manta-product.livewire.categories.category-update', \Darvis\MantaProduct\Livewire\Categories\CategoryUpdate::class);
        Livewire::component('darvis.manta-product.livewire.categories.category-upload', \Darvis\MantaProduct\Livewire\Categories\CategoryUpload::class);
        Livewire::component('darvis.manta-product.livewire.categories.category-read', \Darvis\MantaProduct\Livewire\Categories\CategoryRead::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}

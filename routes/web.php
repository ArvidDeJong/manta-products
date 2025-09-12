<?php

use Illuminate\Support\Facades\Route;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Livewire\Products\ProductList;
use Darvis\MantaProduct\Livewire\Demo\Index;
use Darvis\MantaProduct\Livewire\Products\ProductCreate;
use Darvis\MantaProduct\Livewire\Products\ProductUpdate;
use Darvis\MantaProduct\Livewire\Demo\SlotList;
use Darvis\MantaProduct\Services\SlotGeneratorService;
use Carbon\Carbon;

Route::middleware('web')->prefix('shop')->group(function () {
    Route::get('/', Index::class)->name('manta-products.demo.index');
    Route::get('/products', ProductList::class)->name('manta-products.demo.products');
    Route::get('/products/create', ProductCreate::class)->name('manta-products.demo.products.create');
    Route::get('/products/{product}/update', ProductUpdate::class)->name('manta-products.demo.products.update');

    Route::get('/slots/{product}', SlotList::class)->name('manta-products.demo.slots');
});

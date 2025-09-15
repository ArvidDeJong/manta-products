<?php

use Illuminate\Support\Facades\Route;
use Darvis\MantaProduct\Livewire\Products\ProductList;
use Darvis\MantaProduct\Livewire\Products\ProductCreate;
use Darvis\MantaProduct\Livewire\Products\ProductUpdate;

Route::middleware(['web', 'auth:staff'])->prefix('cms/producten')
    ->name('products.')
    ->group(function () {
        Route::get('/', ProductList::class)->name('list');
        Route::get('/create', ProductCreate::class)->name('create');
        Route::get('/{product}/update', ProductUpdate::class)->name('update');
    });

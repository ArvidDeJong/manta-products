<?php

use Darvis\MantaProduct\Livewire\Attributes\AttributeList;
use Darvis\MantaProduct\Livewire\Attributes\AttributeCreate;
use Darvis\MantaProduct\Livewire\Attributes\AttributeUpdate;
use Darvis\MantaProduct\Livewire\Attributes\AttributeRead;
use Darvis\MantaProduct\Livewire\Attributes\AttributeUpload;
use Illuminate\Support\Facades\Route;
use Darvis\MantaProduct\Livewire\Products\ProductList;
use Darvis\MantaProduct\Livewire\Products\ProductCreate;
use Darvis\MantaProduct\Livewire\Products\ProductUpdate;
use Darvis\MantaProduct\Livewire\Products\ProductUpload;
use Darvis\MantaProduct\Livewire\Products\ProductRead;

Route::middleware(['web', 'auth:staff'])->prefix('cms/producten')
    ->name('product.')
    ->group(function () {
        Route::get('/', ProductList::class)->name('list');
        Route::get('/create', ProductCreate::class)->name('create');
        Route::get('/{product}/update', ProductUpdate::class)->name('update');
        Route::get('/{product}/read', ProductRead::class)->name('read');
        Route::get('/{product}/upload', ProductUpload::class)->name('upload');
    });

Route::middleware(['web', 'auth:staff'])->prefix('cms/attributes')
    ->name('attribute.')
    ->group(function () {
        Route::get('/', AttributeList::class)->name('list');
        Route::get('/create', AttributeCreate::class)->name('create');
        Route::get('/{attribute}/update', AttributeUpdate::class)->name('update');
        Route::get('/{attribute}/read', AttributeRead::class)->name('read');
        Route::get('/{attribute}/upload', AttributeUpload::class)->name('upload');
    });

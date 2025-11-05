<?php

use Darvis\MantaProduct\Livewire\Attributes\AttributeList;
use Darvis\MantaProduct\Livewire\Attributes\AttributeCreate;
use Darvis\MantaProduct\Livewire\Attributes\AttributeUpdate;
use Darvis\MantaProduct\Livewire\Attributes\AttributeRead;
use Darvis\MantaProduct\Livewire\Attributes\AttributeUpload;
use Darvis\MantaProduct\Livewire\Categories\CategoryList;
use Darvis\MantaProduct\Livewire\Categories\CategoryCreate;
use Darvis\MantaProduct\Livewire\Categories\CategoryUpdate;
use Darvis\MantaProduct\Livewire\Categories\CategoryRead;
use Darvis\MantaProduct\Livewire\Categories\CategoryUpload;
use Illuminate\Support\Facades\Route;
use Darvis\MantaProduct\Livewire\Products\ProductList;
use Darvis\MantaProduct\Livewire\Products\ProductCreate;
use Darvis\MantaProduct\Livewire\Products\ProductUpdate;
use Darvis\MantaProduct\Livewire\Products\ProductUpload;
use Darvis\MantaProduct\Livewire\Products\ProductRead;
use Darvis\MantaProduct\Livewire\Variants\VariantList;
use Darvis\MantaProduct\Livewire\Variants\VariantCreate;
use Darvis\MantaProduct\Livewire\Variants\VariantUpdate;
use Darvis\MantaProduct\Livewire\Variants\VariantRead;
use Darvis\MantaProduct\Livewire\Variants\VariantUpload;

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

Route::middleware(['web', 'auth:staff'])->prefix('cms/varianten')
    ->name('variant.')
    ->group(function () {
        Route::get('/', VariantList::class)->name('list');
        Route::get('/create', VariantCreate::class)->name('create');
        Route::get('/{variant}/update', VariantUpdate::class)->name('update');
        Route::get('/{variant}/read', VariantRead::class)->name('read');
        Route::get('/{variant}/upload', VariantUpload::class)->name('upload');
    });

Route::middleware(['web', 'auth:staff'])->prefix('cms/categorieen')
    ->name('category.')
    ->group(function () {
        Route::get('/', CategoryList::class)->name('list');
        Route::get('/create', CategoryCreate::class)->name('create');
        Route::get('/{category}/update', CategoryUpdate::class)->name('update');
        Route::get('/{category}/read', CategoryRead::class)->name('read');
        Route::get('/{category}/upload', CategoryUpload::class)->name('upload');
    });

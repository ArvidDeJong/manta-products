<?php

use Illuminate\Support\Facades\Route;
use Manta\Products\Models\Product;
use Manta\Products\Services\SlotGeneratorService;
use Carbon\Carbon;

Route::middleware('web')->prefix(config('manta-products.demo_prefix','manta-products-demo'))->group(function () {
    Route::get('/', function () {
        return view('manta-products::demo.index', [
            'products' => Product::query()->latest()->limit(10)->get(),
        ]);
    })->name('manta-products.demo.index');

    Route::get('/slots/{product}', function (Product $product) {
        $from = Carbon::now()->startOfDay();
        $to   = Carbon::now()->addDays(7)->endOfDay();
        $slots = app(SlotGeneratorService::class)->slots($product, $from, $to);
        return view('manta-products::demo.slots', compact('product','slots','from','to'));
    })->name('manta-products.demo.slots');
});

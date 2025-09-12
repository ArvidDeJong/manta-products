<?php

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Models\Resource;

beforeEach(function () {
    // Migraties worden automatisch geladen door TestCase
});

it('can create a product', function () {
    $product = Product::factory()->create([
        'title' => 'Test Product',
        'slug' => 'test-product',
        'product_type' => 'sellable',
        'price_per_unit' => 10.50,
    ]);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->title)->toBe('Test Product')
        ->and($product->slug)->toBe('test-product')
        ->and($product->product_type)->toBe('sellable')
        ->and($product->price_per_unit)->toBe('10.50');
});

it('has correct fillable attributes', function () {
    $product = new Product();
    
    $expectedFillable = [
        'active', 'block_size', 'capacity', 'calc_mode', 'dimension_unit',
        'height_mm', 'length_mm', 'max_order_qty', 'max_persons', 'meta',
        'min_order_qty', 'min_persons', 'price_per_unit', 'product_type',
        'rounding_mode', 'slug', 'tax_rate', 'time_unit', 'title',
        'unit_step', 'unit_type', 'wastage_pct', 'width_mm',
    ];

    expect($product->getFillable())->toBe($expectedFillable);
});

it('casts attributes correctly', function () {
    $product = Product::factory()->create([
        'active' => true,
        'capacity' => 5,
        'meta' => ['key' => 'value'],
        'price_per_unit' => 15.99,
        'tax_rate' => 21.00,
        'wastage_pct' => 5.50,
    ]);

    expect($product->active)->toBeBool()
        ->and($product->capacity)->toBeInt()
        ->and($product->meta)->toBeArray()
        ->and($product->meta)->toBe(['key' => 'value'])
        ->and($product->price_per_unit)->toBe('15.99')
        ->and($product->tax_rate)->toBe('21.00')
        ->and($product->wastage_pct)->toBe('5.50');
});

it('belongs to a resource', function () {
    $resource = Resource::factory()->create();
    $product = Product::factory()->create(['resource_id' => $resource->id]);

    expect($product->resource)->toBeInstanceOf(Resource::class)
        ->and($product->resource->id)->toBe($resource->id);
});

it('has many variants', function () {
    $product = Product::factory()->create();
    $variant1 = ProductVariant::factory()->create(['product_id' => $product->id]);
    $variant2 = ProductVariant::factory()->create(['product_id' => $product->id]);

    expect($product->variants)->toHaveCount(2)
        ->and($product->variants->contains($variant1))->toBeTrue()
        ->and($product->variants->contains($variant2))->toBeTrue();
});

it('can check if sellable', function () {
    $sellableProduct = Product::factory()->create(['product_type' => 'sellable']);
    $bookableProduct = Product::factory()->create(['product_type' => 'bookable']);
    $bothProduct = Product::factory()->create(['product_type' => 'both']);

    expect($sellableProduct->isSellable())->toBeTrue()
        ->and($bookableProduct->isSellable())->toBeFalse()
        ->and($bothProduct->isSellable())->toBeTrue();
});

it('normalizes units with direct input', function () {
    $product = Product::factory()->create([
        'calc_mode' => null,
        'unit_step' => 0.1,
        'rounding_mode' => 'round',
    ]);

    $units = $product->normalizeUnits(['units' => 2.5]);
    expect($units)->toBe(2.5);
});

it('normalizes units with direct length', function () {
    $product = Product::factory()->create([
        'calc_mode' => 'direct_length',
        'unit_step' => 0.1,
        'rounding_mode' => 'round',
    ]);

    // 2000mm = 2 meters
    $units = $product->normalizeUnits(['length_mm' => 2000]);
    expect($units)->toBe(2.0);
});

it('normalizes units with 2d dimensions', function () {
    $product = Product::factory()->create([
        'calc_mode' => 'dimensions_2d',
        'unit_step' => 0.01,
        'rounding_mode' => 'round',
    ]);

    // 2000mm x 1500mm = 2m x 1.5m = 3m²
    $units = $product->normalizeUnits(['length_mm' => 2000, 'width_mm' => 1500]);
    expect($units)->toBe(3.0);
});

it('normalizes units with 3d dimensions', function () {
    $product = Product::factory()->create([
        'calc_mode' => 'dimensions_3d',
        'unit_step' => 0.001,
        'rounding_mode' => 'round',
    ]);

    // 2000mm x 1000mm x 500mm = 2m x 1m x 0.5m = 1m³
    $units = $product->normalizeUnits([
        'length_mm' => 2000,
        'width_mm' => 1000,
        'height_mm' => 500
    ]);
    expect($units)->toBe(1.0);
});

it('applies wastage percentage', function () {
    $product = Product::factory()->create([
        'calc_mode' => null,
        'wastage_pct' => 10.0, // 10% wastage
        'unit_step' => 0.01,
    ]);

    $units = $product->normalizeUnits(['units' => 10]);
    expect($units)->toBe(11.0); // 10 + 10% = 11
});

it('applies min and max order quantities', function () {
    $product = Product::factory()->create([
        'min_order_qty' => 5.0,
        'max_order_qty' => 20.0,
        'unit_step' => 0.1,
    ]);

    // Test minimum
    $units = $product->normalizeUnits(['units' => 2]);
    expect($units)->toBe(5.0);

    // Test maximum
    $units = $product->normalizeUnits(['units' => 25]);
    expect($units)->toBe(20.0);

    // Test normal range
    $units = $product->normalizeUnits(['units' => 10]);
    expect($units)->toBe(10.0);
});

it('applies different rounding modes', function () {
    // Test ceil rounding
    $product = Product::factory()->create([
        'rounding_mode' => 'ceil',
        'unit_step' => 1.0,
    ]);
    $units = $product->normalizeUnits(['units' => 2.3]);
    expect($units)->toBe(3.0);

    // Test floor rounding
    $product->update(['rounding_mode' => 'floor']);
    $units = $product->normalizeUnits(['units' => 2.7]);
    expect($units)->toBe(2.0);

    // Test round (default)
    $product->update(['rounding_mode' => 'round']);
    $units = $product->normalizeUnits(['units' => 2.6]);
    expect($units)->toBe(3.0);
});

it('calculates price for units', function () {
    $product = Product::factory()->create([
        'price_per_unit' => 10.00,
        'tax_rate' => 21.00,
    ]);

    $price = $product->priceForUnits(2.5);

    expect($price)->toBe([
        'excl' => 25.00,
        'tax' => 5.25,
        'incl' => 30.25,
    ]);
});

it('uses default tax rate when not set', function () {
    config(['manta-products.default_tax_rate' => 19.00]);
    
    $product = Product::factory()->create([
        'price_per_unit' => 10.00,
        'tax_rate' => null,
    ]);

    $price = $product->priceForUnits(1);

    expect($price)->toBe([
        'excl' => 10.00,
        'tax' => 1.90,
        'incl' => 11.90,
    ]);
});

it('uses soft deletes', function () {
    $product = Product::factory()->create();
    
    $product->delete();
    
    expect($product->fresh()->deleted_at)->not->toBeNull();
});

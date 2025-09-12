<?php

use Darvis\MantaProduct\Models\Product;

beforeEach(function () {
    // Migraties worden automatisch geladen door TestCase
});

it('can create a product using factory', function () {
    $product = Product::factory()->create();

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->id->not->toBeNull();
});

it('generates unique slugs', function () {
    $product1 = Product::factory()->create();
    $product2 = Product::factory()->create();

    expect($product1->slug)
        ->not->toBe($product2->slug)
        ->toContain('-');
    
    expect($product2->slug)->toContain('-');
});

it('creates products with valid product types', function () {
    $validTypes = ['sellable', 'bookable', 'both'];
    
    for ($i = 0; $i < 10; $i++) {
        $product = Product::factory()->create();
        expect($product->product_type)->toBeIn($validTypes);
    }
});

it('creates products with valid time units', function () {
    $validTimeUnits = ['minute', 'day'];
    
    for ($i = 0; $i < 10; $i++) {
        $product = Product::factory()->create();
        expect($product->time_unit)->toBeIn($validTimeUnits);
    }
});

it('creates products with valid unit types', function () {
    $validUnitTypes = ['piece', 'meter', 'm2', 'm3'];
    
    for ($i = 0; $i < 10; $i++) {
        $product = Product::factory()->create();
        expect($product->unit_type)->toBeIn($validUnitTypes);
    }
});

it('creates products with valid calc modes', function () {
    $validCalcModes = ['direct_length', 'dimensions_2d', 'dimensions_3d'];
    
    for ($i = 0; $i < 10; $i++) {
        $product = Product::factory()->create();
        expect($product->calc_mode)->toBeIn($validCalcModes);
    }
});

it('creates products with reasonable dimensions', function () {
    $product = Product::factory()->create();

    expect($product)
        ->length_mm->toBeGreaterThanOrEqual(500)->toBeLessThanOrEqual(3000)
        ->width_mm->toBeGreaterThanOrEqual(100)->toBeLessThanOrEqual(1500)
        ->height_mm->toBeGreaterThanOrEqual(10)->toBeLessThanOrEqual(800);
});

it('creates products with reasonable prices', function () {
    $product = Product::factory()->create();

    expect($product->price_per_unit)
        ->toBeGreaterThanOrEqual(2.00)
        ->toBeLessThanOrEqual(99.00);
});

it('creates products with default values', function () {
    $product = Product::factory()->create();

    expect($product)
        ->active->toBeTrue()
        ->tax_rate->toBe(21.00)
        ->unit_step->toBe(0.01)
        ->dimension_unit->toBe('mm')
        ->meta->toBe([]);
});

it('creates products with valid capacity range', function () {
    $product = Product::factory()->create();

    expect($product->capacity)
        ->toBeGreaterThanOrEqual(1)
        ->toBeLessThanOrEqual(8);
});

it('creates products with valid block sizes', function () {
    $validBlockSizes = [15, 30, 60, 1];
    
    for ($i = 0; $i < 10; $i++) {
        $product = Product::factory()->create();
        expect($product->block_size)->toBeIn($validBlockSizes);
    }
});

it('can override factory attributes', function () {
    $product = Product::factory()->create([
        'title' => 'Custom Product Title',
        'price_per_unit' => 150.00,
        'active' => false,
    ]);

    expect($product)
        ->title->toBe('Custom Product Title')
        ->price_per_unit->toBe(150.00)
        ->active->toBeFalse();
});

it('can create multiple products', function () {
    $products = Product::factory()->count(5)->create();

    expect($products)->toHaveCount(5);
    expect(Product::count())->toBe(5);
    
    // Ensure all have unique slugs
    $slugs = $products->pluck('slug')->toArray();
    expect(count(array_unique($slugs)))->toBe(5);
});

it('generates proper title format', function () {
    $product = Product::factory()->create();

    // Title should be capitalized
    expect($product->title)->toBe(ucfirst($product->title));
    
    // Title should contain words
    expect(str_word_count($product->title))->toBeGreaterThan(0);
});

it('can make products without persisting', function () {
    $product = Product::factory()->make();

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->id->toBeNull();
    
    expect(Product::count())->toBe(0);
});

it('creates products with consistent data types', function () {
    $product = Product::factory()->create();

    expect($product)
        ->active->toBeBool()
        ->title->toBeString()
        ->slug->toBeString()
        ->product_type->toBeString()
        ->capacity->toBeInt()
        ->block_size->toBeInt()
        ->meta->toBeArray();
});

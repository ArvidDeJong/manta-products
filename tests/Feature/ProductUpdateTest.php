<?php

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Livewire\Products\ProductUpdate;
use Livewire\Livewire;

beforeEach(function () {
    // Migraties worden automatisch geladen door TestCase
});

it('can render the component', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->assertStatus(200)
        ->assertViewIs('manta-product::livewire.products.product-update');
});

it('loads product data on mount', function () {
    $product = Product::factory()->create([
        'title' => 'Test Product',
        'slug' => 'test-product',
        'product_type' => 'bookable',
        'unit_type' => 'piece',
    ]);

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->assertSet('title', 'Test Product')
        ->assertSet('slug', 'test-product')
        ->assertSet('product_type', 'bookable')
        ->assertSet('unit_type', 'piece');
});

it('generates slug when title is updated', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('title', 'Updated Product Name')
        ->assertSet('slug', 'updated-product-name');
});

it('can update a product with valid data', function () {
    $product = Product::factory()->create([
        'title' => 'Original Title',
        'slug' => 'original-slug',
    ]);

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('title', 'Updated Title')
        ->set('slug', 'updated-slug')
        ->set('product_type', 'both')
        ->set('unit_type', 'kg')
        ->call('save')
        ->assertRedirect(route('manta-product.demo.products'));

    expect($product->fresh())
        ->title->toBe('Updated Title')
        ->slug->toBe('updated-slug')
        ->product_type->toBe('both')
        ->unit_type->toBe('kg');
});

it('validates required title', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

it('validates title max length', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('title', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

it('validates required slug', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['slug' => 'required']);
});

it('validates slug max length', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('slug', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['slug' => 'max']);
});

it('validates unique slug excluding current product', function () {
    $product1 = Product::factory()->create(['slug' => 'existing-slug']);
    $product2 = Product::factory()->create(['slug' => 'another-slug']);

    // Should fail when trying to use another product's slug
    Livewire::test(ProductUpdate::class, ['product' => $product2])
        ->set('slug', 'existing-slug')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);

    // Should pass when keeping the same slug
    Livewire::test(ProductUpdate::class, ['product' => $product1])
        ->set('slug', 'existing-slug')
        ->call('save')
        ->assertHasNoErrors(['slug']);
});

it('validates required product type', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('product_type', '')
        ->call('save')
        ->assertHasErrors(['product_type' => 'required']);
});

it('validates required unit type', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('unit_type', '')
        ->call('save')
        ->assertHasErrors(['unit_type' => 'required']);
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->call('deleteConfirm')
        ->assertRedirect(route('manta-product.demo.products'));

    expect($product->fresh())->toBeNull();
});

it('can show delete modal', function () {
    $product = Product::factory()->create();

    // This test verifies the delete method exists and can be called
    // The actual modal functionality would need to be tested in a browser test
    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->call('delete')
        ->assertStatus(200);
});

it('preserves other product attributes when updating', function () {
    $product = Product::factory()->create([
        'title' => 'Original Title',
        'price_per_unit' => 25.50,
        'active' => true,
        'capacity' => 10,
    ]);

    Livewire::test(ProductUpdate::class, ['product' => $product])
        ->set('title', 'Updated Title')
        ->call('save');

    $updatedProduct = $product->fresh();
    expect($updatedProduct)
        ->title->toBe('Updated Title')
        ->price_per_unit->toBe(25.50)
        ->active->toBeTrue()
        ->capacity->toBe(10);
});

it('can handle different product types', function () {
    $product = Product::factory()->create();
    $productTypes = ['sellable', 'bookable', 'both'];

    foreach ($productTypes as $type) {
        Livewire::test(ProductUpdate::class, ['product' => $product])
            ->set('product_type', $type)
            ->call('save');

        expect($product->fresh()->product_type)->toBe($type);
    }
});

it('can handle different unit types', function () {
    $product = Product::factory()->create();
    $unitTypes = ['piece', 'm', 'm2', 'm3', 'kg', 'liter'];

    foreach ($unitTypes as $type) {
        Livewire::test(ProductUpdate::class, ['product' => $product])
            ->set('unit_type', $type)
            ->call('save');

        expect($product->fresh()->unit_type)->toBe($type);
    }
});

<?php

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Livewire\Products\ProductCreate;
use Livewire\Livewire;

beforeEach(function () {
    // Migraties worden automatisch geladen door TestCase
});

it('can render the component', function () {
    Livewire::test(ProductCreate::class)
        ->assertStatus(200)
        ->assertViewIs('manta-products::livewire.products.product-create');
});

it('has default values', function () {
    Livewire::test(ProductCreate::class)
        ->assertSet('title', '')
        ->assertSet('slug', '')
        ->assertSet('product_type', 'sellable')
        ->assertSet('unit_type', 'm2');
});

it('generates slug when title is updated', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Test Product Name')
        ->assertSet('slug', 'test-product-name');
});

it('handles special characters in slug generation', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Product with Spëcial Chàracters & Numbers 123!')
        ->assertSet('slug', 'product-with-special-characters-numbers-123');
});

it('can create a product with valid data', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'New Product')
        ->set('slug', 'new-product')
        ->set('product_type', 'bookable')
        ->set('unit_type', 'piece')
        ->call('save')
        ->assertRedirect(route('manta-products.demo.products'));

    expect(Product::where('slug', 'new-product')->first())
        ->title->toBe('New Product')
        ->product_type->toBe('bookable')
        ->unit_type->toBe('piece')
        ->active->toBeTrue();
});

it('validates required title', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', '')
        ->call('save')
        ->assertHasErrors(['title' => 'required']);
});

it('validates title max length', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['title' => 'max']);
});

it('validates required slug', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Valid Title')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['slug' => 'required']);
});

it('validates slug max length', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Valid Title')
        ->set('slug', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['slug' => 'max']);
});

it('validates unique slug', function () {
    Product::factory()->create(['slug' => 'existing-slug']);

    Livewire::test(ProductCreate::class)
        ->set('title', 'New Product')
        ->set('slug', 'existing-slug')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

it('validates required product type', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Valid Title')
        ->set('slug', 'valid-slug')
        ->set('product_type', '')
        ->call('save')
        ->assertHasErrors(['product_type' => 'required']);
});

it('validates required unit type', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Valid Title')
        ->set('slug', 'valid-slug')
        ->set('unit_type', '')
        ->call('save')
        ->assertHasErrors(['unit_type' => 'required']);
});

it('creates product with active true by default', function () {
    Livewire::test(ProductCreate::class)
        ->set('title', 'Test Product')
        ->set('slug', 'test-product')
        ->call('save');

    $product = Product::where('slug', 'test-product')->first();
    expect($product->active)->toBeTrue();
});

it('can handle different product types', function () {
    $productTypes = ['sellable', 'bookable', 'both'];

    foreach ($productTypes as $type) {
        Livewire::test(ProductCreate::class)
            ->set('title', "Product {$type}")
            ->set('slug', "product-{$type}")
            ->set('product_type', $type)
            ->call('save');

        expect(Product::where('slug', "product-{$type}")->first())
            ->product_type->toBe($type);
    }
});

it('can handle different unit types', function () {
    $unitTypes = ['piece', 'm', 'm2', 'm3', 'kg', 'liter'];

    foreach ($unitTypes as $type) {
        Livewire::test(ProductCreate::class)
            ->set('title', "Product {$type}")
            ->set('slug', "product-{$type}")
            ->set('unit_type', $type)
            ->call('save');

        expect(Product::where('slug', "product-{$type}")->first())
            ->unit_type->toBe($type);
    }
});

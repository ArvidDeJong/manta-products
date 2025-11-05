<?php

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Livewire\Products\ProductList;
use Livewire\Livewire;

beforeEach(function () {
    // Migraties worden automatisch geladen door TestCase
});

it('can render the component', function () {
    Livewire::test(ProductList::class)
        ->assertStatus(200)
        ->assertViewIs('manta-product::livewire.products.product-list');
});

it('displays products', function () {
    $product1 = Product::factory()->create(['title' => 'Product A']);
    $product2 = Product::factory()->create(['title' => 'Product B']);

    Livewire::test(ProductList::class)
        ->assertSee('Product A')
        ->assertSee('Product B');
});

it('can search products', function () {
    Product::factory()->create(['title' => 'Laravel Book']);
    Product::factory()->create(['title' => 'PHP Guide']);
    Product::factory()->create(['title' => 'Vue.js Tutorial']);

    Livewire::test(ProductList::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Book')
        ->assertDontSee('PHP Guide')
        ->assertDontSee('Vue.js Tutorial');
});

it('can sort products', function () {
    Product::factory()->create(['title' => 'Zebra Product']);
    Product::factory()->create(['title' => 'Alpha Product']);

    $component = Livewire::test(ProductList::class)
        ->call('doSort', 'title');

    // Should be sorted ascending by default
    expect($component->get('sortBy'))->toBe('title')
        ->and($component->get('sortDirection'))->toBe('asc');

    // Call sort again to reverse direction
    $component->call('doSort', 'title');
    expect($component->get('sortDirection'))->toBe('desc');
});

it('can change sort field', function () {
    $component = Livewire::test(ProductList::class)
        ->set('sortBy', 'title')
        ->set('sortDirection', 'desc')
        ->call('doSort', 'created_at');

    expect($component->get('sortBy'))->toBe('created_at')
        ->and($component->get('sortDirection'))->toBe('asc');
});

it('can change per page', function () {
    Product::factory()->count(25)->create();

    Livewire::test(ProductList::class)
        ->set('perPage', 5)
        ->assertViewHas('products', function ($products) {
            return $products->count() === 5;
        });
});

it('can show delete modal', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductList::class)
        ->call('showDeleteModal', $product->id)
        ->assertSet('deleteId', $product->id);
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductList::class)
        ->set('deleteId', $product->id)
        ->call('delete');

    expect($product->fresh()->deleted_at)->not->toBeNull();
});

it('can restore a product', function () {
    $product = Product::factory()->create();
    $product->delete();

    Livewire::test(ProductList::class)
        ->call('restore', $product->id);

    expect($product->fresh()->deleted_at)->toBeNull();
});

it('can show trashed products', function () {
    $activeProduct = Product::factory()->create(['title' => 'Active Product']);
    $trashedProduct = Product::factory()->create(['title' => 'Trashed Product']);
    $trashedProduct->delete();

    // Without trashed
    Livewire::test(ProductList::class)
        ->set('withTrashed', false)
        ->assertSee('Active Product')
        ->assertDontSee('Trashed Product');

    // With trashed
    Livewire::test(ProductList::class)
        ->set('withTrashed', true)
        ->assertSee('Active Product')
        ->assertSee('Trashed Product');
});

it('paginates products', function () {
    Product::factory()->count(25)->create();

    Livewire::test(ProductList::class)
        ->set('perPage', 10)
        ->assertViewHas('products', function ($products) {
            return $products->hasPages() && $products->count() === 10;
        });
});

it('has url queryable search', function () {
    Livewire::test(ProductList::class)
        ->set('search', 'test search')
        ->assertSet('search', 'test search');
});

it('has url queryable per page', function () {
    Livewire::test(ProductList::class)
        ->set('perPage', 25)
        ->assertSet('perPage', 25);
});

it('has url queryable with trashed', function () {
    Livewire::test(ProductList::class)
        ->set('withTrashed', true)
        ->assertSet('withTrashed', true);
});

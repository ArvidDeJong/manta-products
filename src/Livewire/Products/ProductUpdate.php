<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Livewire\Component;
use Darvis\MantaProduct\Models\Product;
use Illuminate\Support\Str;
use Flux\Flux;

class ProductUpdate extends Component
{
    public Product $product;

    public string $title = '';
    public string $slug = '';
    public string $product_type = 'sellable';
    public string $unit_type = 'm2';

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->title = $product->title;
        $this->slug = $product->slug;
        $this->product_type = $product->product_type;
        $this->unit_type = $product->unit_type;
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function delete()
    {
        Flux::modal('delete-product-modal')->open();
    }

    public function deleteConfirm()
    {
        $this->product->delete();
        Flux::modal('delete-product-modal')->close();
        return redirect()->route('manta-products.demo.products');
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->product->id,
            'product_type' => 'required|string',
            'unit_type' => 'required|string',
        ]);

        $this->product->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'product_type' => $this->product_type,
            'unit_type' => $this->unit_type,
        ]);

        return redirect()->route('manta-products.demo.products');
    }

    public function render()
    {
        return view('manta-products::livewire.products.product-update')->layout('manta-cms::layouts.app');
    }
}

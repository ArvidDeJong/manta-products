<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Livewire\Component;
use Darvis\MantaProduct\Models\Product;
use Illuminate\Support\Str;

class ProductCreate extends Component
{
    public string $title = '';
    public string $slug = '';
    public string $product_type = 'sellable';
    public string $unit_type = 'm2';

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'product_type' => 'required|string',
            'unit_type' => 'required|string',
        ]);

        Product::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'product_type' => $this->product_type,
            'unit_type' => $this->unit_type,
            'active' => true,
        ]);

        return redirect()->route('manta-products.demo.products');
    }

    public function render()
    {
        return view('manta-products::livewire.products.product-create')->layout('manta-cms::layouts.app');
    }
}

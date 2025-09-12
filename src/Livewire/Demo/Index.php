<?php

namespace Darvis\MantaProduct\Livewire\Demo;

use Livewire\Component;
use Darvis\MantaProduct\Models\Product;

class Index extends Component
{
    public function render()
    {
        return view('manta-products::livewire.demo.index', [
            'products' => Product::query()->latest()->limit(10)->get(),
        ])->layout('manta-cms::layouts.app');
    }
}

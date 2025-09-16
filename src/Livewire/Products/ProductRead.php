<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Traits\ProductTrait;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class ProductRead extends Component
{
    use MantaTrait, ProductTrait;

    public function mount(Product $product)
    {
        $this->item = $product;
        $this->itemOrg = $product;
        $this->id = $product->id;
        $this->locale = $product->locale;

        $this->getLocaleInfo();
        $this->getBreadcrumb('read');
        $this->getTablist();
        $this->loadAttributes();
        $this->loadVariants();
    }

    public function render()
    {
        return view('manta-product::livewire.products.product-read');
    }
}

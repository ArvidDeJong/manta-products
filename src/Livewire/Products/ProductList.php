<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Traits\ProductTrait;
use Livewire\Attributes\Layout;
use Manta\FluxCMS\Traits\MantaTrait;
use Manta\FluxCMS\Traits\SortableTrait;
use Manta\FluxCMS\Traits\WithSortingTrait;

#[Layout('manta-cms::layouts.app')]
class ProductList extends Component
{
    use ProductTrait;
    use WithPagination;
    use SortableTrait;
    use MantaTrait;
    use WithSortingTrait;

    public function mount()
    {
        $this->getBreadcrumb();
        $this->sortBy = 'title';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $this->trashed = count(Product::whereNull('pid')->onlyTrashed()->get());

        $obj = Product::with(['categories', 'variants' => function($query) {
            $query->orderBy('price_override_excl', 'asc');
        }])->whereNull('pid');
        if ($this->tablistShow == 'trashed') {
            $obj->onlyTrashed();
        }
        $obj = $this->applySorting($obj);
        $obj = $this->applySearch($obj);
        $items = $obj->paginate(50);
        return view('manta-product::livewire.products.product-list', ['items' => $items]);
    }
}

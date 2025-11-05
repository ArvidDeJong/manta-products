<?php

namespace Darvis\MantaProduct\Livewire\Variants;

use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Traits\VariantTrait;
use Livewire\Component;
use Livewire\WithPagination;
use Manta\FluxCMS\Traits\MantaTrait;
use Manta\FluxCMS\Traits\SortableTrait;
use Manta\FluxCMS\Traits\WithSortingTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class VariantList extends Component
{
    use VariantTrait;
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
        $obj = ProductVariant::with(['product']);
        $obj = $this->applySorting($obj);
        $obj = $this->applySearch($obj);
        $items = $obj->paginate(25);
        
        return view('manta-product::livewire.variants.variant-list', ['items' => $items]);
    }
}

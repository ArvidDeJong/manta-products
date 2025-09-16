<?php

namespace Darvis\MantaProduct\Livewire\Attributes;

use Livewire\Component;
use Livewire\WithPagination;
use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Traits\AttributeTrait;
use Livewire\Attributes\Layout;
use Manta\FluxCMS\Traits\MantaTrait;
use Manta\FluxCMS\Traits\SortableTrait;
use Manta\FluxCMS\Traits\WithSortingTrait;

#[Layout('manta-cms::layouts.app')]
class AttributeList extends Component
{
    use AttributeTrait;
    use WithPagination;
    use SortableTrait;
    use MantaTrait;
    use WithSortingTrait;

    public function mount()
    {
        $this->getBreadcrumb();
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $this->trashed = count(Attribute::onlyTrashed()->get());

        $obj = Attribute::query();
        if ($this->tablistShow == 'trashed') {
            $obj->onlyTrashed();
        }
        $obj = $this->applySorting($obj);
        $obj = $this->applySearch($obj);
        $items = $obj->paginate(50);
        
        return view('manta-product::livewire.attributes.attribute-list', ['items' => $items]);
    }
}

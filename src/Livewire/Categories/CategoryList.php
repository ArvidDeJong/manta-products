<?php

namespace Darvis\MantaProduct\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use Darvis\MantaProduct\Models\Category;
use Darvis\MantaProduct\Traits\CategoryTrait;
use Livewire\Attributes\Layout;
use Manta\FluxCMS\Traits\MantaTrait;
use Manta\FluxCMS\Traits\SortableTrait;
use Manta\FluxCMS\Traits\WithSortingTrait;

#[Layout('manta-cms::layouts.app')]
class CategoryList extends Component
{
    use CategoryTrait;
    use WithPagination;
    use SortableTrait;
    use MantaTrait;
    use WithSortingTrait;

    public function mount()
    {
        $this->getBreadcrumb();
        $this->sortBy = 'sort';
        $this->sortDirection = 'asc';
    }

    /**
     * Override MantaTrait's deleteChildItems to use parent_id for categories
     */
    protected function deleteChildItems($id)
    {
        $this->deleteCategoryChildren($id);
    }

    public function render()
    {
        $this->trashed = count(Category::onlyTrashed()->get());

        $obj = Category::query()->with(['parent', 'children']);
        
        if ($this->tablistShow == 'trashed') {
            $obj->onlyTrashed();
        }
        
        $obj = $this->applySorting($obj);
        
        // Apply search
        if (!empty($this->search)) {
            $obj->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        $items = $obj->paginate(50);
        
        return view('manta-product::livewire.categories.category-list', ['items' => $items]);
    }
}

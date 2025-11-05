<?php

namespace Darvis\MantaProduct\Livewire\Categories;

use Darvis\MantaProduct\Models\Category;
use Darvis\MantaProduct\Traits\CategoryTrait;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class CategoryRead extends Component
{
    use MantaTrait, CategoryTrait;

    public function mount($category)
    {
        $this->item = Category::with(['parent', 'children', 'products'])->findOrFail($category->id ?? $category);
        $this->getBreadcrumb('read');
    }

    public function render()
    {
        return view('manta-product::livewire.categories.category-read');
    }
}

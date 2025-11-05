<?php

namespace Darvis\MantaProduct\Livewire\Categories;

use Darvis\MantaProduct\Models\Category;
use Darvis\MantaProduct\Traits\CategoryTrait;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class CategoryUpload extends Component
{
    use MantaTrait, CategoryTrait;

    public function mount($category)
    {
        $this->item = is_object($category) ? $category : Category::findOrFail($category);
        $this->id = $this->item->id;
        $this->getBreadcrumb('upload');
        $this->getTablist();
    }

    public function render()
    {
        return view('manta-cms::livewire.default.manta-default-upload');
    }
}

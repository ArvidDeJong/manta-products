<?php

namespace Darvis\MantaProduct\Livewire\Categories;

use Darvis\MantaProduct\Models\Category;
use Darvis\MantaProduct\Traits\CategoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class CategoryUpdate extends Component
{
    use MantaTrait, CategoryTrait;

    public function mount(Request $request, $category)
    {
        $this->item = is_object($category) ? $category : Category::findOrFail($category);
        $this->itemOrg = $this->item;
        $this->id = $this->item->id;

        $this->fill(
            $this->item->only(
                'locale',
                'name',
                'slug',
                'description',
                'parent_id',
                'sort',
                'active',
                'image'
            )
        );

        $this->getLocaleInfo();
        $this->getTablist();
        $this->getBreadcrumb('update');
    }

    public function updatedName($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();

        // Check if trying to set itself as parent
        if ($this->parent_id == $this->item->id) {
            \Flux\Flux::toast('Een categorie kan niet zijn eigen hoofdcategorie zijn!', variant: 'danger');
            return;
        }

        // Check if trying to set a descendant as parent
        $descendantIds = $this->item->getAllCategoryIds();
        if ($this->parent_id && $descendantIds->contains($this->parent_id)) {
            \Flux\Flux::toast('Een subcategorie kan niet als hoofdcategorie worden ingesteld!', variant: 'danger');
            return;
        }

        $this->item->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'sort' => $this->sort,
            'active' => $this->active,
            'image' => $this->image,
        ]);

        \Flux\Flux::toast('Categorie succesvol bijgewerkt!', variant: 'success');

        return redirect()->route($this->module_routes['update'], ['id' => $this->item->id]);
    }

    public function render()
    {
        $categoriesTree = $this->getCategoriesTree($this->item->id);

        return view('manta-product::livewire.categories.category-update', [
            'categoriesTree' => $categoriesTree
        ]);
    }
}

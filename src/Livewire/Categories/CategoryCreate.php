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
class CategoryCreate extends Component
{
    use MantaTrait, CategoryTrait;

    public function mount(Request $request)
    {
        $this->locale = getLocaleManta();
        
        if ($request->input('parent_id')) {
            $this->parent_id = $request->input('parent_id');
        }

        $this->getLocaleInfo();
        $this->getTablist();
        $this->getBreadcrumb('create');

        // Faker data voor development
        if (env('USE_FAKER', false) && class_exists(\Faker\Factory::class)) {
            $this->fillFakeData();
        }
    }

    private function fillFakeData()
    {
        $faker = \Faker\Factory::create('nl_NL');
        
        $categoryNames = [
            'Vloeren',
            'Wandbekleding',
            'Tegels',
            'Parket',
            'Laminaat',
            'Vinyl',
            'Natuursteen',
            'Keramiek',
            'Hout',
            'Bamboe',
            'Kurk',
            'Beton',
        ];

        $this->name = $faker->randomElement($categoryNames);
        $this->slug = Str::slug($this->name);
        $this->description = $faker->optional(0.7)->sentence(12);
        $this->active = $faker->boolean(90); // 90% kans op actief
        $this->sort = $faker->numberBetween(0, 100);
    }

    public function generateFakeData()
    {
        $this->fillFakeData();
        \Flux\Flux::toast('Fake data gegenereerd!', duration: 2000, variant: 'success');
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

        $category = Category::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'sort' => $this->sort,
            'active' => $this->active,
            'image' => $this->image,
        ]);

        \Flux\Flux::toast('Categorie succesvol aangemaakt!', variant: 'success');

        return redirect()->route($this->module_routes['update'], ['category' => $category->id]);
    }

    public function render()
    {
        $categoriesTree = $this->getCategoriesTree();
        
        return view('manta-product::livewire.categories.category-create', [
            'categoriesTree' => $categoriesTree
        ]);
    }
}

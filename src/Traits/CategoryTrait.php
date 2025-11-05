<?php

namespace Darvis\MantaProduct\Traits;

use Darvis\MantaProduct\Models\Category;
use Livewire\Attributes\Locked;
use Manta\FluxCMS\Services\ModuleSettingsService;

trait CategoryTrait
{
    public function __construct()
    {
        $this->module_routes = [
            'name' => 'category',
            'list' => 'category.list',
            'create' => 'category.create',
            'update' => 'category.update',
            'read' => 'category.read',
            'upload' => 'category.upload',
            'settings' => null,
            'maps' => null,
        ];

        $settings = ModuleSettingsService::ensureModuleSettings('category', 'darvis/manta-product');
        $this->config = $settings;

        $this->fields = $settings['fields'] ?? [];
        $this->tab_title = $settings['tab_title'] ?? null;
        $this->moduleClass = 'Darvis\MantaProduct\Models\Category';
    }

    // Model items
    public ?Category $item = null;
    public ?Category $itemOrg = null;

    #[Locked]
    public ?string $company_id = null;

    #[Locked]
    public ?string $host = null;

    public ?string $locale = null;

    // Category eigenschappen
    public ?string $name = null;
    public ?string $slug = null;
    public ?string $description = null;
    public ?int $parent_id = null;
    public int $sort = 0;
    public bool $active = true;
    public ?string $image = null;

    #[Locked]
    public ?string $redirect_url = null;

    public function rules()
    {
        $return = [];
        
        if ($this->fields['name']) {
            $return['name'] = 'required|string|max:255';
        }
        
        if (isset($this->fields['slug']) && $this->fields['slug']['active'] == true) {
            if ($this->item) {
                $return['slug'] = $this->fields['slug']['required'] == true 
                    ? 'required|string|max:255|unique:manta_categories,slug,' . $this->item->id 
                    : 'nullable|string|max:255|unique:manta_categories,slug,' . $this->item->id;
            } else {
                $return['slug'] = $this->fields['slug']['required'] == true 
                    ? 'required|string|max:255|unique:manta_categories,slug' 
                    : 'nullable|string|max:255|unique:manta_categories,slug';
            }
        }

        if ($this->fields['description']) {
            $return['description'] = 'nullable|string';
        }

        if ($this->fields['parent_id']) {
            $return['parent_id'] = 'nullable|exists:manta_categories,id';
        }

        if ($this->fields['sort']) {
            $return['sort'] = 'nullable|integer|min:0';
        }

        return $return;
    }

    public function messages()
    {
        return [
            'name.required' => 'De naam is verplicht',
            'name.max' => 'De naam mag maximaal 255 tekens zijn',
            'slug.required' => 'De slug is verplicht',
            'slug.unique' => 'Deze slug bestaat al',
            'parent_id.exists' => 'De geselecteerde hoofdcategorie bestaat niet',
            'sort.min' => 'De sorteervolgorde moet minimaal 0 zijn',
        ];
    }

    /**
     * Get all categories as a flat tree structure for select dropdown
     */
    public function getCategoriesTree($excludeId = null)
    {
        $categories = Category::with('descendants')
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $tree = [];
        foreach ($categories as $category) {
            if ($excludeId && $category->id == $excludeId) {
                continue;
            }
            $this->buildTreeArray($category, $tree, 0, $excludeId);
        }

        return $tree;
    }

    private function buildTreeArray($category, &$tree, $level, $excludeId = null)
    {
        $prefix = str_repeat('—', $level) . ' ';
        $tree[$category->id] = $prefix . $category->name;

        foreach ($category->children as $child) {
            if ($excludeId && $child->id == $excludeId) {
                continue;
            }
            $this->buildTreeArray($child, $tree, $level + 1, $excludeId);
        }
    }

    /**
     * Delete child categories using parent_id instead of pid
     */
    protected function deleteCategoryChildren($id)
    {
        $children = $this->moduleClass::where('parent_id', $id)->get();

        foreach ($children as $child) {
            $this->deleteCategoryChildren($child->id);
            $child->update(['deleted_by' => auth('staff')->user()->name]);
            $child->delete();
        }
    }
}

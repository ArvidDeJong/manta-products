<?php

namespace Darvis\MantaProduct\Traits;

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Models\Productcat;
use Flux\Flux;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Manta\FluxCMS\Models\MantaModule;
use Manta\FluxCMS\Services\MantaOpenai;
use Manta\FluxCMS\Services\ModuleSettingsService;

trait ProductTrait
{
    public function __construct()
    {
        $this->module_routes = [
            'name' => 'product',
            'list' => 'product.list',
            'create' => 'product.create',
            'update' => 'product.update',
            'read' => 'product.read',
            'upload' => 'product.upload',
            'settings' => 'product.settings',
            'maps' => null,
        ];

        $settings = ModuleSettingsService::ensureModuleSettings('product', 'darvis/manta-product');
        $this->config = $settings;

        $this->fields = $settings['fields'] ?? [];
        $this->tab_title = $settings['tab_title'] ?? null;
        $this->moduleClass = 'Darvis\MantaProduct\Models\Product';
        $this->openaiImagePossible = true;
    }

    // * Model items
    public ?Product $item = null;
    public ?Product $itemOrg = null;



    #[Locked]
    public ?string $company_id = null;

    #[Locked]
    public ?string $host = null;

    public ?string $locale = null;

    // Tab management
    public string $activeTab = 'general';
    public ?string $pid = null;

    // Product eigenschappen
    public bool $active = true;
    public int $capacity = 1;
    public int $block_size = 1;
    public string $product_type = 'bookable'; // bookable|sellable|both
    public ?string $time_unit = null; // minute|day
    public ?int $resource_id = null;
    public ?string $title = null;
    public ?string $title_2 = null;
    public ?string $title_3 = null;
    public ?string $slug = null;
    public ?string $excerpt = null;
    public ?string $description = null;
    public ?string $description_2 = null;
    public ?string $description_3 = null;
    public ?string $comments = null;

    // Unit pricing
    public ?string $unit_type = null; // piece|meter|m2|m3
    public ?float $unit_step = null;
    public ?float $min_order_qty = null;
    public ?float $max_order_qty = null;
    public ?float $price_per_unit = null;
    public ?float $tax_rate = null;
    public ?string $calc_mode = null; // direct_length|dimensions_2d|dimensions_3d
    public ?float $wastage_pct = null;
    public ?string $rounding_mode = null; // ceil|floor|round

    // Dimensions (mm)
    public ?int $length_mm = null;
    public ?int $width_mm = null;
    public ?int $height_mm = null;
    public string $dimension_unit = 'mm';

    public array $productcat = [];

    // Product attributen
    public array $availableAttributes = [];
    public array $selectedAttributes = [];

    // Product varianten
    public array $variants = [];
    public array $newVariant = [];
    public bool $showVariantForm = false;
    
    // Variant modal properties
    public $variantTitle = '';
    public $variantSku = '';
    public $variantPrice = '';
    public $variantActive = true;

    // Categorieën
    public array $selectedCategories = [];
    public array $availableCategories = [];
    public ?string $categorySearch = null;

    // File uploads
    public $files = [];
    public array $existingUploads = [];

    #[Locked]
    public ?string $redirect_url = null;

    public function rules()
    {
        $return = [];
        if ($this->fields['title']) $return['title'] = 'required';
        if ($this->fields['capacity']) $return['capacity'] = 'required|integer|min:1';
        if ($this->fields['block_size']) $return['block_size'] = 'required|integer|min:1';
        if ($this->fields['product_type']) $return['product_type'] = 'required|in:bookable,sellable,both';

        if (isset($this->fields['slug']) && $this->fields['slug']['active'] == true) {
            if ($this->item) {
                $return['slug'] = $this->fields['slug']['required'] == true ? 'required|string|max:255|unique:manta_products,slug,' . $this->item->id : 'nullable|string|max:255|unique:manta_products,slug';
            } else {
                $return['slug'] = $this->fields['slug']['required'] == true ? 'required|string|max:255|unique:manta_products,slug' : 'nullable|string|max:255|unique:manta_products,slug';
            }
        }

        // Pricing validatie
        if ($this->fields['price_per_unit']) $return['price_per_unit'] = 'nullable|numeric|min:0';
        if ($this->fields['tax_rate']) $return['tax_rate'] = 'nullable|numeric|min:0|max:100';
        if ($this->fields['min_order_qty']) $return['min_order_qty'] = 'nullable|numeric|min:0';
        if ($this->fields['max_order_qty']) $return['max_order_qty'] = 'nullable|numeric|min:0';

        // Dimensie validatie
        if ($this->fields['length_mm']) $return['length_mm'] = 'nullable|integer|min:0';
        if ($this->fields['width_mm']) $return['width_mm'] = 'nullable|integer|min:0';
        if ($this->fields['height_mm']) $return['height_mm'] = 'nullable|integer|min:0';

        return $return;
    }

    public function messages()
    {
        $return = [];
        $return['title.required'] = 'De titel is verplicht';
        $return['capacity.required'] = 'De capaciteit is verplicht';
        $return['capacity.min'] = 'De capaciteit moet minimaal 1 zijn';
        $return['block_size.required'] = 'De blok grootte is verplicht';
        $return['block_size.min'] = 'De blok grootte moet minimaal 1 zijn';
        $return['product_type.required'] = 'Het product type is verplicht';
        $return['product_type.in'] = 'Het product type moet bookable, sellable of both zijn';
        $return['price_per_unit.numeric'] = 'De prijs per eenheid moet een getal zijn';
        $return['price_per_unit.min'] = 'De prijs per eenheid moet 0 of hoger zijn';
        $return['tax_rate.numeric'] = 'Het BTW percentage moet een getal zijn';
        $return['tax_rate.max'] = 'Het BTW percentage mag niet hoger zijn dan 100';
        return $return;
    }

    public function loadAttributes()
    {
        // Laad alle beschikbare attributen
        $this->availableAttributes = \Darvis\MantaProduct\Models\Attribute::orderBy('sort')->get()->toArray();

        // Laad geselecteerde attributen voor dit product
        if ($this->item) {
            $productAttributes = \Darvis\MantaProduct\Models\ProductAttribute::where('product_id', $this->item->id)
                ->with('attribute')
                ->orderBy('sort')
                ->get();

            foreach ($productAttributes as $productAttribute) {
                $this->selectedAttributes[$productAttribute->attribute_id] = [
                    'is_required' => $productAttribute->is_required,
                    'sort' => $productAttribute->sort,
                ];
            }
        }
    }

    public function toggleAttribute($attributeId)
    {
        if (isset($this->selectedAttributes[$attributeId])) {
            unset($this->selectedAttributes[$attributeId]);
        } else {
            $this->selectedAttributes[$attributeId] = [
                'is_required' => false,
                'sort' => count($this->selectedAttributes) + 1,
            ];
        }
    }

    public function updateAttributeRequired($attributeId, $isRequired)
    {
        if (isset($this->selectedAttributes[$attributeId])) {
            $this->selectedAttributes[$attributeId]['is_required'] = $isRequired;
        }
    }

    public function saveProductAttributes()
    {
        if (!$this->item) {
            return;
        }

        // Verwijder alle bestaande product attributen
        \Darvis\MantaProduct\Models\ProductAttribute::where('product_id', $this->item->id)->delete();

        // Voeg nieuwe product attributen toe
        foreach ($this->selectedAttributes as $attributeId => $data) {
            \Darvis\MantaProduct\Models\ProductAttribute::create([
                'product_id' => $this->item->id,
                'attribute_id' => $attributeId,
                'is_required' => $data['is_required'],
                'sort' => $data['sort'],
            ]);
        }
    }

    public function loadVariants()
    {
        if ($this->item) {
            $this->variants = $this->item->variants()
                ->with(['values.attribute', 'values.attributeValue'])
                ->get()
                ->toArray();
        }
    }

    public function loadCategories()
    {
        // Laad alle beschikbare categorieën met optionele zoekfilter
        $query = \Darvis\MantaProduct\Models\Category::with('parent')
            ->orderBy('sort');

        // Filter op zoekterm indien aanwezig
        if ($this->categorySearch) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->categorySearch . '%')
                    ->orWhere('description', 'like', '%' . $this->categorySearch . '%');
            });
        }

        $this->availableCategories = $query->get()->toArray();

        // Laad geselecteerde categorieën voor dit product
        if ($this->item) {
            $this->selectedCategories = $this->item->categories()->pluck('manta_categories.id')->toArray();
        }
    }

    public function updatedCategorySearch()
    {
        $this->loadCategories();
    }

    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_values(array_diff($this->selectedCategories, [$categoryId]));
        } else {
            $this->selectedCategories[] = $categoryId;
        }
    }

    public function saveCategories()
    {
        if (!$this->item) {
            return;
        }

        // Sync de categorieën met het product
        $this->item->categories()->sync($this->selectedCategories);
    }

    public function getCategoryBreadcrumb($category)
    {
        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, $current['name']);
            $current = $current['parent'] ?? null;
        }

        return implode(' > ', $breadcrumb);
    }

    public function loadUploads()
    {
        if ($this->item) {
            $this->existingUploads = $this->item->uploads()->orderBy('sort', 'ASC')->get()->toArray();
        }
    }

    public function removeUpload($uploadId)
    {
        $upload = \Manta\FluxCMS\Models\Upload::find($uploadId);
        if ($upload) {
            $upload->delete();
            $this->loadUploads();
            Flux::toast('Bestand verwijderd', variant: 'success');
        }
    }

    public function saveUploads()
    {


        if (!$this->item || empty($this->files)) {
            return;
        }

        $uploadModel = new \Manta\FluxCMS\Models\Upload();

        foreach ($this->files as $file) {

            $uploadModel->upload(
                $file,
                'Darvis\MantaProduct\Models\Product',
                $this->item->id,
                [
                    'disk' => config('manta-cms.media.disk', 'public'),
                    'location' => 'uploads/' . env('THEME', 'default') . '/products/' . date('Y') . '/' . date('m') . '/',
                ]
            );
        }

        $this->files = [];
        $this->loadUploads();
        Flux::toast('Bestanden geüpload', variant: 'success');
    }

    public function initNewVariant()
    {
        $this->newVariant = [
            'title' => '',
            'sku' => '',
            'active' => true,
            'capacity' => 1,
            'price_override_excl' => null,
            'stock_qty' => 0,
            'tax_rate' => null,
            'unit_step' => null,
            'unit_type' => null,
            'calc_mode' => null,
            'wastage_pct' => null,
            'rounding_mode' => null,
            'length_mm' => null,
            'width_mm' => null,
            'height_mm' => null,
            'variant_values' => [],
        ];
    }

    public function showVariantForm()
    {
        $this->initNewVariant();
        $this->showVariantForm = true;
    }

    public function hideVariantForm()
    {
        $this->showVariantForm = false;
        $this->newVariant = [];
    }

    public function addVariant()
    {
        if (!$this->item) {
            return;
        }

        $variantData = [
            'product_id' => $this->item->id,
            'title' => $this->newVariant['title'],
            'sku' => $this->newVariant['sku'],
            'active' => $this->newVariant['active'],
            'capacity' => $this->newVariant['capacity'],
            'price_override_excl' => $this->newVariant['price_override_excl'],
            'stock_qty' => $this->newVariant['stock_qty'],
            'tax_rate' => $this->newVariant['tax_rate'],
            'unit_step' => $this->newVariant['unit_step'],
            'unit_type' => $this->newVariant['unit_type'],
            'calc_mode' => $this->newVariant['calc_mode'],
            'wastage_pct' => $this->newVariant['wastage_pct'],
            'rounding_mode' => $this->newVariant['rounding_mode'],
            'length_mm' => $this->newVariant['length_mm'],
            'width_mm' => $this->newVariant['width_mm'],
            'height_mm' => $this->newVariant['height_mm'],
        ];

        // Genereer variant key op basis van attribute values
        $variantKey = $this->generateVariantKey($this->newVariant['variant_values'] ?? []);
        $variantData['variant_key'] = $variantKey;

        $variant = \Darvis\MantaProduct\Models\ProductVariant::create($variantData);

        // Voeg variant values toe
        if (!empty($this->newVariant['variant_values'])) {
            foreach ($this->newVariant['variant_values'] as $attributeId => $valueId) {
                \Darvis\MantaProduct\Models\ProductVariantValue::create([
                    'product_variant_id' => $variant->id,
                    'attribute_id' => $attributeId,
                    'attribute_value_id' => $valueId,
                ]);
            }
        }

        $this->hideVariantForm();
        $this->loadVariants();
    }

    public function deleteVariant($variantId)
    {
        $variant = \Darvis\MantaProduct\Models\ProductVariant::find($variantId);
        if ($variant && $variant->product_id === $this->item->id) {
            // Verwijder eerst de variant values
            \Darvis\MantaProduct\Models\ProductVariantValue::where('product_variant_id', $variantId)->delete();
            // Verwijder de variant
            $variant->delete();
            $this->loadVariants();
        }
    }

    private function generateVariantKey(array $variantValues): string
    {
        if (empty($variantValues)) {
            return 'default';
        }

        $keys = [];
        foreach ($variantValues as $attributeId => $valueId) {
            $keys[] = $attributeId . ':' . $valueId;
        }

        return implode('|', $keys);
    }

    public function getAttributeValues($attributeId)
    {
        return \Darvis\MantaProduct\Models\AttributeValue::where('attribute_id', $attributeId)
            ->orderBy('sort')
            ->get()
            ->toArray();
    }

    protected function applySearch($query)
    {
        return $this->search === ''
            ? $query
            : $query->where(function (Builder $querysub) {
                $querysub->where('title', 'LIKE', "%{$this->search}%")
                    ->orWhere('slug', 'LIKE', "%{$this->search}%")
                    ->orWhere('product_type', 'LIKE', "%{$this->search}%")
                    ->orWhere('unit_type', 'LIKE', "%{$this->search}%");
            });
    }
    public function getProductcats()
    {
        $return = [];

        foreach (Productcat::whereNull('productcat_id')->whereNull('pid')->get() as $value) {
            $return[$value->id] = $value->title;
        }

        return $return;
    }

    public function getOpenaiResult()
    {
        Flux::modals()->close();
        $ai = app(MantaOpenai::class);

        // geeft een directe URL terug naar de afbeelding

        $result = $ai->generate(
            $this->openaiSubject . ' ' . $this->openaiDescription,
            [
                'title' => 'Product naam',
                'slug' => 'URL-vriendelijke slug',
                'product_type' => 'Product type (bookable, sellable of both)',
            ]
        );

        $this->title = $result['title'];
        $this->slug = $result['slug'];
        $this->product_type = $result['product_type'];

        if ($this->openaiImageGenerate) {
            $ai->generateImage(
                $this->openaiSubject . ' ' . $this->openaiDescription,
                Product::class,
                'openai',
                '1024x1024'
            );
        }
    }

    /**
     * Reset variant form fields
     */
    public function resetVariantForm()
    {
        $this->variantTitle = '';
        $this->variantSku = '';
        $this->variantPrice = '';
        $this->variantActive = true;
    }

    /**
     * Save new variant
     */
    public function saveVariant()
    {
        $this->validate([
            'variantTitle' => 'required|string|max:255',
            'variantSku' => 'required|string|max:255|unique:manta_product_variants,sku',
            'variantPrice' => 'required|numeric|min:0',
        ], [
            'variantTitle.required' => 'Titel is verplicht',
            'variantSku.required' => 'SKU is verplicht',
            'variantSku.unique' => 'Deze SKU bestaat al',
            'variantPrice.required' => 'Prijs is verplicht',
            'variantPrice.numeric' => 'Prijs moet een geldig bedrag zijn',
        ]);

        // Check if we have a product (for create vs update)
        if (!$this->item) {
            \Flux\Flux::toast('Sla eerst het product op voordat je varianten toevoegt', duration: 3000, variant: 'warning');
            return;
        }

        // Create new variant
        $variant = $this->item->variants()->create([
            'title' => $this->variantTitle,
            'sku' => $this->variantSku,
            'price_override_excl' => $this->variantPrice,
            'active' => $this->variantActive,
        ]);

        // Reload variants
        $this->loadVariants();

        // Reset form and show success message
        $this->resetVariantForm();
        \Flux\Flux::toast('Variant toegevoegd', duration: 2000, variant: 'success');
        
        // Close modal via JavaScript
        $this->dispatch('close-modal', 'add-variant');
    }
}

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
    public ?string $pid = null;

    // Product eigenschappen
    public bool $active = true;
    public int $capacity = 1;
    public int $block_size = 1;
    public string $product_type = 'bookable'; // bookable|sellable|both
    public ?string $time_unit = null; // minute|day
    public ?int $resource_id = null;
    public ?string $title = null;
    public ?string $slug = null;

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
}

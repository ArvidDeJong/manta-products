<?php

namespace Darvis\MantaProduct\Traits;

use Darvis\MantaProduct\Models\ProductVariant;
use Flux\Flux;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Manta\FluxCMS\Models\MantaModule;
use Manta\FluxCMS\Services\MantaOpenai;
use Manta\FluxCMS\Services\ModuleSettingsService;
use Illuminate\Support\Str;

trait VariantTrait
{
    public function __construct()
    {
        $this->module_routes = [
            'name' => 'variant',
            'list' => 'variant.list',
            'create' => 'variant.create',
            'update' => 'variant.update',
            'read' => 'variant.read',
            'upload' => 'variant.upload',
            'settings' => 'variant.settings',
            'maps' => null,
        ];

        $settings = ModuleSettingsService::ensureModuleSettings('variant', 'darvis/manta-product');
        $this->config = $settings;

        $this->fields = $settings['fields'] ?? $this->getDefaultFields();
        $this->tab_title = $settings['tab_title'] ?? 'Varianten';
        $this->moduleClass = 'Darvis\MantaProduct\Models\ProductVariant';
        $this->openaiImagePossible = false;
    }

    // * Model items
    public ?ProductVariant $item = null;
    public ?ProductVariant $itemOrg = null;

    #[Locked]
    public ?string $company_id = null;

    #[Locked]
    public ?string $host = null;

    public ?string $locale = null;

    // Tab management
    public string $activeTab = 'general';
    public ?string $pid = null;

    // Variant eigenschappen
    public ?string $title = null;
    public ?string $sku = null;
    public bool $active = true;
    public int $capacity = 1;
    public ?float $price_override_excl = null;
    public int $stock_qty = 0;
    public ?float $tax_rate = null;
    public ?float $unit_step = null;
    public ?string $unit_type = null;
    public ?string $calc_mode = null;
    public ?float $wastage_pct = null;
    public ?string $rounding_mode = null;
    public ?int $length_mm = null;
    public ?int $width_mm = null;
    public ?int $height_mm = null;
    public ?string $variant_key = null;
    public ?int $product_id = null;

    #[Locked]
    public ?string $redirect_url = null;

    public function rules()
    {
        $return = [];
        if ($this->fields['title']) $return['title'] = 'required';
        if ($this->fields['sku']) $return['sku'] = 'nullable|string|max:255';
        if ($this->fields['capacity']) $return['capacity'] = 'required|integer|min:1';
        if ($this->fields['price_override_excl']) $return['price_override_excl'] = 'nullable|numeric|min:0';
        if ($this->fields['stock_qty']) $return['stock_qty'] = 'required|integer|min:0';
        if ($this->fields['tax_rate']) $return['tax_rate'] = 'nullable|numeric|min:0|max:100';
        if ($this->fields['unit_step']) $return['unit_step'] = 'nullable|numeric|min:0';
        if ($this->fields['wastage_pct']) $return['wastage_pct'] = 'nullable|numeric|min:0|max:100';
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
        $return['stock_qty.required'] = 'De voorraad is verplicht';
        $return['stock_qty.min'] = 'De voorraad moet 0 of hoger zijn';
        $return['price_override_excl.numeric'] = 'De prijs moet een getal zijn';
        $return['price_override_excl.min'] = 'De prijs moet 0 of hoger zijn';
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
                    ->orWhere('sku', 'LIKE', "%{$this->search}%")
                    ->orWhere('variant_key', 'LIKE', "%{$this->search}%");
            });
    }

    public function getProducts()
    {
        return \Darvis\MantaProduct\Models\Product::orderBy('title')->get();
    }

    public function fillFakeData()
    {
        $faker = \Faker\Factory::create('nl_NL');
        
        $this->title = $faker->randomElement([
            'Rood - Groot',
            'Blauw - Klein',
            'Groen - Medium',
            'Zwart - XL',
            'Wit - S',
            'Geel - L'
        ]);
        
        $this->sku = 'VAR-' . $faker->randomNumber(6);
        $this->active = $faker->boolean(90);
        $this->capacity = $faker->numberBetween(1, 10);
        $this->price_override_excl = $faker->randomFloat(2, 5, 100);
        $this->stock_qty = $faker->numberBetween(0, 100);
        $this->tax_rate = $faker->randomElement([21.00, 9.00, 0.00]);
        $this->unit_step = $faker->randomFloat(2, 0.1, 5);
        $this->unit_type = $faker->randomElement(['piece', 'meter', 'm2', 'm3']);
        $this->calc_mode = $faker->randomElement(['direct_length', 'dimensions_2d', 'dimensions_3d']);
        $this->wastage_pct = $faker->randomFloat(2, 0, 15);
        $this->rounding_mode = $faker->randomElement(['ceil', 'floor', 'round']);
        
        // Afmetingen
        $this->length_mm = $faker->numberBetween(100, 2000);
        $this->width_mm = $faker->numberBetween(50, 500);
        $this->height_mm = $faker->numberBetween(5, 100);
    }

    public function generateFakeData()
    {
        $this->fillFakeData();
        Flux::toast('Fake data gegenereerd!', duration: 2000, variant: 'success');
    }

    public function getOpenaiResult()
    {
        Flux::modals()->close();
        $ai = app(MantaOpenai::class);

        $result = $ai->generate(
            $this->openaiSubject . ' ' . $this->openaiDescription,
            [
                'title' => 'Variant naam',
                'sku' => 'Product code',
                'capacity' => 'Capaciteit (getal)',
            ]
        );

        $this->title = $result['title'];
        $this->sku = $result['sku'];
        $this->capacity = (int) $result['capacity'];
    }

    private function getDefaultFields(): array
    {
        return [
            'title' => ['active' => true, 'required' => true],
            'sku' => ['active' => true, 'required' => false],
            'capacity' => ['active' => true, 'required' => true],
            'price_override_excl' => ['active' => true, 'required' => false],
            'stock_qty' => ['active' => true, 'required' => true],
            'tax_rate' => ['active' => true, 'required' => false],
            'unit_step' => ['active' => true, 'required' => false],
            'unit_type' => ['active' => true, 'required' => false],
            'calc_mode' => ['active' => true, 'required' => false],
            'wastage_pct' => ['active' => true, 'required' => false],
            'rounding_mode' => ['active' => true, 'required' => false],
            'length_mm' => ['active' => true, 'required' => false],
            'width_mm' => ['active' => true, 'required' => false],
            'height_mm' => ['active' => true, 'required' => false],
        ];
    }
}

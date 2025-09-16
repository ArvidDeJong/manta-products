<?php

namespace Darvis\MantaProduct\Traits;

use Darvis\MantaProduct\Models\Attribute;
use Flux\Flux;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Manta\FluxCMS\Models\MantaModule;
use Manta\FluxCMS\Services\MantaOpenai;
use Manta\FluxCMS\Services\ModuleSettingsService;
use Illuminate\Support\Str;

trait AttributeTrait
{
    public function __construct()
    {
        $this->module_routes = [
            'name' => 'attribute',
            'list' => 'attribute.list',
            'create' => 'attribute.create',
            'update' => 'attribute.update',
            'read' => 'attribute.read',
            'upload' => 'attribute.upload',
            'settings' => 'attribute.settings',
            'maps' => null,
        ];

        $settings = ModuleSettingsService::ensureModuleSettings('attribute', 'darvis/manta-product');
        $this->config = $settings;

        $this->fields = $settings['fields'] ?? [];
        $this->tab_title = $settings['tab_title'] ?? null;
        $this->moduleClass = 'Darvis\MantaProduct\Models\Attribute';
        $this->openaiImagePossible = false;
    }

    // * Model items
    public ?Attribute $item = null;
    public ?Attribute $itemOrg = null;

    #[Locked]
    public ?string $company_id = null;

    #[Locked]
    public ?string $host = null;

    public ?string $locale = null;

    // Attribute eigenschappen
    public ?string $code = null;
    public ?string $name = null;
    public ?string $type = null;
    public ?int $sort = null;
    public array $config = [];

    #[Locked]
    public ?string $redirect_url = null;

    // Config properties voor verschillende types
    public ?string $configJson = null;
    public ?int $configMin = null;
    public ?int $configMax = null;
    public ?float $configStep = null;
    public ?string $configUnit = null;
    public ?int $configMaxLength = null;
    public ?string $configPlaceholder = null;

    public function rules()
    {
        $return = [];
        if ($this->fields['code']) $return['code'] = 'required|string|max:255';
        if ($this->fields['name']) $return['name'] = 'required|string|max:255';
        if ($this->fields['type']) $return['type'] = 'required|string|max:255';
        if ($this->fields['sort']) $return['sort'] = 'nullable|integer|min:0';

        if (isset($this->fields['code']) && $this->fields['code']['active'] == true) {
            if ($this->item) {
                $return['code'] = $this->fields['code']['required'] == true ? 'required|string|max:255|unique:manta_attributes,code,' . $this->item->id : 'nullable|string|max:255|unique:manta_attributes,code';
            } else {
                $return['code'] = $this->fields['code']['required'] == true ? 'required|string|max:255|unique:manta_attributes,code' : 'nullable|string|max:255|unique:manta_attributes,code';
            }
        }

        return $return;
    }

    public function messages()
    {
        $return = [];
        $return['code.required'] = 'De code is verplicht';
        $return['code.unique'] = 'Deze code bestaat al';
        $return['name.required'] = 'De naam is verplicht';
        $return['type.required'] = 'Het type is verplicht';
        $return['sort.integer'] = 'De sortering moet een getal zijn';
        $return['sort.min'] = 'De sortering moet 0 of hoger zijn';
        return $return;
    }

    protected function applySearch($query)
    {
        return $this->search === ''
            ? $query
            : $query->where(function (Builder $querysub) {
                $querysub->where('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('code', 'LIKE', "%{$this->search}%")
                    ->orWhere('type', 'LIKE', "%{$this->search}%");
            });
    }

    public function getOpenaiResult()
    {
        Flux::modals()->close();
        $ai = app(MantaOpenai::class);

        $result = $ai->generate(
            $this->openaiSubject . ' ' . $this->openaiDescription,
            [
                'name' => 'Attribute naam',
                'code' => 'Unieke code voor het attribuut',
                'type' => 'Type van het attribuut (text, number, select, etc.)',
            ]
        );

        $this->name = $result['name'];
        $this->code = $result['code'];
        $this->type = $result['type'];
    }

    private function fillFakeData()
    {
        $faker = \Faker\Factory::create('nl_NL');

        // Attribute basis informatie
        $attributeTypes = ['text', 'number', 'select', 'multiselect', 'boolean', 'date', 'textarea'];
        $attributeNames = [
            'Kleur',
            'Materiaal',
            'Afmeting',
            'Gewicht',
            'Merk',
            'Model',
            'Categorie',
            'Stijl',
            'Afwerking',
            'Dikte'
        ];

        $name = $faker->randomElement($attributeNames);
        $this->name = $name;
        $this->code = Str::slug($name, '_');
        $this->type = $faker->randomElement($attributeTypes);
        $this->sort = $faker->numberBetween(1, 100);

        // Basis config afhankelijk van type
        $this->config = match ($this->type) {
            'select', 'multiselect' => [
                'options' => [
                    'rood' => 'Rood',
                    'blauw' => 'Blauw',
                    'groen' => 'Groen',
                    'geel' => 'Geel'
                ]
            ],
            'number' => [
                'min' => 0,
                'max' => 1000,
                'step' => 1,
                'unit' => 'cm'
            ],
            'text', 'textarea' => [
                'max_length' => 255,
                'placeholder' => 'Voer ' . strtolower($name) . ' in...'
            ],
            default => []
        };
    }

    public function generateFakeData()
    {
        $this->fillFakeData();
        \Flux\Flux::toast('Fake data gegenereerd!', duration: 2000, variant: 'success');
    }

    public function updatedName($value)
    {
        if (!$this->code && $value) {
            $this->code = \Illuminate\Support\Str::slug($value, '_');
        }
    }

    public function updatedType($value)
    {
        // Reset config when type changes
        $this->resetConfigFields();

        // Set default values based on type
        match ($value) {
            'number' => [
                $this->configMin = 0,
                $this->configMax = 1000,
                $this->configStep = 1
            ],
            'text', 'textarea' => [
                $this->configMaxLength = 255,
                $this->configPlaceholder = 'Voer ' . strtolower($this->name ?: 'waarde') . ' in...'
            ],
            'select', 'multiselect' => [
                $this->configJson = '{"optie1": "Optie 1", "optie2": "Optie 2"}'
            ],
            default => null
        };
    }

    private function resetConfigFields()
    {
        $this->configJson = null;
        $this->configMin = null;
        $this->configMax = null;
        $this->configStep = null;
        $this->configUnit = null;
        $this->configMaxLength = null;
        $this->configPlaceholder = null;
    }

    private function buildConfig(): array
    {
        return match ($this->type) {
            'select', 'multiselect' => [
                'options' => $this->configJson ? json_decode($this->configJson, true) : []
            ],
            'number' => array_filter([
                'min' => $this->configMin,
                'max' => $this->configMax,
                'step' => $this->configStep,
                'unit' => $this->configUnit
            ]),
            'text', 'textarea' => array_filter([
                'max_length' => $this->configMaxLength,
                'placeholder' => $this->configPlaceholder
            ]),
            default => []
        };
    }
}

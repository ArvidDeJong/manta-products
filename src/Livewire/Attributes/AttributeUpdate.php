<?php

namespace Darvis\MantaProduct\Livewire\Attributes;

use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Traits\AttributeTrait;
use Flux\Flux;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class AttributeUpdate extends Component
{
    use MantaTrait, AttributeTrait;

    // Config properties voor verschillende types
    public ?string $configJson = null;
    public ?int $configMin = null;
    public ?int $configMax = null;
    public ?float $configStep = null;
    public ?string $configUnit = null;
    public ?int $configMaxLength = null;
    public ?string $configPlaceholder = null;

    public function mount(Attribute $attribute)
    {
        $this->item = $attribute;
        $this->itemOrg = $attribute;
        $this->id = $attribute->id;

        $this->fill(
            $attribute->only(
                'company_id',
                'locale',
                'code',
                'name',
                'type',
                'sort'
            )
        );
        
        // Load config into separate properties
        $this->loadConfigFromAttribute($attribute);
        
        $this->getLocaleInfo();
        $this->getBreadcrumb('update');
        $this->getTablist();
    }

    private function loadConfigFromAttribute(Attribute $attribute)
    {
        $config = $attribute->config ?? [];
        
        match($attribute->type) {
            'select', 'multiselect' => [
                $this->configJson = isset($config['options']) ? json_encode($config['options'], JSON_PRETTY_PRINT) : null
            ],
            'number' => [
                $this->configMin = $config['min'] ?? null,
                $this->configMax = $config['max'] ?? null,
                $this->configStep = $config['step'] ?? null,
                $this->configUnit = $config['unit'] ?? null
            ],
            'text', 'textarea' => [
                $this->configMaxLength = $config['max_length'] ?? null,
                $this->configPlaceholder = $config['placeholder'] ?? null
            ],
            default => null
        };
    }

    public function updatedType($value)
    {
        // Reset config when type changes
        $this->resetConfigFields();
        
        // Set default values based on type
        match($value) {
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

    public function render()
    {
        return view('manta-product::livewire.attributes.attribute-update');
    }

    public function save()
    {
        $this->validate();

        // Build config array based on type
        $config = $this->buildConfig();

        $row = $this->only(
            'company_id',
            'locale',
            'code',
            'name',
            'type',
            'sort'
        );
        $row['config'] = $config;
        $row['updated_by'] = auth('staff')->user()->name;
        
        Attribute::where('id', $this->id)->update($row);

        Flux::toast('Opgeslagen', duration: 1000, variant: 'success');
    }

    private function buildConfig(): array
    {
        return match($this->type) {
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

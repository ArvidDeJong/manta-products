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

        match ($attribute->type) {
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


    public function render()
    {
        return view('manta-product::livewire.attributes.attribute-update');
    }

    public function save()
    {
        $this->saveAttribute(true);
    }

}

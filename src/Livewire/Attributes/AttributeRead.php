<?php

namespace Darvis\MantaProduct\Livewire\Attributes;

use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Traits\AttributeTrait;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class AttributeRead extends Component
{
    use MantaTrait, AttributeTrait;

    public function mount(Attribute $attribute)
    {
        $this->item = $attribute;
        $this->itemOrg = $attribute;
        $this->id = $attribute->id;
        $this->locale = $attribute->locale;

        $this->getLocaleInfo();
        $this->getBreadcrumb('read');
        $this->getTablist();
    }

    public function render()
    {
        return view('manta-product::livewire.attributes.attribute-read');
    }
}

<?php

namespace Darvis\MantaProduct\Livewire\Variants;

use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Traits\VariantTrait;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class VariantUpload extends Component
{
    use MantaTrait, VariantTrait;

    public function mount(ProductVariant $variant)
    {
        $this->item = $variant;
        $this->itemOrg = $variant;
        $this->id = $variant->id;

        $this->getLocaleInfo();
        $this->getBreadcrumb('upload');
        $this->getTablist();
    }

    public function render()
    {
        return view('manta-cms::livewire.default.manta-default-upload');
    }
}

<?php

namespace Darvis\MantaProduct\Livewire\Variants;

use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Traits\VariantTrait;
use Flux\Flux;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class VariantUpdate extends Component
{
    use MantaTrait, VariantTrait;

    public function mount(ProductVariant $variant)
    {
        $this->item = $variant;
        $this->itemOrg = $variant;
        $this->id = $variant->id;

        $this->fill(
            $variant->only(
                'product_id',
                'title',
                'sku',
                'active',
                'capacity',
                'price_override_excl',
                'stock_qty',
                'tax_rate',
                'unit_step',
                'unit_type',
                'calc_mode',
                'wastage_pct',
                'rounding_mode',
                'length_mm',
                'width_mm',
                'height_mm',
                'variant_key'
            )
        );

        $this->getLocaleInfo();
        $this->getBreadcrumb('update');
        $this->getTablist();
    }

    public function render()
    {
        return view('manta-product::livewire.variants.variant-update');
    }

    public function save()
    {
        $this->validate();

        $row = $this->only(
            'product_id',
            'title',
            'sku',
            'active',
            'capacity',
            'price_override_excl',
            'stock_qty',
            'tax_rate',
            'unit_step',
            'unit_type',
            'calc_mode',
            'wastage_pct',
            'rounding_mode',
            'length_mm',
            'width_mm',
            'height_mm',
        );
        
        $row['updated_by'] = auth('staff')->user()->name;
        ProductVariant::where('id', $this->id)->update($row);

        Flux::toast('Opgeslagen', duration: 1000, variant: 'success');
    }
}

<?php

namespace Darvis\MantaProduct\Livewire\Variants;

use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Traits\VariantTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class VariantCreate extends Component
{
    use MantaTrait, VariantTrait;

    public function mount(Request $request)
    {
        $this->locale = getLocaleManta();
        
        // Als er een product_id wordt meegegeven
        if ($request->input('product_id')) {
            $this->product_id = $request->input('product_id');
        }

        $this->getLocaleInfo();
        $this->getTablist();
        $this->getBreadcrumb('create');

        // Faker data voor development
        if (env('USE_FAKER', false)) {
            $this->fillFakeData();
        }
    }

    public function render()
    {
        return view('manta-product::livewire.variants.variant-create');
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
        
        // Genereer variant key
        $row['variant_key'] = $this->sku ?: Str::random(8);
        $row['created_by'] = auth('staff')->user()->name;
        $row['host'] = request()->host();
        
        ProductVariant::create($row);

        return $this->redirect(route('variant.list'));
    }
}

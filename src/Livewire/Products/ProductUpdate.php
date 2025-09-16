<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Traits\ProductTrait;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class ProductUpdate extends Component
{
    use MantaTrait, ProductTrait;

    public function mount(Product $product)
    {
        $this->item = $product;
        $this->itemOrg = translate($product, 'nl')['org'];
        $this->id = $product->id;

        $this->fill(
            $product->only(
                'company_id',
                'pid',
                'locale',
                'active',
                'capacity',
                'block_size',
                'product_type',
                'time_unit',
                'resource_id',
                'title',
                'slug',
                'unit_type',
                'unit_step',
                'min_order_qty',
                'max_order_qty',
                'price_per_unit',
                'tax_rate',
                'calc_mode',
                'wastage_pct',
                'rounding_mode',
                'length_mm',
                'width_mm',
                'height_mm',
                'dimension_unit',
            ),
        );
        $this->getLocaleInfo();
        $this->getBreadcrumb('update');
        $this->getTablist();
        $this->loadAttributes();
        $this->loadVariants();
    }

    public function render()
    {
        return view('manta-product::livewire.products.product-update');
    }

    public function save()
    {
        $this->validate();

        $row = $this->only(
            'company_id',
            'pid',
            'locale',
            'active',
            'capacity',
            'block_size',
            'product_type',
            'time_unit',
            'resource_id',
            'title',
            'slug',
            'unit_type',
            'unit_step',
            'min_order_qty',
            'max_order_qty',
            'price_per_unit',
            'tax_rate',
            'calc_mode',
            'wastage_pct',
            'rounding_mode',
            'length_mm',
            'width_mm',
            'height_mm',
            'dimension_unit',
        );
        $row['updated_by'] = auth('staff')->user()->name;
        Product::where('id', $this->id)->update($row);

        // Sla product attributen op
        $this->saveProductAttributes();

        Flux::toast('Opgeslagen', duration: 1000, variant: 'success');
    }
}

<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Traits\ProductTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class ProductCreate extends Component
{
    use MantaTrait, ProductTrait;

    public function mount(Request $request)
    {
        $this->locale = getLocaleManta();
        if ($request->input('locale') && $request->input('pid')) {
            $item = Product::find($request->input('pid'));
            $this->pid = $item->id;
            $this->locale = $request->input('locale');
            $this->itemOrg = $item;
        }

        $this->getLocaleInfo();
        $this->getTablist();
        $this->getBreadcrumb('create');
        $this->loadAttributes();
        $this->loadVariants();

        // Faker data voor development
        if (env('USE_FAKER', false)) {
            $this->fillFakeData();
        }
    }

    private function fillFakeData()
    {
        $faker = \Faker\Factory::create('nl_NL');
        
        // Product basis informatie
        $productNames = [
            'Premium Houten Vloer',
            'Luxe Keramische Tegels',
            'Moderne Laminaat Planken',
            'Natuursteen Wandbekleding',
            'Vinyl Click Vloer',
            'Bamboe Parket',
            'Industriële Betonlook Tegels',
            'Klassieke Eiken Planken'
        ];

        $this->title = $faker->randomElement($productNames) . ' ' . $faker->randomNumber(3);
        $this->slug = \Illuminate\Support\Str::slug($this->title);
        
        // Product eigenschappen
        $this->active = $faker->boolean(90); // 90% kans op actief
        $this->capacity = $faker->numberBetween(1, 10);
        $this->block_size = $faker->numberBetween(1, 5);
        $this->product_type = $faker->randomElement(['bookable', 'sellable', 'both']);
        $this->time_unit = $this->product_type === 'sellable' ? null : $faker->randomElement(['minute', 'day']);
        
        // Unit pricing
        $this->unit_type = $faker->randomElement(['piece', 'meter', 'm2', 'm3']);
        $this->unit_step = $faker->randomFloat(2, 0.1, 10);
        $this->min_order_qty = $faker->randomFloat(2, 1, 5);
        $this->max_order_qty = $faker->randomFloat(2, 10, 100);
        $this->price_per_unit = $faker->randomFloat(2, 5, 150);
        $this->tax_rate = $faker->randomElement([21.00, 9.00, 0.00]); // Nederlandse BTW tarieven
        $this->calc_mode = $faker->randomElement(['direct_length', 'dimensions_2d', 'dimensions_3d']);
        $this->wastage_pct = $faker->randomFloat(2, 5, 15);
        $this->rounding_mode = $faker->randomElement(['ceil', 'floor', 'round']);
        
        // Afmetingen (in mm)
        if ($this->unit_type === 'm2' || $this->calc_mode === 'dimensions_2d') {
            $this->length_mm = $faker->numberBetween(200, 2000);
            $this->width_mm = $faker->numberBetween(100, 500);
            $this->height_mm = $faker->numberBetween(5, 50);
        } elseif ($this->unit_type === 'm3' || $this->calc_mode === 'dimensions_3d') {
            $this->length_mm = $faker->numberBetween(500, 3000);
            $this->width_mm = $faker->numberBetween(200, 800);
            $this->height_mm = $faker->numberBetween(50, 300);
        } else {
            $this->length_mm = $faker->numberBetween(100, 1000);
            $this->width_mm = $faker->numberBetween(50, 200);
            $this->height_mm = $faker->numberBetween(10, 100);
        }
        
        $this->dimension_unit = 'mm';
    }

    public function generateFakeData()
    {
        $this->fillFakeData();
        \Flux\Flux::toast('Fake data gegenereerd!', duration: 2000, variant: 'success');
    }

    public function render()
    {
        return view('manta-product::livewire.products.product-create');
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
        $row['created_by'] = auth('staff')->user()->name;
        $row['host'] = request()->host();
        $row['slug'] = $this->slug ? $this->slug : Str::of($this->title)->slug('-');
        $product = Product::create($row);

        // Zet het nieuwe product als item voor attributen opslaan
        $this->item = $product;
        
        // Sla product attributen op
        $this->saveProductAttributes();

        return $this->redirect(ProductList::class);
    }
}

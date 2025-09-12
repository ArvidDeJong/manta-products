<?php

namespace Darvis\MantaProduct\Livewire\Demo;

use Livewire\Component;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Services\SlotGeneratorService;
use Carbon\Carbon;

class SlotList extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        $from = Carbon::now()->startOfDay();
        $to   = Carbon::now()->addDays(7)->endOfDay();
        $slots = app(SlotGeneratorService::class)->slots($this->product, $from, $to);

        return view('manta-products::livewire.demo.slots', compact('slots', 'from', 'to'))
            ->layout('manta-cms::layouts.app');
    }
}

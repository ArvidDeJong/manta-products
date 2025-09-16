<?php

namespace Darvis\MantaProduct\Livewire\Attributes;

use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Traits\AttributeTrait;
use Illuminate\Http\Request;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;

#[Layout('manta-cms::layouts.app')]
class AttributeCreate extends Component
{
    use MantaTrait, AttributeTrait;

    public function mount(Request $request)
    {
        $this->locale = getLocaleManta();

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
        return view('manta-product::livewire.attributes.attribute-create');
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
        $row['created_by'] = auth('staff')->user()->name;
        $row['host'] = request()->host();

        Attribute::create($row);

        return $this->redirect(AttributeList::class);
    }
}

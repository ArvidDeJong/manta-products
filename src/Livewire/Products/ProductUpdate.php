<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Traits\ProductTrait;
use Darvis\MantaProduct\Traits\GeneratesSkuTrait;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Component;
use Manta\FluxCMS\Traits\MantaTrait;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('manta-cms::layouts.app')]
class ProductUpdate extends Component
{
    use MantaTrait, ProductTrait, WithFileUploads, GeneratesSkuTrait;

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
                'title_2',
                'title_3',
                'slug',
                'excerpt',
                'description',
                'description_2',
                'description_3',
                'comments',
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
        $this->loadCategories();
        $this->loadUploads();
        $this->sortExistingUploadsByTimestamp();
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
            'title_2',
            'title_3',
            'slug',
            'excerpt',
            'description',
            'description_2',
            'description_3',
            'comments',
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
        
        // Sla categorieën op
        $this->saveCategories();
        
        // Sla uploads op
        $this->saveUploads();

        Flux::toast('Opgeslagen', duration: 1000, variant: 'success');
    }

    public function sortUploads(int $uploadId, int $position)
    {
        // Vind de upload in de existingUploads array
        $uploadIndex = collect($this->existingUploads)->search(function ($upload) use ($uploadId) {
            return $upload['id'] == $uploadId;
        });

        if ($uploadIndex !== false) {
            // Verwijder het item van de huidige positie
            $upload = array_splice($this->existingUploads, $uploadIndex, 1)[0];
            
            // Voeg het item toe op de nieuwe positie
            array_splice($this->existingUploads, $position, 0, [$upload]);
            
            // Update de volgorde in de pivot tabel (als die bestaat) of sla de nieuwe volgorde op
            $this->updateUploadOrder();
        }
    }

    private function updateUploadOrder()
    {
        // Update de volgorde van uploads door de updated_at timestamp te gebruiken
        // Dit zorgt ervoor dat de volgorde behouden blijft zonder extra database kolommen
        foreach ($this->existingUploads as $index => $upload) {
            // Update de timestamp met een kleine offset om volgorde te behouden
            $timestamp = now()->addSeconds($index);
            
            \Manta\FluxCMS\Models\Upload::where('id', $upload['id'])
                ->update(['updated_at' => $timestamp]);
        }
        
        // De existingUploads array is al in de juiste volgorde door de sortUpload methode
        // Geen extra sortering nodig - de interface toont direct de nieuwe volgorde
    }

    private function sortExistingUploadsByTimestamp()
    {
        // Sorteer de bestaande uploads op updated_at timestamp (ASC)
        // Dit zorgt ervoor dat de volgorde behouden blijft bij het laden van de pagina
        if (!empty($this->existingUploads)) {
            // Haal de volledige Upload models op om toegang te krijgen tot updated_at
            // Zorg ervoor dat alleen niet-verwijderde uploads worden opgehaald
            $uploadIds = collect($this->existingUploads)->pluck('id')->toArray();
            $uploads = \Manta\FluxCMS\Models\Upload::whereIn('id', $uploadIds)
                ->whereNull('deleted_at')  // Alleen niet-verwijderde uploads
                ->orderBy('updated_at', 'asc')
                ->get()
                ->keyBy('id');

            // Filter eerst de existingUploads om soft-deleted items te verwijderen
            $this->existingUploads = collect($this->existingUploads)
                ->filter(function ($upload) use ($uploads) {
                    return isset($uploads[$upload['id']]);
                })
                ->values()
                ->toArray();

            // Sorteer de existingUploads array op basis van de database volgorde
            usort($this->existingUploads, function ($a, $b) use ($uploads) {
                $uploadA = $uploads[$a['id']] ?? null;
                $uploadB = $uploads[$b['id']] ?? null;
                
                if (!$uploadA || !$uploadB) {
                    return 0;
                }
                
                return $uploadA->updated_at <=> $uploadB->updated_at;
            });
        }
    }

    public function deleteUpload(int $uploadId)
    {
        // Soft delete: markeer de upload als verwijderd in de database
        $uploadModel = \Manta\FluxCMS\Models\Upload::find($uploadId);
        
        if ($uploadModel) {
            // Gebruik Laravel's soft delete functionaliteit
            $uploadModel->delete();
            
            // Verwijder ook uit de existingUploads array voor directe UI feedback
            $this->existingUploads = collect($this->existingUploads)
                ->reject(function ($upload) use ($uploadId) {
                    return $upload['id'] == $uploadId;
                })
                ->values()
                ->toArray();
                
            // Toon bevestiging
            \Flux\Flux::toast('Bestand verwijderd', duration: 2000, variant: 'success');
        }
    }

}

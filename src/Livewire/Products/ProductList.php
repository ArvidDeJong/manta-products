<?php

namespace Darvis\MantaProduct\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Darvis\MantaProduct\Models\Product;
use Flux\Flux;

class ProductList extends Component
{
    use WithPagination;
    public ?string $deleteId = null;

    #[Url]
    public string $search = '';

    #[Url]
    public int $perPage = 10;

    #[Url]
    public bool $withTrashed = false;

    public string $sortBy = 'title';
    public string $sortDirection = 'asc';

    public function showDeleteModal($id)
    {
        $this->deleteId = $id;
        Flux::modal('delete-product-modal')->open();
    }

    public function doSort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete()
    {
        Product::find($this->deleteId)->delete();
        Flux::modal('delete-product-modal')->close();
    }

    public function restore($id)
    {
        Product::withTrashed()->find($id)->restore();
    }

    public function render()
    {
        return view('manta-products::livewire.products.product-list', [
            'products' => Product::query()
                ->when($this->withTrashed, fn($query) => $query->withTrashed())
                ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%'))
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ])->layout('manta-cms::layouts.app');
    }
}

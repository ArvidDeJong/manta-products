<div class="p-6 space-y-4">
    <flux:header>
        <x-slot:title>
            <h1 class="text-2xl font-bold">Producten</h1>
        </x-slot:title>
        <x-slot:actions>
            <div class="flex items-center space-x-4">
                <flux:select wire:model.live="perPage">
                    <flux:select.option :value="10">10</flux:select.option>
                    <flux:select.option :value="25">25</flux:select.option>
                    <flux:select.option :value="50">50</flux:select.option>
                    <flux:select.option :value="100">100</flux:select.option>
                </flux:select>
                <flux:input wire:model.live="search" placeholder="Zoeken..." />
                <flux:checkbox wire:model.live="withTrashed" label="Met verwijderde" />
                <flux:button href="{{ route('manta-products.demo.products.create') }}" icon="plus">Toevoegen</flux:button>
            </div>
        </x-slot:actions>
    </flux:header>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="doSort('title')">Titel</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'product_type'" :direction="$sortDirection" wire:click="doSort('product_type')">Type</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'unit_type'" :direction="$sortDirection" wire:click="doSort('unit_type')">Unit</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'block_size'" :direction="$sortDirection" wire:click="doSort('block_size')">Block Size</flux:table.column>
            <flux:table.column />
        </flux:table.columns>
        <flux:table.rows>
            @foreach($products as $product)
                <flux:table.row>
                    <flux:table.cell>{{ $product->title }}</flux:table.cell>
                    <flux:table.cell>{{ $product->product_type }}</flux:table.cell>
                    <flux:table.cell>{{ $product->unit_type }}</flux:table.cell>
                    <flux:table.cell>{{ $product->block_size }} {{ $product->time_unit }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button href="{{ route('manta-products.demo.products.update', $product) }}" icon="pencil-solid" size="sm" variant="subtle" />
                        <flux:button href="{{ route('manta-products.demo.slots', $product) }}">Bekijk slots</flux:button>
                        @if($product->trashed())
                            <flux:button wire:click="restore('{{ $product->id }}')" icon="arrow-path" size="sm" variant="primary" />
                        @else
                            <flux:button wire:click="showDeleteModal('{{ $product->id }}')" icon="trash" size="sm" variant="danger" />
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="delete-product-modal">
        <flux:modal.header>
            <flux:heading>Product Verwijderen</flux:heading>
        </flux:modal.header>
        <flux:modal.content>
            <p>Weet je zeker dat je dit product wilt verwijderen?</p>
        </flux:modal.content>
        <flux:modal.actions>
            <flux:button wire:click="delete" variant="danger">Verwijderen</flux:button>
            <flux:modal.closer>
                <flux:button variant="secondary">Annuleren</flux:button>
            </flux:modal.closer>
        </flux:modal.actions>
    </flux:modal>
</div>

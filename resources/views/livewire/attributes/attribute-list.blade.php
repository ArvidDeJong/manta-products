<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:button icon="plus" href="{{ route($this->module_routes['create']) }}">
                Toevoegen
            </flux:button>

            <flux:button icon="list-bullet" href="{{ route('product.list') }}">
                Producten
            </flux:button>
        </div>
        <div style="width: 300px">
            <flux:input type="search" wire:model="search" placeholder="Zoeken..." />
        </div>
    </div>
    <x-manta.tables.tabs :$tablistShow :$trashed />

    <flux:table :paginate="$items">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="dosort('name')">
                Naam
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'code'" :direction="$sortDirection"
                wire:click="dosort('code')">
                Code
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'type'" :direction="$sortDirection"
                wire:click="dosort('type')">
                Type
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'sort'" :direction="$sortDirection"
                wire:click="dosort('sort')">
                Sortering
            </flux:table.column>

            <flux:table.column>
                Waarden
            </flux:table.column>

            <flux:table.column />
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($items as $item)
                <flux:table.row data-id="{{ $item->id }}">
                    <flux:table.cell>{{ $item->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="outline">{{ $item->code }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge>{{ $item->type }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->sort }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="outline">{{ $item->values->count() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button size="sm" href="{{ route($this->module_routes['read'], $item) }}"
                            icon="eye" />
                        <x-manta.tables.delete-modal :item="$item" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

</flux:main>

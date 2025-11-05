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

            <flux:table.column sortable :sorted="$sortBy === 'parent_id'" :direction="$sortDirection"
                wire:click="dosort('parent_id')">
                Hoofdcategorie
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'sort'" :direction="$sortDirection"
                wire:click="dosort('sort')">
                Sortering
            </flux:table.column>

            <flux:table.column>
                Subcategorieën
            </flux:table.column>

            <flux:table.column>
                Producten
            </flux:table.column>

            <flux:table.column sortable :sorted="$sortBy === 'active'" :direction="$sortDirection"
                wire:click="dosort('active')">
                Status
            </flux:table.column>

            <flux:table.column />
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($items as $item)
                <flux:table.row data-id="{{ $item->id }}">
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            @if($item->parent_id)
                                <span class="text-gray-400">└─</span>
                            @endif
                            {{ $item->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($item->parent)
                            <flux:badge variant="outline">{{ $item->parent->name }}</flux:badge>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $item->sort }}</flux:table.cell>
                    <flux:table.cell>
                        @if($item->children->count() > 0)
                            <flux:badge variant="outline">{{ $item->children->count() }}</flux:badge>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge variant="outline">{{ $item->products->count() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($item->active)
                            <flux:badge color="green">Actief</flux:badge>
                        @else
                            <flux:badge color="red">Inactief</flux:badge>
                        @endif
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

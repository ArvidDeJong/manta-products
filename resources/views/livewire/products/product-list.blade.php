<flux:main>
    <x-manta.breadcrumb :$breadcrumb />
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:button icon="plus" href="{{ route($this->module_routes['create']) }}">
                Toevoegen
            </flux:button>

            <flux:button icon="list-bullet" href="{{ route('attribute.list') }}">
                Eigenschappen
            </flux:button>

            <flux:button icon="list-bullet" href="{{ route('variant.list') }}">
                Varianten
            </flux:button>
        </div>
        <div style="width: 300px">
            <flux:input type="search" wire:model="search" placeholder="Zoeken..." />
        </div>
    </div>
    <x-manta.tables.tabs :$tablistShow :$trashed />

    <flux:table :paginate="$items">
        <flux:table.columns>
            <flux:table.column>
                <flux:icon.photo />
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection"
                wire:click="dosort('title')">
                Titel</flux:table.column>

            @if ($fields['slug']['active'])
                <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection"
                    wire:click="dosort('slug')">
                    Slug
                </flux:table.column>
            @endif
            <flux:table.column>Categorieën</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Prijs</flux:table.column>
            <flux:table.column><flux:icon.document-duplicate /></flux:table.column>
            <flux:table.column />
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($items as $item)
                <flux:table.row data-id="{{ $item->id }}">

                    @if ($this->fields['uploads'] && $this->fields['uploads']['active'])
                        <flux:table.cell><x-manta.tables.image :item="$item->image" /></flux:table.cell>
                    @endif
                    <flux:table.cell>{{ $item->title }}</flux:table.cell>
                    @if ($this->fields['slug']['active'])
                        <flux:table.cell>
                            {{ $item->slug }}
                        </flux:table.cell>
                    @endif

                    <flux:table.cell>
                        @if ($item->categories->count() > 0)
                            @foreach ($item->categories->take(4) as $category)
                                <flux:badge size="sm" color="zinc" class="mb-1 mr-1">{{ $category->name }}
                                </flux:badge>
                            @endforeach
                            @if ($item->categories->count() > 4)
                                <flux:badge size="sm" color="gray" class="mb-1 mr-1">
                                    +{{ $item->categories->count() - 4 }}</flux:badge>
                            @endif
                        @else
                            <span class="text-sm text-gray-400">Geen categorieën</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($item->product_type)
                            @php
                                $typeLabels = [
                                    'bookable' => 'Boekbaar',
                                    'sellable' => 'Verkoopbaar', 
                                    'both' => 'Beide'
                                ];
                                $typeColors = [
                                    'bookable' => 'blue',
                                    'sellable' => 'green',
                                    'both' => 'purple'
                                ];
                            @endphp
                            <flux:badge size="sm" color="{{ $typeColors[$item->product_type] ?? 'gray' }}">
                                {{ $typeLabels[$item->product_type] ?? $item->product_type }}
                            </flux:badge>
                        @else
                            <span class="text-gray-400 text-sm">Geen type</span>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        @if($item->isGiftCard())
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" color="yellow">Cadeaubon</flux:badge>
                                @if($item->variants->count() > 0)
                                    <span class="text-sm font-medium">{{ $item->price_display }}</span>
                                @endif
                            </div>
                        @else
                            <div>
                                <span class="font-medium">{{ $item->price_display }}</span>
                                @if($item->variants->count() > 0)
                                    <span class="text-sm text-gray-500 block">{{ $item->variants->count() }} {{ $item->variants->count() === 1 ? 'optie' : 'opties' }}</span>
                                @endif
                            </div>
                        @endif
                    </flux:table.cell>

                    @if (isset($this->fields['uploads']) && $this->fields['uploads']['active'])
                        <flux:table.cell>{{ count($item->images) > 0 ? count($item->images) : null }}</flux:table.cell>
                    @endif

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

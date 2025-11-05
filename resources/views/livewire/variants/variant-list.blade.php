<flux:main>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $this->tab_title }}</flux:heading>
            <flux:subheading>Beheer product varianten</flux:subheading>
        </div>
        <div class="flex items-center space-x-2">
            <flux:button icon="plus" href="{{ route($this->module_routes['create']) }}">
                Toevoegen
            </flux:button>
            <flux:button icon="list-bullet" href="{{ route('product.list') }}">
                Producten
            </flux:button>
            <flux:button icon="list-bullet" href="{{ route('product.list') }}">
                Producten
            </flux:button>
        </div>
        <div style="width: 300px">
            <flux:input type="search" wire:model="search" placeholder="Zoeken..." />
        </div>
    </div>
    <flux:table :paginate="$items">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection"
                wire:click="dosort('title')">
                Titel
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'sku'" :direction="$sortDirection"
                wire:click="dosort('sku')">
                SKU
            </flux:table.column>
            <flux:table.column>
                Product
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'active'" :direction="$sortDirection"
                wire:click="dosort('active')">
                Status
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'stock_qty'" :direction="$sortDirection"
                wire:click="dosort('stock_qty')">
                Voorraad
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'price_override_excl'" :direction="$sortDirection"
                wire:click="dosort('price_override_excl')">
                Prijs
            </flux:table.column>
            <flux:table.column />
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($items as $item)
                <flux:table.row data-id="{{ $item->id }}">
                    <flux:table.cell>
                        <div class="font-medium">{{ $item->title ?: 'Naamloze variant' }}</div>
                        @if ($item->variant_key)
                            <div class="text-sm text-gray-500">{{ $item->variant_key }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($item->sku)
                            <flux:badge size="sm" color="gray">{{ $item->sku }}</flux:badge>
                        @else
                            <span class="text-gray-400">Geen SKU</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($item->product)
                            <div class="font-medium">{{ $item->product->title }}</div>
                            <div class="text-sm text-gray-500">ID: {{ $item->product->id }}</div>
                        @else
                            <span class="text-red-500">Product niet gevonden</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($item->active)
                            <flux:badge size="sm" color="green">Actief</flux:badge>
                        @else
                            <flux:badge size="sm" color="red">Inactief</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium">{{ $item->stock_qty }}</div>
                        <div class="text-sm text-gray-500">Capaciteit: {{ $item->capacity }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($item->price_override_excl)
                            <div class="font-medium">€{{ number_format($item->price_override_excl, 2) }}</div>
                            <div class="text-sm text-gray-500">Override</div>
                        @else
                            <span class="text-gray-400">Standaard prijs</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                            <flux:menu>
                                <flux:menu.item icon="eye" href="{{ route($this->module_routes['read'], $item) }}">
                                    Bekijken
                                </flux:menu.item>
                                <flux:menu.item icon="pencil"
                                    href="{{ route($this->module_routes['update'], $item) }}">
                                    Bewerken
                                </flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger"
                                    wire:click="delete({{ $item->id }})"
                                    wire:confirm="Weet je zeker dat je deze variant wilt verwijderen?">
                                    Verwijderen
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:main>

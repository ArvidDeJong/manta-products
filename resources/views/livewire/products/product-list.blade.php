<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:button icon="plus" href="{{ route($this->module_routes['create']) }}">
                Toevoegen
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
            <flux:table.column><flux:icon.document-duplicate /></flux:table.column>
            <flux:table.column />
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($items as $item)
                <flux:table.row data-id="{{ $item->id }}">

                    @if ($this->fields['uploads']['active'])
                        <flux:table.cell><x-manta.tables.image :item="$item->image" /></flux:table.cell>
                    @endif
                    <flux:table.cell>{{ $item->title }}</flux:table.cell>
                    @if ($this->fields['slug']['active'])
                        <flux:table.cell>
                            @if ($item->slug && Route::has('website.news-item'))
                                <a href="{{ route('website.news-item', ['slug' => $item->slug]) }}"
                                    class="text-blue-500 hover:text-blue-800">
                                    {{ $item->slug }}
                                </a>
                            @endif
                        </flux:table.cell>
                    @endif

                    @if ($this->fields['uploads']['active'])
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

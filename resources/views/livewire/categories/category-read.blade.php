<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $item->name }}</flux:heading>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="primary" icon="pencil">
                Bewerken
            </flux:button>
            <flux:button href="{{ route($this->module_routes['list']) }}" variant="outline" icon="arrow-left">
                Terug naar lijst
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hoofdinformatie -->
        <div class="lg:col-span-2 space-y-6">
            <flux:card class="space-y-6">
                <flux:heading size="lg">Categorie informatie</flux:heading>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Naam</div>
                        <div class="mt-1 text-base">{{ $item->name }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Slug</div>
                        <div class="mt-1 text-base">{{ $item->slug }}</div>
                    </div>

                    @if($item->description)
                        <div>
                            <div class="text-sm font-medium text-gray-500">Beschrijving</div>
                            <div class="mt-1 text-base">{{ $item->description }}</div>
                        </div>
                    @endif

                    @if($item->parent)
                        <div>
                            <div class="text-sm font-medium text-gray-500">Hoofdcategorie</div>
                            <div class="mt-1">
                                <flux:badge variant="outline">{{ $item->parent->name }}</flux:badge>
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-sm font-medium text-gray-500">Pad</div>
                        <div class="mt-1 text-base">{{ $item->getBreadcrumbPath() }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Sortering</div>
                        <div class="mt-1 text-base">{{ $item->sort }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="mt-1">
                            @if($item->active)
                                <flux:badge color="green">Actief</flux:badge>
                            @else
                                <flux:badge color="red">Inactief</flux:badge>
                            @endif
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Subcategorieën -->
            @if($item->children->count() > 0)
                <flux:card class="space-y-4">
                    <flux:heading size="lg">Subcategorieën ({{ $item->children->count() }})</flux:heading>
                    <div class="space-y-2">
                        @foreach($item->children as $child)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div>
                                    <span class="font-medium">{{ $child->name }}</span>
                                    @if(!$child->active)
                                        <flux:badge size="sm" color="red" class="ml-2">Inactief</flux:badge>
                                    @endif
                                </div>
                                <flux:button 
                                    size="sm" 
                                    variant="ghost" 
                                    icon="eye"
                                    href="{{ route($this->module_routes['read'], $child->id) }}"
                                />
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            <!-- Producten -->
            @if($item->products->count() > 0)
                <flux:card class="space-y-4">
                    <flux:heading size="lg">Producten ({{ $item->products->count() }})</flux:heading>
                    <div class="space-y-2">
                        @foreach($item->products as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div>
                                    <span class="font-medium">{{ $product->title }}</span>
                                    @if(!$product->active)
                                        <flux:badge size="sm" color="red" class="ml-2">Inactief</flux:badge>
                                    @endif
                                </div>
                                <flux:button 
                                    size="sm" 
                                    variant="ghost" 
                                    icon="eye"
                                    href="{{ route('product.read', $product->id) }}"
                                />
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <flux:card class="space-y-4">
                <flux:heading size="lg">Statistieken</flux:heading>
                <div class="text-sm text-gray-600 space-y-2">
                    <div class="flex justify-between">
                        <span>Subcategorieën:</span>
                        <strong>{{ $item->children->count() }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Producten:</span>
                        <strong>{{ $item->products->count() }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Niveau:</span>
                        <strong>{{ $item->depth }}</strong>
                    </div>
                </div>
            </flux:card>

            <flux:card class="space-y-4">
                <flux:heading size="lg">Metadata</flux:heading>
                <div class="text-sm text-gray-600 space-y-2">
                    <div>
                        <span class="font-medium">Aangemaakt:</span><br>
                        {{ $item->created_at->format('d-m-Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Laatst bijgewerkt:</span><br>
                        {{ $item->updated_at->format('d-m-Y H:i') }}
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</flux:main>

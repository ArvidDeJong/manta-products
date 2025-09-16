<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Product bekijken</flux:heading>
            <div class="text-sm text-gray-600 mt-1">
                {{ $item->title }} ({{ $item->slug }})
            </div>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="outline" icon="pencil">
                Bewerken
            </flux:button>
            <flux:button href="{{ route($this->module_routes['list']) }}" variant="outline" icon="arrow-left">
                Terug naar lijst
            </flux:button>
        </div>
    </div>

    <flux:tab.group wire:model="activeTab">
        <flux:tabs>
            <flux:tab name="general">Algemeen</flux:tab>
            <flux:tab name="pricing">Prijzen</flux:tab>
            <flux:tab name="dimensions">Afmetingen</flux:tab>
            <flux:tab name="attributes">Eigenschappen</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="general" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Hoofdinformatie -->
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Basis informatie</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:field>
                                    <flux:label>Titel</flux:label>
                                    <flux:input value="{{ $item->title }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Slug</flux:label>
                                    <flux:input value="{{ $item->slug }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Product Type</flux:label>
                                    <flux:input value="{{ $this->fields['product_type']['options'][$item->product_type] ?? $item->product_type }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Status</flux:label>
                                    <flux:badge variant="{{ $item->active ? 'success' : 'danger' }}">
                                        {{ $item->active ? 'Actief' : 'Inactief' }}
                                    </flux:badge>
                                </flux:field>
                            </div>
                        </div>
                    </flux:card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Acties -->
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Acties</flux:heading>
                        
                        <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="primary" class="w-full" icon="pencil">
                            Bewerken
                        </flux:button>
                        
                        <flux:button href="{{ route($this->module_routes['list']) }}" variant="ghost" class="w-full">
                            Terug naar lijst
                        </flux:button>
                    </flux:card>

                    <!-- Statistieken -->
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Statistieken</flux:heading>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Aangemaakt:</span>
                            <span class="text-sm">{{ $item->created_at->format('d-m-Y') }}</span>
                        </div>
                        
                        @if($item->updated_at->ne($item->created_at))
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gewijzigd:</span>
                                <span class="text-sm">{{ $item->updated_at->format('d-m-Y') }}</span>
                            </div>
                        @endif
                        
                        @if($item->created_by)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Door:</span>
                                <span class="text-sm">{{ $item->created_by }}</span>
                            </div>
                        @endif
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="pricing" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Prijsinformatie</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:field>
                                    <flux:label>Eenheid Type</flux:label>
                                    <flux:input value="{{ $this->fields['unit_type']['options'][$item->unit_type] ?? $item->unit_type }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Prijs per eenheid</flux:label>
                                    <flux:input value="€ {{ number_format($item->price_per_unit, 2) }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>BTW tarief</flux:label>
                                    <flux:input value="{{ $item->tax_rate }}%" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Prijs incl. BTW</flux:label>
                                    <flux:input value="€ {{ number_format($item->price_per_unit * (1 + $item->tax_rate / 100), 2) }}" disabled />
                                </flux:field>
                            </div>
                        </div>
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Acties</flux:heading>
                        <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="primary" class="w-full" icon="pencil">
                            Bewerken
                        </flux:button>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="dimensions" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Afmetingen</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Lengte (mm)</flux:label>
                                    <flux:input value="{{ $item->length_mm ?? 'Niet ingesteld' }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Breedte (mm)</flux:label>
                                    <flux:input value="{{ $item->width_mm ?? 'Niet ingesteld' }}" disabled />
                                </flux:field>
                            </div>
                            
                            <div>
                                <flux:field>
                                    <flux:label>Hoogte (mm)</flux:label>
                                    <flux:input value="{{ $item->height_mm ?? 'Niet ingesteld' }}" disabled />
                                </flux:field>
                            </div>
                        </div>

                        @if($item->length_mm && $item->width_mm)
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <flux:heading size="md" class="mb-2">Berekeningen</flux:heading>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Oppervlakte:</span>
                                        <span class="font-medium">{{ number_format(($item->length_mm * $item->width_mm) / 1000000, 2) }} m²</span>
                                    </div>
                                    @if($item->height_mm)
                                        <div>
                                            <span class="text-gray-600">Volume:</span>
                                            <span class="font-medium">{{ number_format(($item->length_mm * $item->width_mm * $item->height_mm) / 1000000000, 3) }} m³</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Acties</flux:heading>
                        <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="primary" class="w-full" icon="pencil">
                            Bewerken
                        </flux:button>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="attributes" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Product eigenschappen</flux:heading>
                        
                        <div class="text-center py-8 text-gray-500">
                            <flux:icon.tag class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                            <p class="text-lg font-medium mb-2">Eigenschappen overzicht</p>
                            <p class="text-sm mb-4">Hier zie je alle eigenschappen die aan dit product zijn gekoppeld.</p>
                            <flux:button href="{{ route($this->module_routes['update'], $item) }}?tab=attributes" variant="primary" icon="plus">
                                Eigenschappen beheren
                            </flux:button>
                        </div>
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Acties</flux:heading>
                        <flux:button href="{{ route($this->module_routes['update'], $item) }}" variant="primary" class="w-full" icon="pencil">
                            Bewerken
                        </flux:button>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</flux:main>

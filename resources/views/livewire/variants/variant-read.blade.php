<flux:main>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ $this->tab_title }}</flux:heading>
            <flux:subheading>Variant details</flux:subheading>
        </div>
        <div class="flex items-center space-x-2">
            <flux:button icon="pencil" href="{{ route($this->module_routes['update'], $item) }}">
                Bewerken
            </flux:button>
        </div>
    </div>

    <flux:tab.group wire:model="activeTab">
        <flux:tabs>
            <flux:tab name="general">Algemeen</flux:tab>
            <flux:tab name="pricing">Prijzen</flux:tab>
            <flux:tab name="dimensions">Afmetingen</flux:tab>
            <flux:tab name="values">Attribuut waarden</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="general" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Basis informatie</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:label>Titel</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->title ?: 'Naamloze variant' }}</div>
                            </div>

                            @if($item->sku)
                                <div>
                                    <flux:label>SKU</flux:label>
                                    <div class="mt-1">
                                        <flux:badge size="sm" color="gray">{{ $item->sku }}</flux:badge>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <flux:label>Status</flux:label>
                                <div class="mt-1">
                                    @if($item->active)
                                        <flux:badge size="sm" color="green">Actief</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="red">Inactief</flux:badge>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <flux:label>Capaciteit</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->capacity }}</div>
                            </div>

                            <div>
                                <flux:label>Voorraad</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->stock_qty }}</div>
                            </div>

                            @if($item->variant_key)
                                <div>
                                    <flux:label>Variant sleutel</flux:label>
                                    <div class="mt-1">
                                        <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $item->variant_key }}</code>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </flux:card>

                    @if($item->product)
                        <flux:card class="space-y-6">
                            <flux:heading size="lg">Product informatie</flux:heading>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <flux:label>Product naam</flux:label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $item->product->title }}</div>
                                </div>

                                <div>
                                    <flux:label>Product ID</flux:label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $item->product->id }}</div>
                                </div>

                                @if($item->product->slug)
                                    <div>
                                        <flux:label>Product slug</flux:label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $item->product->slug }}</div>
                                    </div>
                                @endif

                                <div>
                                    <flux:label>Product type</flux:label>
                                    <div class="mt-1">
                                        <flux:badge size="sm" color="blue">{{ ucfirst($item->product->product_type) }}</flux:badge>
                                    </div>
                                </div>
                            </div>
                        </flux:card>
                    @endif
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Samenvatting</flux:heading>
                        <div class="text-sm text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>Status:</span>
                                @if($item->active)
                                    <span class="text-green-600 font-medium">Actief</span>
                                @else
                                    <span class="text-red-600 font-medium">Inactief</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Voorraad:</span>
                                <span class="font-medium">{{ $item->stock_qty }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Capaciteit:</span>
                                <span class="font-medium">{{ $item->capacity }}</span>
                            </div>
                            @if($item->price_override_excl)
                                <div class="flex items-center justify-between">
                                    <span>Override prijs:</span>
                                    <span class="font-medium">€{{ number_format($item->price_override_excl, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </flux:card>

                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Acties</flux:heading>
                        <flux:button icon="pencil" href="{{ route($this->module_routes['update'], $item) }}" class="w-full">
                            Bewerken
                        </flux:button>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="pricing" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Prijzen en berekening</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($item->price_override_excl)
                                <div>
                                    <flux:label>Prijs override (excl. BTW)</flux:label>
                                    <div class="mt-1 text-lg font-semibold text-gray-900">€{{ number_format($item->price_override_excl, 2) }}</div>
                                </div>
                            @else
                                <div>
                                    <flux:label>Prijs override</flux:label>
                                    <div class="mt-1 text-sm text-gray-500">Gebruikt standaard productprijs</div>
                                </div>
                            @endif

                            @if($item->tax_rate)
                                <div>
                                    <flux:label>BTW percentage</flux:label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $item->tax_rate }}%</div>
                                </div>
                            @endif

                            @if($item->unit_type)
                                <div>
                                    <flux:label>Eenheid type</flux:label>
                                    <div class="mt-1">
                                        <flux:badge size="sm" color="blue">{{ ucfirst($item->unit_type) }}</flux:badge>
                                    </div>
                                </div>
                            @endif

                            @if($item->unit_step)
                                <div>
                                    <flux:label>Eenheid stap</flux:label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $item->unit_step }}</div>
                                </div>
                            @endif

                            @if($item->calc_mode)
                                <div>
                                    <flux:label>Berekeningswijze</flux:label>
                                    <div class="mt-1">
                                        <flux:badge size="sm" color="purple">{{ ucfirst(str_replace('_', ' ', $item->calc_mode)) }}</flux:badge>
                                    </div>
                                </div>
                            @endif

                            @if($item->wastage_pct)
                                <div>
                                    <flux:label>Verspilling percentage</flux:label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $item->wastage_pct }}%</div>
                                </div>
                            @endif

                            @if($item->rounding_mode)
                                <div>
                                    <flux:label>Afrondingswijze</flux:label>
                                    <div class="mt-1">
                                        <flux:badge size="sm" color="gray">{{ ucfirst($item->rounding_mode) }}</flux:badge>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Prijsberekening</flux:heading>
                        <div class="text-sm text-gray-600">
                            @if($item->price_override_excl)
                                @php
                                    $excl = $item->price_override_excl;
                                    $taxRate = $item->tax_rate ?? ($item->product->tax_rate ?? 21.00);
                                    $tax = $excl * ($taxRate / 100);
                                    $incl = $excl + $tax;
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Excl. BTW:</span>
                                        <span class="font-medium">€{{ number_format($excl, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>BTW ({{ $taxRate }}%):</span>
                                        <span class="font-medium">€{{ number_format($tax, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2">
                                        <span class="font-semibold">Incl. BTW:</span>
                                        <span class="font-semibold">€{{ number_format($incl, 2) }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-500">Gebruikt standaard productprijs</div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="dimensions" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Afmetingen</flux:heading>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <flux:label>Lengte</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->length_mm ?? 0 }} mm</div>
                            </div>

                            <div>
                                <flux:label>Breedte</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->width_mm ?? 0 }} mm</div>
                            </div>

                            <div>
                                <flux:label>Hoogte</flux:label>
                                <div class="mt-1 text-sm text-gray-900">{{ $item->height_mm ?? 0 }} mm</div>
                            </div>
                        </div>
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Berekeningen</flux:heading>
                        <div class="text-sm text-gray-600">
                            @if($item->length_mm && $item->width_mm)
                                <div class="flex justify-between">
                                    <span>Oppervlakte:</span>
                                    <span class="font-medium">{{ number_format(($item->length_mm * $item->width_mm) / 1000000, 2) }} m²</span>
                                </div>
                            @endif
                            @if($item->length_mm && $item->width_mm && $item->height_mm)
                                <div class="flex justify-between">
                                    <span>Volume:</span>
                                    <span class="font-medium">{{ number_format(($item->length_mm * $item->width_mm * $item->height_mm) / 1000000000, 3) }} m³</span>
                                </div>
                            @endif
                            @if(!$item->length_mm && !$item->width_mm && !$item->height_mm)
                                <div class="text-gray-500">Geen afmetingen ingesteld</div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="values" class="mt-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">Attribuut waarden</flux:heading>
                        
                        @if($item->values && count($item->values) > 0)
                            <div class="space-y-4">
                                @foreach($item->values as $value)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $value['attribute']['name'] ?? 'Onbekend attribuut' }}</div>
                                            @if($value['attribute']['code'])
                                                <div class="text-sm text-gray-500">Code: {{ $value['attribute']['code'] }}</div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <flux:badge size="sm" color="blue">{{ $value['attribute_value']['value'] ?? 'Onbekende waarde' }}</flux:badge>
                                            @if($value['attribute_value']['code'])
                                                <div class="text-sm text-gray-500 mt-1">{{ $value['attribute_value']['code'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <flux:icon.tag class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                <p class="text-lg font-medium mb-2">Geen attribuut waarden</p>
                                <p class="text-sm">Deze variant heeft geen specifieke attribuut waarden.</p>
                            </div>
                        @endif
                    </flux:card>
                </div>

                <div class="space-y-6">
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Samenvatting</flux:heading>
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Aantal attributen:</span>
                                <span class="font-medium">{{ $item->values ? count($item->values) : 0 }}</span>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</flux:main>

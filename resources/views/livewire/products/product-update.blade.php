<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Product bewerken</flux:heading>
            <div class="text-sm text-gray-600 mt-1">
                {{ $item->title }} ({{ $item->slug }})
            </div>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route($this->module_routes['read'], $item) }}" variant="outline" icon="eye">
                Bekijken
            </flux:button>
            <flux:button href="{{ route($this->module_routes['list']) }}" variant="outline" icon="arrow-left">
                Terug naar lijst
            </flux:button>
        </div>
    </div>

    <form wire:submit="save">
        <flux:tab.group wire:model="activeTab">
            <flux:tabs>
                <flux:tab name="general">Algemeen</flux:tab>
                <flux:tab name="pricing">Prijzen</flux:tab>
                <flux:tab name="dimensions">Afmetingen</flux:tab>
                <flux:tab name="attributes">Eigenschappen</flux:tab>
                <flux:tab name="variants">Varianten</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="general" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Hoofdformulier -->
                    <div class="lg:col-span-2">
                        <flux:card class="space-y-6">
                            <flux:heading size="lg">Basis informatie</flux:heading>
                            
                            <!-- Titel -->
                            @if($this->fields['title']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['title']['title'] }}</flux:label>
                                    <flux:input 
                                        wire:model.blur="title" 
                                        placeholder="Bijv. Premium Houten Vloer"
                                        :required="$this->fields['title']['required']"
                                    />
                                    <flux:error name="title" />
                                </flux:field>
                            @endif

                            <!-- Slug -->
                            @if($this->fields['slug']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['slug']['title'] }}</flux:label>
                                    <flux:input 
                                        wire:model.blur="slug" 
                                        placeholder="premium-houten-vloer"
                                        :required="$this->fields['slug']['required']"
                                    />
                                    <flux:description>
                                        URL-vriendelijke versie van de titel
                                    </flux:description>
                                    <flux:error name="slug" />
                                </flux:field>
                            @endif

                            <!-- Product Type -->
                            @if($this->fields['product_type']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['product_type']['title'] }}</flux:label>
                                    <flux:select 
                                        wire:model.live="product_type" 
                                        placeholder="Selecteer een type..."
                                        :required="$this->fields['product_type']['required']"
                                    >
                                        @foreach($this->fields['product_type']['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="product_type" />
                                </flux:field>
                            @endif

                            <!-- Actief -->
                            @if($this->fields['active']['active'])
                                <flux:field>
                                    <flux:checkbox wire:model="active">{{ $this->fields['active']['title'] }}</flux:checkbox>
                                    <flux:error name="active" />
                                </flux:field>
                            @endif
                        </flux:card>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Acties -->
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Acties</flux:heading>
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Opslaan
                            </flux:button>
                            <flux:button href="{{ route($this->module_routes['list']) }}" variant="ghost" class="w-full">
                                Annuleren
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
                            
                            <!-- Unit Type -->
                            @if($this->fields['unit_type']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['unit_type']['title'] }}</flux:label>
                                    <flux:select 
                                        wire:model.live="unit_type" 
                                        placeholder="Selecteer een eenheid..."
                                        :required="$this->fields['unit_type']['required']"
                                    >
                                        @foreach($this->fields['unit_type']['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="unit_type" />
                                </flux:field>
                            @endif

                            <!-- Prijs per eenheid -->
                            @if($this->fields['price_per_unit']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['price_per_unit']['title'] }}</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01"
                                        wire:model="price_per_unit" 
                                        placeholder="0.00"
                                        :required="$this->fields['price_per_unit']['required']"
                                    />
                                    <flux:error name="price_per_unit" />
                                </flux:field>
                            @endif

                            <!-- BTW tarief -->
                            @if($this->fields['tax_rate']['active'])
                                <flux:field>
                                    <flux:label>{{ $this->fields['tax_rate']['title'] }}</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01"
                                        wire:model="tax_rate" 
                                        placeholder="21.00"
                                        :required="$this->fields['tax_rate']['required']"
                                    />
                                    <flux:error name="tax_rate" />
                                </flux:field>
                            @endif
                        </flux:card>
                    </div>

                    <div class="space-y-6">
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Acties</flux:heading>
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Opslaan
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
                                <!-- Lengte -->
                                @if($this->fields['length_mm']['active'])
                                    <flux:field>
                                        <flux:label>{{ $this->fields['length_mm']['title'] }}</flux:label>
                                        <flux:input 
                                            type="number" 
                                            wire:model="length_mm" 
                                            placeholder="0"
                                            :required="$this->fields['length_mm']['required']"
                                        />
                                        <flux:error name="length_mm" />
                                    </flux:field>
                                @endif

                                <!-- Breedte -->
                                @if($this->fields['width_mm']['active'])
                                    <flux:field>
                                        <flux:label>{{ $this->fields['width_mm']['title'] }}</flux:label>
                                        <flux:input 
                                            type="number" 
                                            wire:model="width_mm" 
                                            placeholder="0"
                                            :required="$this->fields['width_mm']['required']"
                                        />
                                        <flux:error name="width_mm" />
                                    </flux:field>
                                @endif

                                <!-- Hoogte -->
                                @if($this->fields['height_mm']['active'])
                                    <flux:field>
                                        <flux:label>{{ $this->fields['height_mm']['title'] }}</flux:label>
                                        <flux:input 
                                            type="number" 
                                            wire:model="height_mm" 
                                            placeholder="0"
                                            :required="$this->fields['height_mm']['required']"
                                        />
                                        <flux:error name="height_mm" />
                                    </flux:field>
                                @endif
                            </div>
                        </flux:card>
                    </div>

                    <div class="space-y-6">
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Acties</flux:heading>
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Opslaan
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
                            
                            @if(count($availableAttributes) > 0)
                                <div class="space-y-4">
                                    @foreach($availableAttributes as $attribute)
                                        <div class="flex items-center justify-between p-4 border rounded-lg {{ isset($selectedAttributes[$attribute['id']]) ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                            <div class="flex items-center space-x-3">
                                                <flux:checkbox 
                                                    wire:click="toggleAttribute({{ $attribute['id'] }})"
                                                    {{ isset($selectedAttributes[$attribute['id']]) ? 'checked' : '' }}
                                                />
                                                <div>
                                                    <h4 class="font-medium text-gray-900">{{ $attribute['name'] }}</h4>
                                                    @if($attribute['code'])
                                                        <p class="text-sm text-gray-500">Code: {{ $attribute['code'] }}</p>
                                                    @endif
                                                    <p class="text-sm text-gray-600">Type: {{ ucfirst($attribute['type']) }}</p>
                                                </div>
                                            </div>
                                            
                                            @if(isset($selectedAttributes[$attribute['id']]))
                                                <div class="flex items-center space-x-2">
                                                    <flux:checkbox 
                                                        wire:change="updateAttributeRequired({{ $attribute['id'] }}, $event.target.checked)"
                                                        {{ ($selectedAttributes[$attribute['id']]['is_required'] ?? false) ? 'checked' : '' }}
                                                        label="Verplicht"
                                                    />
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <flux:icon.cog class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                    <p class="text-lg font-medium mb-2">Geen eigenschappen beschikbaar</p>
                                    <p class="text-sm mb-4">Er zijn nog geen eigenschappen aangemaakt in het systeem.</p>
                                </div>
                            @endif
                        </flux:card>
                        
                        @if(count($selectedAttributes) > 0)
                            <flux:card class="space-y-4">
                                <flux:heading size="lg">Geselecteerde eigenschappen</flux:heading>
                                <div class="space-y-2">
                                    @foreach($selectedAttributes as $attributeId => $data)
                                        @php
                                            $attribute = collect($availableAttributes)->firstWhere('id', $attributeId);
                                        @endphp
                                        @if($attribute)
                                            <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <div>
                                                    <span class="font-medium text-blue-900">{{ $attribute['name'] }}</span>
                                                    @if($data['is_required'])
                                                        <flux:badge size="sm" color="red" class="ml-2">Verplicht</flux:badge>
                                                    @endif
                                                </div>
                                                <flux:button 
                                                    size="sm" 
                                                    variant="ghost" 
                                                    icon="x-mark"
                                                    wire:click="toggleAttribute({{ $attributeId }})"
                                                />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </flux:card>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Samenvatting</flux:heading>
                            <div class="text-sm text-gray-600">
                                <p><strong>Totaal eigenschappen:</strong> {{ count($availableAttributes) }}</p>
                                <p><strong>Geselecteerd:</strong> {{ count($selectedAttributes) }}</p>
                                <p><strong>Verplicht:</strong> {{ collect($selectedAttributes)->where('is_required', true)->count() }}</p>
                            </div>
                        </flux:card>
                        
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Acties</flux:heading>
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Opslaan
                            </flux:button>
                        </flux:card>
                    </div>
                </div>
            </flux:tab.panel>

            <flux:tab.panel name="variants" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <flux:card class="space-y-6">
                            <div class="flex items-center justify-between">
                                <flux:heading size="lg">Product varianten</flux:heading>
                                <flux:button 
                                    variant="primary" 
                                    icon="plus"
                                    size="sm"
                                    wire:click="showVariantForm"
                                >
                                    Variant toevoegen
                                </flux:button>
                            </div>
                            
                            @if(count($variants) > 0)
                                <div class="space-y-4">
                                    @foreach($variants as $variant)
                                        <div class="p-4 border rounded-lg {{ $variant['active'] ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <h4 class="font-medium text-gray-900">{{ $variant['title'] ?: 'Naamloze variant' }}</h4>
                                                        @if($variant['sku'])
                                                            <flux:badge size="sm" color="gray">{{ $variant['sku'] }}</flux:badge>
                                                        @endif
                                                        @if($variant['active'])
                                                            <flux:badge size="sm" color="green">Actief</flux:badge>
                                                        @else
                                                            <flux:badge size="sm" color="red">Inactief</flux:badge>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                                        @if($variant['price_override_excl'])
                                                            <div>
                                                                <span class="font-medium">Prijs:</span> €{{ number_format($variant['price_override_excl'], 2) }}
                                                            </div>
                                                        @endif
                                                        @if($variant['stock_qty'])
                                                            <div>
                                                                <span class="font-medium">Voorraad:</span> {{ $variant['stock_qty'] }}
                                                            </div>
                                                        @endif
                                                        @if($variant['capacity'])
                                                            <div>
                                                                <span class="font-medium">Capaciteit:</span> {{ $variant['capacity'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if(!empty($variant['values']))
                                                        <div class="mt-3">
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($variant['values'] as $value)
                                                                    <flux:badge size="sm" color="blue">
                                                                        {{ $value['attribute']['name'] ?? 'Onbekend' }}: {{ $value['attribute_value']['value'] ?? 'Onbekend' }}
                                                                    </flux:badge>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <flux:button 
                                                    size="sm" 
                                                    variant="ghost" 
                                                    icon="trash"
                                                    wire:click="deleteVariant({{ $variant['id'] }})"
                                                    wire:confirm="Weet je zeker dat je deze variant wilt verwijderen?"
                                                />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <flux:icon.squares-2x2 class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                                    <p class="text-lg font-medium mb-2">Geen varianten</p>
                                    <p class="text-sm mb-4">Dit product heeft nog geen varianten.</p>
                                </div>
                            @endif
                        </flux:card>
                        
                        @if($showVariantForm)
                            <flux:card class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <flux:heading size="lg">Nieuwe variant</flux:heading>
                                    <flux:button 
                                        size="sm" 
                                        variant="ghost" 
                                        icon="x-mark"
                                        wire:click="hideVariantForm"
                                    />
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>Titel</flux:label>
                                        <flux:input wire:model="newVariant.title" placeholder="Variant naam" />
                                    </flux:field>
                                    
                                    <flux:field>
                                        <flux:label>SKU</flux:label>
                                        <flux:input wire:model="newVariant.sku" placeholder="Product code" />
                                    </flux:field>
                                    
                                    <flux:field>
                                        <flux:label>Prijs override (excl.)</flux:label>
                                        <flux:input type="number" step="0.01" wire:model="newVariant.price_override_excl" placeholder="0.00" />
                                    </flux:field>
                                    
                                    <flux:field>
                                        <flux:label>Voorraad</flux:label>
                                        <flux:input type="number" wire:model="newVariant.stock_qty" placeholder="0" />
                                    </flux:field>
                                    
                                    <flux:field>
                                        <flux:label>Capaciteit</flux:label>
                                        <flux:input type="number" wire:model="newVariant.capacity" placeholder="1" />
                                    </flux:field>
                                    
                                    <flux:field>
                                        <flux:checkbox wire:model="newVariant.active" label="Actief" />
                                    </flux:field>
                                </div>
                                
                                @if(count($selectedAttributes) > 0)
                                    <div class="border-t pt-6">
                                        <flux:heading size="md" class="mb-4">Attribuut waarden</flux:heading>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($selectedAttributes as $attributeId => $data)
                                                @php
                                                    $attribute = collect($availableAttributes)->firstWhere('id', $attributeId);
                                                    $attributeValues = $this->getAttributeValues($attributeId);
                                                @endphp
                                                @if($attribute)
                                                    <flux:field>
                                                        <flux:label>{{ $attribute['name'] }}</flux:label>
                                                        <flux:select wire:model="newVariant.variant_values.{{ $attributeId }}" placeholder="Kies een waarde">
                                                            @foreach($attributeValues as $value)
                                                                <flux:select.option value="{{ $value['id'] }}">{{ $value['value'] }}</flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    </flux:field>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="flex justify-end space-x-2">
                                    <flux:button variant="ghost" wire:click="hideVariantForm">
                                        Annuleren
                                    </flux:button>
                                    <flux:button variant="primary" wire:click="addVariant">
                                        Variant toevoegen
                                    </flux:button>
                                </div>
                            </flux:card>
                        @endif
                    </div>

                    <div class="space-y-6">
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Samenvatting</flux:heading>
                            <div class="text-sm text-gray-600">
                                <p><strong>Totaal varianten:</strong> {{ count($variants) }}</p>
                                <p><strong>Actieve varianten:</strong> {{ collect($variants)->where('active', true)->count() }}</p>
                                <p><strong>Met voorraad:</strong> {{ collect($variants)->where('stock_qty', '>', 0)->count() }}</p>
                            </div>
                        </flux:card>
                        
                        <flux:card class="space-y-4">
                            <flux:heading size="lg">Acties</flux:heading>
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Opslaan
                            </flux:button>
                        </flux:card>
                    </div>
                </div>
            </flux:tab.panel>
        </flux:tab.group>
    </form>
</flux:main>

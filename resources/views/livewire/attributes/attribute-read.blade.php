<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Eigenschap bekijken</flux:heading>
            <div class="text-sm text-gray-600 mt-1">
                {{ $item->name }} ({{ $item->code }})
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hoofdinformatie -->
        <div class="lg:col-span-2">
            <flux:card class="space-y-6">
                <flux:heading size="lg">Basis informatie</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <flux:field>
                            <flux:label>Naam</flux:label>
                            <flux:input value="{{ $item->name }}" disabled />
                        </flux:field>
                    </div>
                    
                    <div>
                        <flux:field>
                            <flux:label>Code</flux:label>
                            <flux:input value="{{ $item->code }}" disabled />
                        </flux:field>
                    </div>
                    
                    <div>
                        <flux:field>
                            <flux:label>Type</flux:label>
                            <flux:input value="{{ $this->fields['type']['options'][$item->type] ?? $item->type }}" disabled />
                        </flux:field>
                    </div>
                    
                    <div>
                        <flux:field>
                            <flux:label>Sortering</flux:label>
                            <flux:input value="{{ $item->sort }}" disabled />
                        </flux:field>
                    </div>
                </div>
            </flux:card>

            <!-- Configuratie -->
            @if($item->config && count($item->config) > 0)
                <flux:card class="mt-6 space-y-6">
                    <flux:heading size="lg">Configuratie</flux:heading>
                    
                    @if($item->type === 'select' || $item->type === 'multiselect')
                        @if(isset($item->config['options']) && is_array($item->config['options']))
                            <div>
                                <flux:field>
                                    <flux:label>Opties</flux:label>
                                    <div class="space-y-2">
                                        @foreach($item->config['options'] as $key => $value)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <span class="font-medium">{{ $value }}</span>
                                                <flux:badge variant="outline">{{ $key }}</flux:badge>
                                            </div>
                                        @endforeach
                                    </div>
                                </flux:field>
                            </div>
                        @endif
                    @elseif($item->type === 'number')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($item->config['min']))
                                <flux:field>
                                    <flux:label>Minimum waarde</flux:label>
                                    <flux:input value="{{ $item->config['min'] }}" disabled />
                                </flux:field>
                            @endif
                            @if(isset($item->config['max']))
                                <flux:field>
                                    <flux:label>Maximum waarde</flux:label>
                                    <flux:input value="{{ $item->config['max'] }}" disabled />
                                </flux:field>
                            @endif
                            @if(isset($item->config['step']))
                                <flux:field>
                                    <flux:label>Stap grootte</flux:label>
                                    <flux:input value="{{ $item->config['step'] }}" disabled />
                                </flux:field>
                            @endif
                            @if(isset($item->config['unit']))
                                <flux:field>
                                    <flux:label>Eenheid</flux:label>
                                    <flux:input value="{{ $item->config['unit'] }}" disabled />
                                </flux:field>
                            @endif
                        </div>
                    @elseif($item->type === 'text' || $item->type === 'textarea')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($item->config['max_length']))
                                <flux:field>
                                    <flux:label>Maximum lengte</flux:label>
                                    <flux:input value="{{ $item->config['max_length'] }}" disabled />
                                </flux:field>
                            @endif
                            @if(isset($item->config['placeholder']))
                                <flux:field>
                                    <flux:label>Placeholder tekst</flux:label>
                                    <flux:input value="{{ $item->config['placeholder'] }}" disabled />
                                </flux:field>
                            @endif
                        </div>
                    @endif
                </flux:card>
            @endif

            <!-- Attribute waarden -->
            @if($item->values && $item->values->count() > 0)
                <flux:card class="mt-6 space-y-6">
                    <flux:heading size="lg">Bestaande waarden</flux:heading>
                    
                    <div class="space-y-2">
                        @foreach($item->values as $value)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium">{{ $value->value }}</div>
                                    @if($value->label && $value->label !== $value->value)
                                        <div class="text-sm text-gray-600">{{ $value->label }}</div>
                                    @endif
                                </div>
                                <flux:badge variant="outline">
                                    {{ $value->products_count ?? 0 }} producten
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif
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
                    <span class="text-gray-600">Waarden:</span>
                    <flux:badge>{{ $item->values ? $item->values->count() : 0 }}</flux:badge>
                </div>
                
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

            <!-- Preview -->
            <flux:card class="space-y-4">
                <flux:heading size="lg">Voorbeeld</flux:heading>
                
                <div class="text-sm text-gray-600 mb-2">
                    Zo zal dit veld eruit zien:
                </div>
                
                @if($item->type === 'text')
                    <flux:input placeholder="{{ $item->config['placeholder'] ?? 'Tekst invoer...' }}" disabled />
                @elseif($item->type === 'number')
                    <flux:input type="number" placeholder="0" disabled />
                    @if(isset($item->config['unit']))
                        <div class="text-xs text-gray-500 mt-1">Eenheid: {{ $item->config['unit'] }}</div>
                    @endif
                @elseif($item->type === 'textarea')
                    <flux:textarea placeholder="{{ $item->config['placeholder'] ?? 'Tekst invoer...' }}" disabled rows="3" />
                @elseif($item->type === 'select')
                    <flux:select disabled>
                        <flux:select.option>Selecteer een optie...</flux:select.option>
                        @if(isset($item->config['options']) && is_array($item->config['options']))
                            @foreach($item->config['options'] as $key => $value)
                                <flux:select.option value="{{ $key }}">{{ $value }}</flux:select.option>
                            @endforeach
                        @endif
                    </flux:select>
                @elseif($item->type === 'boolean')
                    <flux:checkbox disabled>Ja/Nee optie</flux:checkbox>
                @elseif($item->type === 'date')
                    <flux:input type="date" disabled />
                @endif
            </flux:card>
        </div>
    </div>
</flux:main>

<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Eigenschap toevoegen</flux:heading>
        </div>
        <div class="flex gap-2">
            @if(env('USE_FAKER', false))
                <flux:button variant="outline" wire:click="generateFakeData" icon="sparkles">
                    Fake data
                </flux:button>
            @endif
            <flux:button href="{{ route($this->module_routes['list']) }}" variant="outline" icon="arrow-left">
                Terug naar lijst
            </flux:button>
        </div>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Hoofdformulier -->
            <div class="lg:col-span-2">
                <flux:card class="space-y-6">
                    <flux:heading size="lg">Basis informatie</flux:heading>
                        <!-- Naam -->
                        @if($this->fields['name']['active'])
                            <div>
                                <flux:field>
                                    <flux:label>{{ $this->fields['name']['title'] }}</flux:label>
                                    <flux:input 
                                        wire:model.blur="name" 
                                        placeholder="Bijv. Kleur, Materiaal, Afmeting..."
                                        :required="$this->fields['name']['required']"
                                    />
                                    <flux:error name="name" />
                                </flux:field>
                            </div>
                        @endif

                        <!-- Code -->
                        @if($this->fields['code']['active'])
                            <div>
                                <flux:field>
                                    <flux:label>{{ $this->fields['code']['title'] }}</flux:label>
                                    <flux:input 
                                        wire:model.blur="code" 
                                        placeholder="bijv_kleur_materiaal_afmeting"
                                        :required="$this->fields['code']['required']"
                                    />
                                    <flux:description>
                                        Unieke code voor dit attribuut (wordt automatisch gegenereerd op basis van de naam)
                                    </flux:description>
                                    <flux:error name="code" />
                                </flux:field>
                            </div>
                        @endif

                        <!-- Type -->
                        @if($this->fields['type']['active'])
                            <div>
                                <flux:field>
                                    <flux:label>{{ $this->fields['type']['title'] }}</flux:label>
                                    <flux:select 
                                        wire:model.live="type" 
                                        placeholder="Selecteer een type..."
                                        :required="$this->fields['type']['required']"
                                    >
                                        @foreach($this->fields['type']['options'] as $value => $label)
                                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="type" />
                                </flux:field>
                            </div>
                        @endif

                        <!-- Sortering -->
                        @if($this->fields['sort']['active'])
                            <div>
                                <flux:field>
                                    <flux:label>{{ $this->fields['sort']['title'] }}</flux:label>
                                    <flux:input 
                                        type="number" 
                                        wire:model="sort" 
                                        placeholder="100"
                                        min="0"
                                        :required="$this->fields['sort']['required']"
                                    />
                                    <flux:description>
                                        Lagere nummers worden eerst getoond
                                    </flux:description>
                                    <flux:error name="sort" />
                                </flux:field>
                            </div>
                        @endif
                </flux:card>

                <!-- Configuratie sectie -->
                @if($type)
                    <flux:card class="mt-6 space-y-6">
                        <flux:heading size="lg">Type configuratie</flux:heading>
                            @if($type === 'select' || $type === 'multiselect')
                                <div>
                                    <flux:field>
                                        <flux:label>Opties (JSON formaat)</flux:label>
                                        <flux:textarea 
                                            wire:model="configJson" 
                                            rows="8"
                                            placeholder='{"rood": "Rood", "blauw": "Blauw", "groen": "Groen"}'
                                        />
                                        <flux:description>
                                            Voer de opties in als JSON object met key-value paren
                                        </flux:description>
                                        <flux:error name="configJson" />
                                    </flux:field>
                                </div>
                            @elseif($type === 'number')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>Minimum waarde</flux:label>
                                        <flux:input type="number" wire:model="configMin" placeholder="0" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>Maximum waarde</flux:label>
                                        <flux:input type="number" wire:model="configMax" placeholder="1000" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>Stap grootte</flux:label>
                                        <flux:input type="number" wire:model="configStep" placeholder="1" step="0.01" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>Eenheid</flux:label>
                                        <flux:input wire:model="configUnit" placeholder="cm, kg, stuks..." />
                                    </flux:field>
                                </div>
                            @elseif($type === 'text' || $type === 'textarea')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:field>
                                        <flux:label>Maximum lengte</flux:label>
                                        <flux:input type="number" wire:model="configMaxLength" placeholder="255" />
                                    </flux:field>
                                    <flux:field>
                                        <flux:label>Placeholder tekst</flux:label>
                                        <flux:input wire:model="configPlaceholder" placeholder="Voer waarde in..." />
                                    </flux:field>
                                </div>
                            @endif
                    </flux:card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Acties -->
                <flux:card class="space-y-4">
                    <flux:heading size="lg">Acties</flux:heading>
                        <flux:button type="submit" variant="primary" class="w-full" icon="check">
                            Opslaan
                        </flux:button>
                        
                        <flux:button 
                            href="{{ route($this->module_routes['list']) }}" 
                            variant="ghost" 
                            class="w-full"
                        >
                            Annuleren
                        </flux:button>
                </flux:card>

                <!-- Preview -->
                @if($type)
                    <flux:card class="space-y-4">
                        <flux:heading size="lg">Voorbeeld</flux:heading>
                            <div class="text-sm text-gray-600 mb-2">
                                Zo zal dit veld eruit zien:
                            </div>
                            
                            @if($type === 'text')
                                <flux:input placeholder="{{ $configPlaceholder ?: 'Tekst invoer...' }}" disabled />
                            @elseif($type === 'number')
                                <flux:input type="number" placeholder="0" disabled />
                                @if($configUnit)
                                    <div class="text-xs text-gray-500 mt-1">Eenheid: {{ $configUnit }}</div>
                                @endif
                            @elseif($type === 'textarea')
                                <flux:textarea placeholder="{{ $configPlaceholder ?: 'Tekst invoer...' }}" disabled rows="3" />
                            @elseif($type === 'select')
                                <flux:select disabled>
                                    <flux:select.option>Selecteer een optie...</flux:select.option>
                                </flux:select>
                            @elseif($type === 'boolean')
                                <flux:checkbox disabled>Ja/Nee optie</flux:checkbox>
                            @elseif($type === 'date')
                                <flux:input type="date" disabled />
                            @endif
                    </flux:card>
                @endif
            </div>
        </div>
    </form>
</flux:main>

<script>
    // Auto-generate code from name
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('name-updated', (name) => {
            if (!@this.code) {
                @this.code = name.toLowerCase()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_');
            }
        });
    });
</script>

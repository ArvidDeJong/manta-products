<flux:main container>
    <x-manta.breadcrumb :$breadcrumb />
    
    <div class="mb-8 mt-4 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Categorie toevoegen</flux:heading>
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
                        <flux:field>
                            <flux:label>{{ $this->fields['name']['title'] }}</flux:label>
                            <flux:input 
                                wire:model.blur="name" 
                                placeholder="Bijv. Vloeren"
                                :required="$this->fields['name']['required']"
                            />
                            <flux:error name="name" />
                        </flux:field>
                    @endif

                    <!-- Slug -->
                    @if($this->fields['slug']['active'])
                        <flux:field>
                            <flux:label>{{ $this->fields['slug']['title'] }}</flux:label>
                            <flux:input 
                                wire:model.blur="slug" 
                                placeholder="vloeren"
                                :required="$this->fields['slug']['required']"
                            />
                            <flux:description>
                                URL-vriendelijke versie van de naam (wordt automatisch gegenereerd)
                            </flux:description>
                            <flux:error name="slug" />
                        </flux:field>
                    @endif

                    <!-- Hoofdcategorie -->
                    @if($this->fields['parent_id']['active'])
                        <flux:field>
                            <flux:label>{{ $this->fields['parent_id']['title'] }}</flux:label>
                            <flux:select 
                                wire:model="parent_id" 
                                placeholder="Selecteer een categorie"
                            >
                                <flux:select.option value="">Geen (hoofdcategorie)</flux:select.option>
                                @foreach($categoriesTree as $id => $name)
                                    <flux:select.option value="{{ $id }}">{{ $name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:description>
                                Selecteer een hoofdcategorie om een subcategorie te maken
                            </flux:description>
                            <flux:error name="parent_id" />
                        </flux:field>
                    @endif

                    <!-- Beschrijving -->
                    @if($this->fields['description']['active'])
                        <flux:field>
                            <flux:label>{{ $this->fields['description']['title'] }}</flux:label>
                            <flux:textarea 
                                wire:model="description" 
                                placeholder="Beschrijving van de categorie..."
                                rows="4"
                            />
                            <flux:error name="description" />
                        </flux:field>
                    @endif

                    <!-- Sortering -->
                    @if($this->fields['sort']['active'])
                        <flux:field>
                            <flux:label>{{ $this->fields['sort']['title'] }}</flux:label>
                            <flux:input 
                                type="number" 
                                wire:model="sort" 
                                placeholder="0"
                            />
                            <flux:description>
                                Lagere nummers worden eerst getoond
                            </flux:description>
                            <flux:error name="sort" />
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
            </div>
        </div>
    </form>
</flux:main>

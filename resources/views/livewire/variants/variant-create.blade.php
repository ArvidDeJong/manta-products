<flux:main>
    <div class="flex items-center justify-between mb-6">
        <div>
            <flux:heading size="xl">{{ $this->tab_title }}</flux:heading>
            <flux:subheading>Nieuwe variant aanmaken</flux:subheading>
        </div>
        <div class="flex items-center space-x-2">
            @if(env('USE_FAKER', false))
                <flux:button variant="ghost" icon="sparkles" wire:click="generateFakeData">
                    Fake data
                </flux:button>
            @endif
        </div>
    </div>

    <form wire:submit="save">
        <flux:tab.group wire:model="activeTab">
            <flux:tabs>
                <flux:tab name="general">Algemeen</flux:tab>
                <flux:tab name="pricing">Prijzen</flux:tab>
                <flux:tab name="dimensions">Afmetingen</flux:tab>
            </flux:tabs>

            <flux:tab.panel name="general" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <flux:card class="space-y-6">
                            <flux:heading size="lg">Basis informatie</flux:heading>
                            
                            <!-- Product selectie -->
                            @if(!$product_id)
                                <flux:field>
                                    <flux:label>Product</flux:label>
                                    <flux:select wire:model="product_id" placeholder="Kies een product">
                                        @foreach($this->getProducts() as $product)
                                            <flux:select.option value="{{ $product->id }}">{{ $product->title }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:error name="product_id" />
                                </flux:field>
                            @endif
                            
                            <!-- Titel -->
                            @if($this->fields['title']['active'])
                                <flux:field>
                                    <flux:label>Titel</flux:label>
                                    <flux:input 
                                        wire:model="title" 
                                        placeholder="Variant naam"
                                        :required="$this->fields['title']['required']"
                                    />
                                    <flux:error name="title" />
                                </flux:field>
                            @endif

                            <!-- SKU -->
                            @if($this->fields['sku']['active'])
                                <flux:field>
                                    <flux:label>SKU</flux:label>
                                    <flux:input 
                                        wire:model="sku" 
                                        placeholder="Product code"
                                        :required="$this->fields['sku']['required']"
                                    />
                                    <flux:error name="sku" />
                                </flux:field>
                            @endif

                            <!-- Capaciteit -->
                            @if($this->fields['capacity']['active'])
                                <flux:field>
                                    <flux:label>Capaciteit</flux:label>
                                    <flux:input 
                                        type="number" 
                                        wire:model="capacity" 
                                        placeholder="1"
                                        :required="$this->fields['capacity']['required']"
                                    />
                                    <flux:error name="capacity" />
                                </flux:field>
                            @endif

                            <!-- Voorraad -->
                            @if($this->fields['stock_qty']['active'])
                                <flux:field>
                                    <flux:label>Voorraad</flux:label>
                                    <flux:input 
                                        type="number" 
                                        wire:model="stock_qty" 
                                        placeholder="0"
                                        :required="$this->fields['stock_qty']['required']"
                                    />
                                    <flux:error name="stock_qty" />
                                </flux:field>
                            @endif

                            <!-- Actief -->
                            <flux:field>
                                <flux:checkbox wire:model="active" label="Actief" />
                            </flux:field>
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

            <flux:tab.panel name="pricing" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <flux:card class="space-y-6">
                            <flux:heading size="lg">Prijzen en berekening</flux:heading>

                            <!-- Prijs override -->
                            @if($this->fields['price_override_excl']['active'])
                                <flux:field>
                                    <flux:label>Prijs override (excl. BTW)</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01" 
                                        wire:model="price_override_excl" 
                                        placeholder="0.00"
                                        :required="$this->fields['price_override_excl']['required']"
                                    />
                                    <flux:error name="price_override_excl" />
                                </flux:field>
                            @endif

                            <!-- BTW percentage -->
                            @if($this->fields['tax_rate']['active'])
                                <flux:field>
                                    <flux:label>BTW percentage</flux:label>
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

                            <!-- Unit type -->
                            @if($this->fields['unit_type']['active'])
                                <flux:field>
                                    <flux:label>Eenheid type</flux:label>
                                    <flux:select wire:model="unit_type" placeholder="Kies eenheid type">
                                        <flux:select.option value="piece">Stuk</flux:select.option>
                                        <flux:select.option value="meter">Meter</flux:select.option>
                                        <flux:select.option value="m2">Vierkante meter</flux:select.option>
                                        <flux:select.option value="m3">Kubieke meter</flux:select.option>
                                    </flux:select>
                                    <flux:error name="unit_type" />
                                </flux:field>
                            @endif

                            <!-- Unit step -->
                            @if($this->fields['unit_step']['active'])
                                <flux:field>
                                    <flux:label>Eenheid stap</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01" 
                                        wire:model="unit_step" 
                                        placeholder="1.00"
                                        :required="$this->fields['unit_step']['required']"
                                    />
                                    <flux:error name="unit_step" />
                                </flux:field>
                            @endif

                            <!-- Berekeningswijze -->
                            @if($this->fields['calc_mode']['active'])
                                <flux:field>
                                    <flux:label>Berekeningswijze</flux:label>
                                    <flux:select wire:model="calc_mode" placeholder="Kies berekeningswijze">
                                        <flux:select.option value="direct_length">Directe lengte</flux:select.option>
                                        <flux:select.option value="dimensions_2d">2D afmetingen</flux:select.option>
                                        <flux:select.option value="dimensions_3d">3D afmetingen</flux:select.option>
                                    </flux:select>
                                    <flux:error name="calc_mode" />
                                </flux:field>
                            @endif

                            <!-- Verspilling percentage -->
                            @if($this->fields['wastage_pct']['active'])
                                <flux:field>
                                    <flux:label>Verspilling percentage</flux:label>
                                    <flux:input 
                                        type="number" 
                                        step="0.01" 
                                        wire:model="wastage_pct" 
                                        placeholder="0.00"
                                        :required="$this->fields['wastage_pct']['required']"
                                    />
                                    <flux:error name="wastage_pct" />
                                </flux:field>
                            @endif

                            <!-- Afrondingswijze -->
                            @if($this->fields['rounding_mode']['active'])
                                <flux:field>
                                    <flux:label>Afrondingswijze</flux:label>
                                    <flux:select wire:model="rounding_mode" placeholder="Kies afrondingswijze">
                                        <flux:select.option value="round">Afronden</flux:select.option>
                                        <flux:select.option value="ceil">Naar boven</flux:select.option>
                                        <flux:select.option value="floor">Naar beneden</flux:select.option>
                                    </flux:select>
                                    <flux:error name="rounding_mode" />
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
                                        <flux:label>Lengte (mm)</flux:label>
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
                                        <flux:label>Breedte (mm)</flux:label>
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
                                        <flux:label>Hoogte (mm)</flux:label>
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
        </flux:tab.group>
    </form>
</flux:main>

<div class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">Nieuw Product Toevoegen</h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <flux:field>
            <flux:label>Titel</flux:label>
            <flux:input wire:model.live="title" />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label>Slug</flux:label>
            <flux:input wire:model.live="slug" />
            <flux:error name="slug" />
        </flux:field>

        <flux:field>
            <flux:label>Product Type</flux:label>
            <flux:select wire:model.live="product_type">
                <flux:select.option value="sellable">Sellable</flux:select.option>
                <flux:select.option value="reservable">Reservable</flux:select.option>
                <flux:select.option value="both">Both</flux:select.option>
            </flux:select>
            <flux:error name="product_type" />
        </flux:field>

        <flux:field>
            <flux:label>Unit Type</flux:label>
            <flux:select wire:model.live="unit_type">
                <flux:select.option value="m2">m2</flux:select.option>
                <flux:select.option value="hour">Hour</flux:select.option>
                <flux:select.option value="piece">Piece</flux:select.option>
            </flux:select>
            <flux:error name="unit_type" />
        </flux:field>

        <div class="flex items-center space-x-4">
            <flux:button type="submit" variant="primary">Opslaan</flux:button>
            <flux:button href="{{ route('manta-products.demo.products') }}">Annuleren</flux:button>
        </div>
    </form>
</div>

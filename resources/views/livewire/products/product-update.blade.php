<div class="space-y-4 p-6">
    <h1 class="text-2xl font-bold">Product Aanpassen</h1>

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
            <flux:button href="{{ route('manta-product.demo.products') }}">Annuleren</flux:button>
            <flux:button wire:click="delete" variant="danger">Verwijderen</flux:button>
        </div>
    </form>

    <flux:modal name="delete-product-modal">
        <flux:modal.header>
            <flux:heading>Product Verwijderen</flux:heading>
        </flux:modal.header>
        <flux:modal.content>
            <p>Weet je zeker dat je dit product wilt verwijderen?</p>
        </flux:modal.content>
        <flux:modal.actions>
            <flux:button wire:click="deleteConfirm" variant="danger">Verwijderen</flux:button>
            <flux:modal.closer>
                <flux:button variant="secondary">Annuleren</flux:button>
            </flux:modal.closer>
        </flux:modal.actions>
    </flux:modal>
</div>

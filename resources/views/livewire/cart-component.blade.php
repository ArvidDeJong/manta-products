<div class="relative">
    <!-- Cart Toggle Button -->
    <flux:button 
        wire:click="toggleCart" 
        variant="ghost" 
        size="sm"
        class="relative"
    >
        <flux:icon name="shopping-cart" class="w-5 h-5" />
        @if($cartSummary['items_count'] > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $cartSummary['items_count'] }}
            </span>
        @endif
    </flux:button>

    <!-- Cart Dropdown -->
    @if($showCart)
        <div class="absolute right-0 top-full mt-2 w-96 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <flux:heading size="lg">Winkelwagen</flux:heading>
                    <flux:button wire:click="toggleCart" variant="ghost" size="sm">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                    </flux:button>
                </div>

                @if($cartSummary['is_empty'])
                    <div class="text-center py-8">
                        <flux:icon name="shopping-cart" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                        <p class="text-gray-500">Je winkelwagen is leeg</p>
                    </div>
                @else
                    <!-- Cart Items -->
                    <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        @foreach($cartSummary['items'] as $item)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm truncate">{{ $item['title'] }}</h4>
                                    
                                    @if(!empty($item['configuration']))
                                        <div class="text-xs text-gray-600 mt-1">
                                            @foreach($item['configuration'] as $key => $value)
                                                <span class="inline-block mr-2">{{ $key }}: {{ $value }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center gap-2">
                                            @if($item['can_update'])
                                                <flux:button 
                                                    wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                                    variant="ghost" 
                                                    size="xs"
                                                    :disabled="$item['quantity'] <= 1"
                                                >
                                                    <flux:icon name="minus" class="w-3 h-3" />
                                                </flux:button>
                                                
                                                <span class="text-sm font-medium px-2">{{ $item['quantity'] }}</span>
                                                
                                                <flux:button 
                                                    wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                                    variant="ghost" 
                                                    size="xs"
                                                >
                                                    <flux:icon name="plus" class="w-3 h-3" />
                                                </flux:button>
                                            @else
                                                <span class="text-sm text-gray-600">Aantal: {{ $item['quantity'] }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="text-right">
                                            <div class="text-sm font-medium">{{ $item['line_total'] }}</div>
                                            <div class="text-xs text-gray-600">{{ $item['unit_price'] }} per stuk</div>
                                        </div>
                                    </div>
                                </div>

                                <flux:button 
                                    wire:click="removeItem({{ $item['id'] }})"
                                    variant="ghost" 
                                    size="xs"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </flux:button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cart Totals -->
                    <div class="border-t pt-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotaal (excl. BTW)</span>
                                <span>€{{ number_format($cartSummary['subtotal_excl'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>BTW</span>
                                <span>€{{ number_format($cartSummary['tax_total'], 2) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-base border-t pt-2">
                                <span>Totaal</span>
                                <span>{{ $cartSummary['formatted_total'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 mt-4">
                        <flux:button 
                            wire:click="clearCart"
                            variant="ghost"
                            size="sm"
                            class="flex-1"
                        >
                            Leeg winkelwagen
                        </flux:button>
                        
                        <flux:button 
                            variant="primary"
                            size="sm"
                            class="flex-1"
                        >
                            Afrekenen
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@script
<script>
    // Close cart when clicking outside
    document.addEventListener('click', function(event) {
        const cartComponent = event.target.closest('[wire\\:id]');
        if (!cartComponent && $wire.showCart) {
            $wire.showCart = false;
        }
    });

    // Listen for cart updates
    $wire.on('cart-updated', (event) => {
        // You can add toast notifications here
        console.log(event.message);
    });
</script>
@endscript

<?php

namespace Darvis\MantaProduct\Livewire;

use Livewire\Component;
use Darvis\MantaProduct\Services\CartService;
use Darvis\MantaProduct\Models\CartItem;

class CartComponent extends Component
{
    public $cartSummary = [];
    public $showCart = false;

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->loadCartSummary();
    }

    public function loadCartSummary()
    {
        $this->cartSummary = $this->cartService->getSummary();
    }

    public function updateQuantity($itemId, $quantity)
    {
        $item = CartItem::find($itemId);
        
        if ($item && $item->canBeUpdated()) {
            $this->cartService->updateQuantity($item, (float) $quantity);
            $this->loadCartSummary();
            
            $this->dispatch('cart-updated', [
                'message' => 'Aantal bijgewerkt',
                'type' => 'success'
            ]);
        }
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        
        if ($item) {
            $this->cartService->removeItem($item);
            $this->loadCartSummary();
            
            $this->dispatch('cart-updated', [
                'message' => 'Item verwijderd uit winkelwagen',
                'type' => 'success'
            ]);
        }
    }

    public function clearCart()
    {
        $this->cartService->clear();
        $this->loadCartSummary();
        
        $this->dispatch('cart-updated', [
            'message' => 'Winkelwagen geleegd',
            'type' => 'success'
        ]);
    }

    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }

    public function render()
    {
        return view('manta-product::livewire.cart-component');
    }
}

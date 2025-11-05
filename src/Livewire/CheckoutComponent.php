<?php

namespace Darvis\MantaProduct\Livewire;

use Livewire\Component;
use Darvis\MantaProduct\Services\CartService;
use Darvis\MantaProduct\Models\Cart;

class CheckoutComponent extends Component
{
    public $cart;
    public $cartSummary = [];
    public $validationErrors = [];
    public $discountCode = '';
    public $customerInfo = [
        'email' => '',
        'first_name' => '',
        'last_name' => '',
        'phone' => '',
    ];
    public $billingAddress = [
        'street' => '',
        'number' => '',
        'postal_code' => '',
        'city' => '',
        'country' => 'NL',
    ];

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->loadCart();
        $this->validateCart();
        
        // Pre-fill user info if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerInfo['email'] = $user->email;
            $this->customerInfo['first_name'] = $user->first_name ?? '';
            $this->customerInfo['last_name'] = $user->last_name ?? '';
        }
    }

    public function loadCart()
    {
        $this->cart = $this->cartService->getCart();
        $this->cartSummary = $this->cartService->getSummary();
    }

    public function validateCart()
    {
        $this->validationErrors = $this->cartService->validateCart();
    }

    public function updateQuantity($itemId, $quantity)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item) {
            $this->cartService->updateQuantity($item, $quantity);
            $this->loadCart();
            $this->validateCart();
        }
    }

    public function removeItem($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item) {
            $this->cartService->removeItem($item);
            $this->loadCart();
            $this->validateCart();
            
            session()->flash('message', 'Item verwijderd uit winkelwagen');
        }
    }

    public function applyDiscountCode()
    {
        if (empty($this->discountCode)) {
            return;
        }

        $success = $this->cartService->applyDiscountCode($this->discountCode);
        
        if ($success) {
            $this->loadCart();
            session()->flash('message', 'Kortingscode toegepast');
        } else {
            session()->flash('error', 'Ongeldige kortingscode');
        }
    }

    public function removeDiscountCode()
    {
        $this->cartService->removeDiscountCode();
        $this->discountCode = '';
        $this->loadCart();
        session()->flash('message', 'Kortingscode verwijderd');
    }

    public function proceedToPayment()
    {
        // Validate form
        $this->validate([
            'customerInfo.email' => 'required|email',
            'customerInfo.first_name' => 'required|string|max:255',
            'customerInfo.last_name' => 'required|string|max:255',
            'billingAddress.street' => 'required|string|max:255',
            'billingAddress.number' => 'required|string|max:10',
            'billingAddress.postal_code' => 'required|string|max:10',
            'billingAddress.city' => 'required|string|max:255',
        ]);

        // Final cart validation
        $this->validateCart();
        if (!empty($this->validationErrors)) {
            session()->flash('error', 'Er zijn problemen met je winkelwagen. Controleer de items.');
            return;
        }

        if ($this->cartSummary['is_empty']) {
            session()->flash('error', 'Je winkelwagen is leeg');
            return;
        }

        // Redirect to payment or create order
        return redirect()->route('checkout.payment', [
            'cart_id' => $this->cart->id,
            'customer' => $this->customerInfo,
            'billing' => $this->billingAddress,
        ]);
    }

    public function render()
    {
        return view('manta-product::livewire.checkout-component');
    }
}

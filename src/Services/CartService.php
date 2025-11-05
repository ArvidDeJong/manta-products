<?php

namespace Darvis\MantaProduct\Services;

use Darvis\MantaProduct\Models\Cart;
use Darvis\MantaProduct\Models\CartItem;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Models\GiftCard;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected ?Cart $cart = null;

    /**
     * Get current cart instance
     */
    public function getCart(): Cart
    {
        if (!$this->cart) {
            $this->cart = Cart::current();
        }

        return $this->cart;
    }

    /**
     * Add product to cart
     */
    public function addProduct(
        Product $product, 
        float $quantity = 1, 
        array $configuration = []
    ): CartItem {
        $cart = $this->getCart();
        $item = $cart->addProduct($product, $quantity, $configuration);
        
        return $item;
    }

    /**
     * Add product variant to cart
     */
    public function addVariant(
        ProductVariant $variant, 
        float $quantity = 1, 
        array $configuration = []
    ): CartItem {
        $cart = $this->getCart();
        $item = $cart->addVariant($variant, $quantity, $configuration);
        
        return $item;
    }

    /**
     * Add gift card to cart
     */
    public function addGiftCard(GiftCard $giftCard, float $quantity = 1): CartItem
    {
        $cart = $this->getCart();
        $item = $cart->addGiftCard($giftCard, $quantity);
        
        return $item;
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(CartItem $item, float $quantity): void
    {
        $cart = $this->getCart();
        $cart->updateItemQuantity($item, $quantity);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $cart = $this->getCart();
        $cart->removeItem($item);
    }

    /**
     * Clear entire cart
     */
    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->clear();
    }

    /**
     * Get cart summary
     */
    public function getSummary(): array
    {
        $cart = $this->getCart();
        
        return [
            'items_count' => $cart->total_items,
            'subtotal_excl' => $cart->subtotal_excl,
            'tax_total' => $cart->tax_total,
            'total_incl' => $cart->total_incl,
            'formatted_total' => $cart->formatted_total,
            'is_empty' => $cart->isEmpty(),
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->formatted_unit_price,
                    'line_total' => $item->formatted_line_total,
                    'configuration' => $item->configuration_display,
                    'can_update' => $item->canBeUpdated(),
                ];
            }),
        ];
    }

    /**
     * Apply discount code
     */
    public function applyDiscountCode(string $code): bool
    {
        $cart = $this->getCart();
        
        // Hier zou je de discount logic implementeren
        // Voor nu een simpele implementatie
        $meta = $cart->meta ?? [];
        $meta['discount_code'] = $code;
        
        $cart->meta = $meta;
        $cart->save();
        
        return true;
    }

    /**
     * Remove discount code
     */
    public function removeDiscountCode(): void
    {
        $cart = $this->getCart();
        
        $meta = $cart->meta ?? [];
        unset($meta['discount_code']);
        
        $cart->meta = $meta;
        $cart->save();
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(): array
    {
        $cart = $this->getCart();
        $errors = [];

        if ($cart->isEmpty()) {
            $errors[] = 'Winkelwagen is leeg';
            return $errors;
        }

        foreach ($cart->items as $item) {
            // Check stock for variants
            if ($item->item_type === 'variant' && $item->variant) {
                if ($item->variant->stock_qty !== null && $item->quantity > $item->variant->stock_qty) {
                    $errors[] = "Onvoldoende voorraad voor {$item->title}";
                }
            }

            // Check if gift card is still valid
            if ($item->item_type === 'gift_card' && $item->giftCard) {
                if (!$item->giftCard->isValid()) {
                    $errors[] = "Cadeaubon {$item->giftCard->code} is niet meer geldig";
                }
            }

            // Check if product/variant is still active
            $purchasable = $item->purchasable;
            if ($purchasable && isset($purchasable->active) && !$purchasable->active) {
                $errors[] = "{$item->title} is niet meer beschikbaar";
            }
        }

        return $errors;
    }

    /**
     * Get cart for specific user (admin function)
     */
    public function getCartForUser(int $userId): ?Cart
    {
        return Cart::where('user_id', $userId)->first();
    }

    /**
     * Merge guest cart when user logs in
     */
    public function mergeGuestCart(): void
    {
        if (auth()->check()) {
            $sessionId = Session::getId();
            Cart::mergeGuestCart($sessionId, auth()->id());
            
            // Reset current cart instance to get the merged cart
            $this->cart = null;
        }
    }

    /**
     * Clean up expired carts
     */
    public static function cleanupExpiredCarts(): int
    {
        return Cart::where('expires_at', '<', now())
            ->where(function ($query) {
                $query->whereDoesntHave('items')
                    ->orWhereHas('items', function ($q) {
                        $q->havingRaw('COUNT(*) = 0');
                    });
            })
            ->delete();
    }

    /**
     * Get cart statistics (admin function)
     */
    public static function getStatistics(): array
    {
        $totalCarts = Cart::count();
        $activeCarts = Cart::whereHas('items')->count();
        $averageItems = Cart::whereHas('items')
            ->withCount('items')
            ->avg('items_count');
        $averageValue = Cart::whereHas('items')
            ->avg('total_incl');

        return [
            'total_carts' => $totalCarts,
            'active_carts' => $activeCarts,
            'average_items_per_cart' => round($averageItems, 2),
            'average_cart_value' => round($averageValue, 2),
            'abandonment_rate' => $totalCarts > 0 ? round((($totalCarts - $activeCarts) / $totalCarts) * 100, 2) : 0,
        ];
    }
}

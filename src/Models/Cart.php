<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    protected $table = 'manta_carts';

    protected $fillable = [
        'session_id',
        'user_id',
        'currency',
        'subtotal_excl',
        'tax_total',
        'total_incl',
        'meta',
        'expires_at',
    ];

    protected $casts = [
        'subtotal_excl' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total_incl' => 'decimal:2',
        'meta' => 'array',
        'expires_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Get or create cart for current session/user
     */
    public static function current(): self
    {
        $userId = auth()->id();
        $sessionId = Session::getId();

        $cart = static::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->first();

        if (!$cart) {
            $cart = static::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'expires_at' => now()->addDays(30),
            ]);
        }

        return $cart;
    }

    /**
     * Add product to cart
     */
    public function addProduct(Product $product, float $quantity = 1, array $configuration = []): CartItem
    {
        return $this->addItem('product', $product->id, null, null, $quantity, $configuration);
    }

    /**
     * Add product variant to cart
     */
    public function addVariant(ProductVariant $variant, float $quantity = 1, array $configuration = []): CartItem
    {
        return $this->addItem('variant', $variant->product_id, $variant->id, null, $quantity, $configuration);
    }

    /**
     * Add gift card to cart
     */
    public function addGiftCard(GiftCard $giftCard, float $quantity = 1): CartItem
    {
        return $this->addItem('gift_card', null, null, $giftCard->id, $quantity);
    }

    /**
     * Generic method to add item to cart
     */
    protected function addItem(
        string $itemType,
        ?int $productId,
        ?int $variantId,
        ?int $giftCardId,
        float $quantity,
        array $configuration = []
    ): CartItem {
        // Check if item already exists with same configuration
        $existingItem = $this->items()
            ->where('item_type', $itemType)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->where('gift_card_id', $giftCardId)
            ->where('configuration', json_encode($configuration))
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->calculateTotals();
            $existingItem->save();
            $this->recalculateTotals();
            return $existingItem;
        }

        // Create new cart item
        $item = $this->items()->create([
            'item_type' => $itemType,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'gift_card_id' => $giftCardId,
            'quantity' => $quantity,
            'configuration' => $configuration,
        ]);

        $item->calculateTotals();
        $item->save();
        
        $this->recalculateTotals();
        
        return $item;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
        $this->recalculateTotals();
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(CartItem $item, float $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($item);
            return;
        }

        $item->quantity = $quantity;
        $item->calculateTotals();
        $item->save();
        
        $this->recalculateTotals();
    }

    /**
     * Clear all items from cart
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->recalculateTotals();
    }

    /**
     * Recalculate cart totals
     */
    public function recalculateTotals(): void
    {
        $this->load('items');
        
        $this->subtotal_excl = $this->items->sum('line_total_excl');
        $this->tax_total = $this->items->sum('line_tax');
        $this->total_incl = $this->items->sum('line_total_incl');
        
        $this->save();
    }

    /**
     * Get total item count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Check if cart is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Extend cart expiry
     */
    public function extendExpiry(int $days = 30): void
    {
        $this->expires_at = now()->addDays($days);
        $this->save();
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total_incl, 2);
    }

    /**
     * Merge guest cart with user cart when user logs in
     */
    public static function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestCart = static::where('session_id', $sessionId)->first();
        $userCart = static::where('user_id', $userId)->first();

        if (!$guestCart) {
            return;
        }

        if (!$userCart) {
            // Convert guest cart to user cart
            $guestCart->update([
                'user_id' => $userId,
                'session_id' => null,
            ]);
            return;
        }

        // Merge items from guest cart to user cart
        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('item_type', $guestItem->item_type)
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->where('gift_card_id', $guestItem->gift_card_id)
                ->where('configuration', $guestItem->configuration)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $guestItem->quantity;
                $existingItem->calculateTotals();
                $existingItem->save();
            } else {
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        $userCart->recalculateTotals();
        $guestCart->delete();
    }
}

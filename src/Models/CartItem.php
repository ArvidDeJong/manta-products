<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'manta_cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'gift_card_id',
        'item_type',
        'quantity',
        'unit_price_excl',
        'tax_rate',
        'line_total_excl',
        'line_tax',
        'line_total_incl',
        'configuration',
        'meta',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price_excl' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'line_total_excl' => 'decimal:2',
        'line_tax' => 'decimal:2',
        'line_total_incl' => 'decimal:2',
        'configuration' => 'array',
        'meta' => 'array',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class, 'gift_card_id');
    }

    /**
     * Get the purchasable item (product, variant, or gift card)
     */
    public function getPurchasableAttribute()
    {
        return match ($this->item_type) {
            'product' => $this->product,
            'variant' => $this->variant,
            'gift_card' => $this->giftCard,
            default => null,
        };
    }

    /**
     * Get the effective product (either direct product or variant's product)
     */
    public function getEffectiveProductAttribute(): ?Product
    {
        return match ($this->item_type) {
            'product' => $this->product,
            'variant' => $this->variant?->product,
            'gift_card' => $this->giftCard?->variant?->product,
            default => null,
        };
    }

    /**
     * Calculate and set totals based on quantity and pricing
     */
    public function calculateTotals(): void
    {
        $purchasable = $this->purchasable;
        
        if (!$purchasable) {
            return;
        }

        // Get unit price and tax rate based on item type
        [$unitPrice, $taxRate] = $this->getPricingDetails($purchasable);
        
        // Apply quantity calculations for products with dimensions
        $effectiveQuantity = $this->calculateEffectiveQuantity($purchasable);
        
        $this->unit_price_excl = $unitPrice;
        $this->tax_rate = $taxRate;
        $this->line_total_excl = round($unitPrice * $effectiveQuantity, 2);
        $this->line_tax = round($this->line_total_excl * ($taxRate / 100), 2);
        $this->line_total_incl = $this->line_total_excl + $this->line_tax;
    }

    /**
     * Get pricing details for the purchasable item
     */
    protected function getPricingDetails($purchasable): array
    {
        return match ($this->item_type) {
            'product' => [
                (float) ($purchasable->price_per_unit ?? 0),
                (float) ($purchasable->tax_rate ?? config('manta-product.default_tax_rate', 21.00))
            ],
            'variant' => [
                $purchasable->effectiveUnitPriceExcl(),
                $purchasable->effectiveTaxRate()
            ],
            'gift_card' => [
                (float) $purchasable->original_value,
                0.00 // Gift cards are usually tax-free
            ],
            default => [0.00, 0.00],
        };
    }

    /**
     * Calculate effective quantity considering dimensions and product settings
     */
    protected function calculateEffectiveQuantity($purchasable): float
    {
        if ($this->item_type === 'gift_card') {
            return $this->quantity;
        }

        $product = $this->effective_product;
        
        if (!$product || !$this->configuration) {
            return $this->quantity;
        }

        // Use product's normalizeUnits method if available
        if (method_exists($product, 'normalizeUnits')) {
            $normalizedUnits = $product->normalizeUnits($this->configuration);
            return $normalizedUnits * $this->quantity;
        }

        return $this->quantity;
    }

    /**
     * Get item title for display
     */
    public function getTitleAttribute(): string
    {
        return match ($this->item_type) {
            'product' => $this->product?->title ?? 'Product',
            'variant' => $this->variant?->title ?? $this->variant?->product?->title ?? 'Product Variant',
            'gift_card' => 'Cadeaubon - ' . ($this->giftCard?->variant?->product?->title ?? 'Cadeaubon'),
            default => 'Unknown Item',
        };
    }

    /**
     * Get formatted line total
     */
    public function getFormattedLineTotalAttribute(): string
    {
        return '€' . number_format($this->line_total_incl, 2);
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '€' . number_format($this->unit_price_excl, 2);
    }

    /**
     * Get configuration display for frontend
     */
    public function getConfigurationDisplayAttribute(): array
    {
        if (!$this->configuration) {
            return [];
        }

        $display = [];
        
        // Handle dimensions
        if (isset($this->configuration['length_mm'])) {
            $display['Lengte'] = ($this->configuration['length_mm'] / 1000) . 'm';
        }
        if (isset($this->configuration['width_mm'])) {
            $display['Breedte'] = ($this->configuration['width_mm'] / 1000) . 'm';
        }
        if (isset($this->configuration['height_mm'])) {
            $display['Hoogte'] = ($this->configuration['height_mm'] / 1000) . 'm';
        }

        // Handle custom attributes
        if (isset($this->configuration['attributes'])) {
            foreach ($this->configuration['attributes'] as $key => $value) {
                $display[ucfirst($key)] = $value;
            }
        }

        return $display;
    }

    /**
     * Check if item can be updated (not for gift cards that are already generated)
     */
    public function canBeUpdated(): bool
    {
        if ($this->item_type === 'gift_card') {
            return !$this->giftCard?->is_active;
        }
        
        return true;
    }

    /**
     * Boot method to automatically calculate totals
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
            $cartItem->calculateTotals();
        });

        static::updating(function ($cartItem) {
            if ($cartItem->isDirty(['quantity', 'configuration'])) {
                $cartItem->calculateTotals();
            }
        });
    }
}

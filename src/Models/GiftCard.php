<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use SoftDeletes;

    protected $table = 'manta_gift_cards';

    protected $fillable = [
        'code',
        'product_variant_id',
        'original_value',
        'current_balance',
        'recipient_name',
        'recipient_email',
        'sender_name',
        'message',
        'expires_at',
        'is_active',
        'redeemed_at',
        'deleted_by',
    ];

    protected $casts = [
        'original_value' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'expires_at' => 'date',
        'redeemed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        return $this->variant->product();
    }

    /**
     * Generate a unique gift card code
     */
    public static function generateCode(): string
    {
        do {
            $code = 'GIFT' . date('Y') . strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Check if gift card is valid for use
     */
    public function isValid(): bool
    {
        return $this->is_active 
            && $this->current_balance > 0 
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Redeem amount from gift card
     */
    public function redeem(float $amount): bool
    {
        if (!$this->isValid() || $amount > $this->current_balance) {
            return false;
        }
        
        $this->current_balance -= $amount;
        if ($this->current_balance <= 0) {
            $this->redeemed_at = now();
        }
        $this->save();
        
        return true;
    }

    /**
     * Get remaining balance as formatted string
     */
    public function getFormattedBalanceAttribute(): string
    {
        return '€' . number_format($this->current_balance, 2);
    }

    /**
     * Check if gift card is fully redeemed
     */
    public function isFullyRedeemed(): bool
    {
        return $this->current_balance <= 0 || $this->redeemed_at !== null;
    }

    /**
     * Check if gift card is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}

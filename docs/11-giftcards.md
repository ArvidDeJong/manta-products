# Gift Cards

The Manta Products package includes a comprehensive gift card system that allows you to create, manage, and redeem digital gift cards.

## Overview

Gift cards are digital vouchers that customers can purchase and redeem for products. They support:

- **Unique codes** - Automatically generated unique gift card codes
- **Flexible values** - Any monetary value can be assigned
- **Expiration dates** - Optional expiration functionality
- **Partial redemption** - Gift cards can be used multiple times until balance is depleted
- **Recipient information** - Store recipient and sender details
- **Personal messages** - Include custom messages with gift cards

## Gift Card Model

The `GiftCard` model handles all gift card functionality:

```php
use Darvis\MantaProduct\Models\GiftCard;

$giftCard = GiftCard::create([
    'code' => GiftCard::generateCode(), // Auto-generated unique code
    'product_variant_id' => $variant->id, // Optional: link to specific product variant
    'original_value' => 50.00,
    'current_balance' => 50.00,
    'recipient_name' => 'John Doe',
    'recipient_email' => 'john@example.com',
    'sender_name' => 'Jane Smith',
    'message' => 'Happy Birthday!',
    'expires_at' => now()->addYear(), // Optional expiration
    'is_active' => true,
]);
```

### Gift Card Properties

- **code**: Unique identifier (auto-generated)
- **product_variant_id**: Optional link to specific product variant
- **original_value**: Initial gift card value
- **current_balance**: Remaining balance after redemptions
- **recipient_name**: Name of the gift card recipient
- **recipient_email**: Email address of recipient
- **sender_name**: Name of the person giving the gift card
- **message**: Personal message included with the gift card
- **expires_at**: Optional expiration date
- **is_active**: Whether the gift card can be used
- **redeemed_at**: Timestamp when fully redeemed

## Creating Gift Cards

### Basic Gift Card

```php
$giftCard = new GiftCard([
    'original_value' => 25.00,
    'current_balance' => 25.00,
    'recipient_email' => 'customer@example.com',
    'is_active' => true,
]);

// Generate unique code automatically
$giftCard->code = GiftCard::generateCode();
$giftCard->save();
```

### Gift Card with Expiration

```php
$giftCard = GiftCard::create([
    'code' => GiftCard::generateCode(),
    'original_value' => 100.00,
    'current_balance' => 100.00,
    'recipient_name' => 'Alice Johnson',
    'recipient_email' => 'alice@example.com',
    'sender_name' => 'Bob Wilson',
    'message' => 'Enjoy your shopping!',
    'expires_at' => now()->addMonths(12), // Expires in 1 year
    'is_active' => true,
]);
```

### Linking to Product Variants

Gift cards can be linked to specific product variants:

```php
$variant = ProductVariant::find(1);

$giftCard = GiftCard::create([
    'code' => GiftCard::generateCode(),
    'product_variant_id' => $variant->id,
    'original_value' => $variant->price_override_excl ?? $variant->product->price_per_unit,
    'current_balance' => $variant->price_override_excl ?? $variant->product->price_per_unit,
    'is_active' => true,
]);
```

## Gift Card Validation

### Check if Valid

```php
$giftCard = GiftCard::where('code', 'GIFT2024ABC123')->first();

if ($giftCard && $giftCard->isValid()) {
    echo "Gift card is valid with balance: {$giftCard->formatted_balance}";
} else {
    echo "Gift card is invalid or expired";
}
```

### Validation Conditions

A gift card is considered valid when:
- `is_active` is true
- `current_balance` is greater than 0
- Not expired (if `expires_at` is set)

```php
// Manual validation checks
if (!$giftCard->is_active) {
    echo "Gift card is deactivated";
}

if ($giftCard->current_balance <= 0) {
    echo "Gift card has no remaining balance";
}

if ($giftCard->isExpired()) {
    echo "Gift card has expired";
}

if ($giftCard->isFullyRedeemed()) {
    echo "Gift card has been fully redeemed";
}
```

## Redeeming Gift Cards

### Basic Redemption

```php
$giftCard = GiftCard::where('code', 'GIFT2024ABC123')->first();
$redemptionAmount = 15.50;

if ($giftCard->redeem($redemptionAmount)) {
    echo "Successfully redeemed €{$redemptionAmount}";
    echo "Remaining balance: {$giftCard->formatted_balance}";
} else {
    echo "Redemption failed - insufficient balance or invalid gift card";
}
```

### Partial Redemption

Gift cards support partial redemption:

```php
$giftCard = GiftCard::create([
    'code' => GiftCard::generateCode(),
    'original_value' => 50.00,
    'current_balance' => 50.00,
    'is_active' => true,
]);

// First purchase: €20
$giftCard->redeem(20.00);
echo $giftCard->current_balance; // 30.00

// Second purchase: €15
$giftCard->redeem(15.00);
echo $giftCard->current_balance; // 15.00

// Third purchase: €15 (remaining balance)
$giftCard->redeem(15.00);
echo $giftCard->current_balance; // 0.00
echo $giftCard->redeemed_at; // Current timestamp
```

### Redemption with Validation

```php
function redeemGiftCard(string $code, float $amount): array
{
    $giftCard = GiftCard::where('code', $code)->first();
    
    if (!$giftCard) {
        return ['success' => false, 'message' => 'Gift card not found'];
    }
    
    if (!$giftCard->isValid()) {
        return ['success' => false, 'message' => 'Gift card is invalid or expired'];
    }
    
    if ($amount > $giftCard->current_balance) {
        return [
            'success' => false, 
            'message' => "Insufficient balance. Available: {$giftCard->formatted_balance}"
        ];
    }
    
    if ($giftCard->redeem($amount)) {
        return [
            'success' => true,
            'message' => 'Gift card redeemed successfully',
            'remaining_balance' => $giftCard->current_balance
        ];
    }
    
    return ['success' => false, 'message' => 'Redemption failed'];
}
```

## Cart Integration

Gift cards can be added to shopping carts:

### Adding Gift Cards to Cart

```php
use Darvis\MantaProduct\Services\CartService;

$cartService = new CartService();
$giftCard = GiftCard::find(1);

// Add gift card to cart
$cartItem = $cartService->addGiftCard($giftCard, 1);

echo "Added gift card {$giftCard->code} to cart";
```

### Cart Validation

The cart service automatically validates gift cards:

```php
$cart = $cartService->getCart();
$errors = $cartService->validateCart($cart);

// Will include errors for invalid gift cards
foreach ($errors as $error) {
    echo $error;
}
```

## Gift Card Relations

### Product Variant Relation

```php
$giftCard = GiftCard::with('variant.product')->find(1);

if ($giftCard->variant) {
    echo "Gift card for: {$giftCard->variant->product->title}";
    echo "Variant: {$giftCard->variant->variant_key}";
}
```

### Finding Gift Cards

```php
// Find by code
$giftCard = GiftCard::where('code', 'GIFT2024ABC123')->first();

// Find active gift cards
$activeCards = GiftCard::where('is_active', true)
    ->where('current_balance', '>', 0)
    ->get();

// Find expiring soon
$expiringSoon = GiftCard::where('expires_at', '<=', now()->addDays(30))
    ->where('is_active', true)
    ->where('current_balance', '>', 0)
    ->get();

// Find by recipient
$recipientCards = GiftCard::where('recipient_email', 'customer@example.com')->get();
```

## Code Generation

Gift cards use automatic code generation:

```php
// Default format: GIFT + Year + 6 random characters
$code = GiftCard::generateCode();
// Example: GIFT2024ABC123

// The method ensures uniqueness
$giftCard1 = GiftCard::create(['code' => GiftCard::generateCode(), ...]);
$giftCard2 = GiftCard::create(['code' => GiftCard::generateCode(), ...]);
// Both will have unique codes
```

### Custom Code Generation

You can also set custom codes:

```php
$giftCard = GiftCard::create([
    'code' => 'CUSTOM-GIFT-001',
    'original_value' => 75.00,
    'current_balance' => 75.00,
    'is_active' => true,
]);
```

## Formatting and Display

### Formatted Balance

```php
$giftCard = GiftCard::find(1);

echo $giftCard->formatted_balance; // €25.50
echo $giftCard->current_balance;   // 25.50
```

### Gift Card Status

```php
function getGiftCardStatus(GiftCard $giftCard): string
{
    if (!$giftCard->is_active) {
        return 'Inactive';
    }
    
    if ($giftCard->isExpired()) {
        return 'Expired';
    }
    
    if ($giftCard->isFullyRedeemed()) {
        return 'Fully Redeemed';
    }
    
    if ($giftCard->current_balance > 0) {
        return 'Active';
    }
    
    return 'Unknown';
}
```

## Best Practices

### Security
- Always validate gift card codes before redemption
- Use HTTPS for gift card transactions
- Log all gift card redemptions for audit trails
- Consider rate limiting for gift card validation attempts

### User Experience
- Send gift card codes via secure email
- Provide clear expiration information
- Show remaining balance after each use
- Allow customers to check balance without redemption

### Business Logic
- Set reasonable expiration periods (6-24 months)
- Consider partial redemption policies
- Implement gift card refund policies
- Track gift card usage analytics

### Example Implementation

```php
class GiftCardController extends Controller
{
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0.01'
        ]);
        
        $result = $this->redeemGiftCard(
            $request->code, 
            $request->amount
        );
        
        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'remaining_balance' => $result['remaining_balance']
            ]);
        }
        
        return response()->json([
            'error' => $result['message']
        ], 400);
    }
    
    public function checkBalance(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $giftCard = GiftCard::where('code', $request->code)->first();
        
        if (!$giftCard) {
            return response()->json(['error' => 'Gift card not found'], 404);
        }
        
        return response()->json([
            'code' => $giftCard->code,
            'balance' => $giftCard->current_balance,
            'formatted_balance' => $giftCard->formatted_balance,
            'is_valid' => $giftCard->isValid(),
            'expires_at' => $giftCard->expires_at?->format('Y-m-d'),
        ]);
    }
}
```

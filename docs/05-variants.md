# Variants

Product variants make it possible to have different versions of a product with their own prices, stock and properties.

## VariantMatrixService

The `VariantMatrixService` automatically generates all possible combinations of attributes.

### Basic Usage

```php
use Manta\Products\Services\VariantMatrixService;

$service = new VariantMatrixService();

// Generate variants for one attribute
$service->generate($product, [
    'color' => ['red', 'blue', 'green']
]);

// This creates 3 variants:
// - color:red
// - color:blue  
// - color:green
```

### Multiple Attributes

```php
// Generate matrix for multiple attributes
$service->generate($product, [
    'color' => ['red', 'blue'],
    'size' => ['small', 'large'],
    'material' => ['wood', 'metal']
]);

// This creates 8 variants (2 × 2 × 2):
// - color:red|size:small|material:wood
// - color:red|size:small|material:metal
// - color:red|size:large|material:wood
// - color:red|size:large|material:metal
// - color:blue|size:small|material:wood
// - color:blue|size:small|material:metal
// - color:blue|size:large|material:wood
// - color:blue|size:large|material:metal
```

## Variant Properties

### Overriding Prices

```php
use Manta\Products\Models\ProductVariant;

$variant = ProductVariant::where('variant_key', 'color:red|size:large')->first();

$variant->update([
    'price_override_excl' => 19.95,  // Overrides product price
    'price_override_incl' => 24.14,  // Optional: including tax
]);
```

### Stock per Variant

```php
$variant->update([
    'stock_quantity' => 25,
    'stock_reserved' => 5,
    'stock_available' => 20,  // Calculated: quantity - reserved
]);
```

### Capacity (for bookable products)

```php
$variant->update([
    'capacity' => 3,  // How many simultaneous reservations
]);
```

### Overriding Dimensions

```php
$variant->update([
    'length_mm' => 3000,   // 3 meter
    'width_mm' => 50,      // 50mm
    'height_mm' => 25,     // 25mm
]);

// Or use the meter accessors
$variant->lengthM = 3.0;
$variant->widthM = 0.05;
$variant->heightM = 0.025;
$variant->save();
```

## Retrieving Variants

### By Variant Key

```php
$variant = ProductVariant::where('product_id', $product->id)
    ->where('variant_key', 'color:red|size:large')
    ->first();
```

### With Attribute Values

```php
$variant = ProductVariant::with('values.attribute')
    ->where('product_id', $product->id)
    ->first();

foreach ($variant->values as $value) {
    echo "{$value->attribute->name}: {$value->value}\n";
}
```

### All Variants of a Product

```php
$variants = $product->variants()
    ->with('values')
    ->orderBy('variant_key')
    ->get();
```

## Advanced Features

### Conditional Variant Generation

```php
// Generate only specific combinations
$combinations = [
    ['color' => 'red', 'size' => 'small'],
    ['color' => 'blue', 'size' => 'large'],
];

foreach ($combinations as $combo) {
    $service->generateSingle($product, $combo);
}
```

### Deleting Variants

```php
$variant = ProductVariant::where('variant_key', 'color:red')->first();
$variant->delete();

// Or all variants of a product
$product->variants()->delete();
```

### Custom Variant Keys

```php
// Default format: attribute_code:value_code|attribute_code:value_code
// Custom format possible via service configuration
$service->setKeyFormat('{attribute}={value}&{attribute}={value}');
// Result: color=red&size=large
```

## Best Practices

1. **Use codes**: Use short, URL-friendly codes for attributes and values
2. **Sort attributes**: Use the `sort` column in `product_attributes` for consistent order
3. **Batch operations**: Generate all variants at once for better performance
4. **Cleanup**: Remove unused variants regularly

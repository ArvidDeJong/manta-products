# Models

This package contains various Eloquent models for managing products, attributes and variants.

## Product Model

The main model for products with unit pricing and dimensions.

```php
use Manta\Products\Models\Product;

$product = new Product([
    'active' => true,
    'title' => 'Aluminum Profile',
    'slug' => 'aluminum-profile',
    'product_type' => 'sellable', // sellable|bookable|both
    'unit_type' => 'meter',       // piece|meter|m2|m3
    'price_per_unit' => 14.95,
    'tax_rate' => 21.00,
    'calc_mode' => 'direct_length', // direct_length|dimensions_2d|dimensions_3d
]);
```

### Important Properties
- **Dimensions**: Stored in mm, accessible via `lengthM`, `widthM`, `heightM`
- **Unit types**: `piece`, `meter`, `m2`, `m3`
- **Calc modes**: Determines how units are calculated

## Attribute & AttributeValue Models

For defining product properties like color, size, etc.

```php
use Manta\Products\Models\Attribute;
use Manta\Products\Models\AttributeValue;

// Create an attribute
$color = Attribute::create([
    'name' => 'Color',
    'code' => 'color',
    'type' => 'color_swatches'
]);

// Add values
AttributeValue::create([
    'attribute_id' => $color->id,
    'value' => 'Red',
    'code' => 'red',
    'hex' => '#ff0000'
]);
```

### Attribute Types
- `color_swatches`: Color selection with hex values
- `dropdown`: Dropdown selection
- `text`: Free text input

## ProductVariant Model

For product variants with their own prices and properties.

```php
use Manta\Products\Models\ProductVariant;

$variant = ProductVariant::create([
    'product_id' => $product->id,
    'variant_key' => 'color:red|size:large',
    'price_override_excl' => 16.95,
    'capacity' => 5,
    'stock_quantity' => 100
]);
```

### Variant Properties
- **variant_key**: Unique key for fast lookups
- **price_override**: Overrides product price
- **capacity**: For bookable products
- **stock_quantity**: Stock per variant

## ProductAttribute (Pivot)

Links attributes to products with additional properties.

```php
$product->attributes()->attach($color->id, [
    'is_required' => true,
    'sort' => 1
]);
```

## Relations

### Product Relations
```php
$product->attributes()      // Linked attributes
$product->variants()        // Product variants
$product->resource()        // Linked resource (optional)
```

### Variant Relations
```php
$variant->product()         // Parent product
$variant->values()          // Attribute values
```

# Usage

This guide shows how to use the package for managing products with unit pricing.

## Basic Workflow

### 1. Creating a Product

```php
use Manta\Products\Models\Product;

$product = Product::create([
    'active' => true,
    'title' => 'Aluminum Profile 40x40mm',
    'slug' => 'aluminum-profile-40x40',
    'product_type' => 'sellable',
    'unit_type' => 'meter',
    'price_per_unit' => 14.95,
    'tax_rate' => 21.00,
    'calc_mode' => 'direct_length',
    'unit_step' => 0.01,
]);

// Set dimensions (in meters, stored as mm)
$product->lengthM = 6.0;  // 6 meter length
$product->widthM = 0.04;  // 40mm width  
$product->heightM = 0.04; // 40mm height
$product->save();
```

### 2. Adding Attributes

```php
use Manta\Products\Models\Attribute;
use Manta\Products\Models\AttributeValue;

// Color attribute
$color = Attribute::create([
    'name' => 'Color',
    'code' => 'color',
    'type' => 'color_swatches'
]);

// Color values
$red = AttributeValue::create([
    'attribute_id' => $color->id,
    'value' => 'Red',
    'code' => 'red',
    'hex' => '#ff0000'
]);

$blue = AttributeValue::create([
    'attribute_id' => $color->id,
    'value' => 'Blue', 
    'code' => 'blue',
    'hex' => '#0000ff'
]);

// Link attribute to product
$product->attributes()->attach($color->id, [
    'is_required' => true,
    'sort' => 1
]);
```

### 3. Generating Variants

```php
use Manta\Products\Services\VariantMatrixService;

$service = new VariantMatrixService();
$service->generate($product, [
    'color' => ['red', 'blue']
]);

// This automatically creates variants:
// - color:red
// - color:blue
```

### 4. Calculating Prices

```php
// For a specific length
$units = $product->normalizeUnits(['length_mm' => 2750]); // 2.75m
$pricing = $product->priceForUnits($units);

echo "Length: {$pricing['units']} meter\n";
echo "Price excl: €{$pricing['price_excl']}\n";
echo "Tax: €{$pricing['tax_amount']}\n";
echo "Price incl: €{$pricing['price_incl']}\n";
```

### 5. Retrieving Variants

```php
use Manta\Products\Models\ProductVariant;

// Find specific variant
$variant = ProductVariant::where('product_id', $product->id)
    ->where('variant_key', 'color:red')
    ->first();

// Override variant price
$variant->update([
    'price_override_excl' => 16.95,
    'stock_quantity' => 50
]);
```

## Advanced Examples

### Multiple Attributes

```php
// Add size attribute
$size = Attribute::create([
    'name' => 'Size',
    'code' => 'size', 
    'type' => 'dropdown'
]);

AttributeValue::create(['attribute_id' => $size->id, 'value' => 'Small', 'code' => 's']);
AttributeValue::create(['attribute_id' => $size->id, 'value' => 'Large', 'code' => 'l']);

$product->attributes()->attach($size->id, ['is_required' => false, 'sort' => 2]);

// Generate matrix with multiple attributes
$service->generate($product, [
    'color' => ['red', 'blue'],
    'size' => ['s', 'l']
]);
// Result: color:red|size:s, color:red|size:l, color:blue|size:s, color:blue|size:l
```

### Different Unit Types

```php
// M² product (surface area)
$carpet = Product::create([
    'title' => 'Carpet',
    'unit_type' => 'm2',
    'calc_mode' => 'dimensions_2d',
    'price_per_unit' => 25.00,
]);

// M³ product (volume)
$concrete = Product::create([
    'title' => 'Concrete',
    'unit_type' => 'm3', 
    'calc_mode' => 'dimensions_3d',
    'price_per_unit' => 85.00,
]);
```

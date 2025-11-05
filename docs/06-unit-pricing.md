# Unit Pricing

The unit pricing system makes it possible to sell products per piece, meter, square meter or cubic meter.

## Unit Types

### `piece` - Per Piece
For products sold per piece.

```php
$product = Product::create([
    'title' => 'Screw M6x20',
    'unit_type' => 'piece',
    'price_per_unit' => 0.15,
    'calc_mode' => 'direct_length', // Not relevant for pieces
]);

// Calculate price for 50 pieces
$pricing = $product->priceForUnits(['quantity' => 50]);
// Result: 50 × €0.15 = €7.50
```

### `meter` - Per Meter
For products sold per meter.

```php
$product = Product::create([
    'title' => 'Cable 2x1.5mm²',
    'unit_type' => 'meter', 
    'price_per_unit' => 2.50,
    'calc_mode' => 'direct_length',
    'unit_step' => 0.1, // Minimum 10cm steps
]);

// Price for 2.75 meter
$pricing = $product->priceForUnits(['length_mm' => 2750]);
// Result: 2.8m (rounded) × €2.50 = €7.00
```

### `m2` - Per Square Meter
For surface area products like carpet, paint, etc.

```php
$product = Product::create([
    'title' => 'Berber Carpet',
    'unit_type' => 'm2',
    'price_per_unit' => 25.00,
    'calc_mode' => 'dimensions_2d',
]);

// Set dimensions
$product->lengthM = 4.0;  // 4 meter width (roll width)
$product->save();

// Price for 3m × 2.5m = 7.5m²
$pricing = $product->priceForUnits([
    'length_mm' => 3000,
    'width_mm' => 2500
]);
// Result: 7.5m² × €25.00 = €187.50
```

### `m3` - Per Cubic Meter
For volume products like concrete, soil, etc.

```php
$product = Product::create([
    'title' => 'Concrete C20/25',
    'unit_type' => 'm3',
    'price_per_unit' => 85.00,
    'calc_mode' => 'dimensions_3d',
]);

// Price for 2m × 1.5m × 0.2m = 0.6m³
$pricing = $product->priceForUnits([
    'length_mm' => 2000,
    'width_mm' => 1500,
    'height_mm' => 200
]);
// Result: 0.6m³ × €85.00 = €51.00
```

## Calculation Modes

### `direct_length`
Uses only length for calculation.

```php
// For meter products
$units = $product->normalizeUnits(['length_mm' => 2750]);
// Result: 2.75 meter (or rounded according to unit_step)
```

### `dimensions_2d` 
Calculates surface area: length × width.

```php
// For m² products  
$units = $product->normalizeUnits([
    'length_mm' => 3000,
    'width_mm' => 2500
]);
// Result: 7.5 m²
```

### `dimensions_3d`
Calculates volume: length × width × height.

```php
// For m³ products
$units = $product->normalizeUnits([
    'length_mm' => 2000,
    'width_mm' => 1500, 
    'height_mm' => 200
]);
// Result: 0.6 m³
```

## Rounding Modes

Configure in `config/manta-product.php`:

```php
'default_rounding_mode' => 'ceil', // ceil|floor|round
'default_unit_step' => 0.01,
```

### `ceil` - Round Up
```php
// 2.71m with step 0.1 → 2.8m
// 7.51m² with step 0.5 → 8.0m²
```

### `floor` - Round Down  
```php
// 2.79m with step 0.1 → 2.7m
// 7.49m² with step 0.5 → 7.0m²
```

### `round` - Standard Rounding
```php
// 2.74m with step 0.1 → 2.7m
// 2.76m with step 0.1 → 2.8m
```

## Unit Steps

Define minimum step sizes:

```php
$product->unit_step = 0.1;   // 10cm steps for meter
$product->unit_step = 0.25;  // 25cm steps  
$product->unit_step = 1.0;   // Whole meter steps
```

## Pricing Response

The `priceForUnits()` method returns:

```php
[
    'units' => 2.8,           // Calculated/rounded units
    'price_excl' => 7.00,     // Price excluding tax
    'tax_rate' => 21.00,      // Tax percentage
    'tax_amount' => 1.47,     // Tax amount
    'price_incl' => 8.47,     // Price including tax
    'unit_type' => 'meter',   // Unit type
    'calc_mode' => 'direct_length'
]
```

## Examples by Industry

### Construction & Building
```php
// Steel beam per meter
Product::create([
    'title' => 'HEA 200 Steel Beam',
    'unit_type' => 'meter',
    'calc_mode' => 'direct_length',
    'price_per_unit' => 45.00,
    'unit_step' => 0.1
]);

// Concrete per m³
Product::create([
    'title' => 'Concrete C25/30',
    'unit_type' => 'm3', 
    'calc_mode' => 'dimensions_3d',
    'price_per_unit' => 95.00
]);
```

### Textile & Interior
```php
// Fabric per meter (fixed width)
Product::create([
    'title' => 'Curtain Fabric 280cm Wide',
    'unit_type' => 'meter',
    'calc_mode' => 'direct_length', 
    'price_per_unit' => 18.50
]);

// Carpet per m²
Product::create([
    'title' => 'Carpet Tiles 50x50cm',
    'unit_type' => 'm2',
    'calc_mode' => 'dimensions_2d',
    'price_per_unit' => 32.00
]);
```

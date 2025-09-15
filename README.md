# darvis/manta-product

Products, attributes & variants with unit pricing (meter/m²/m³) for Laravel 11.  
Also works great alongside reservations (rooms/slots) but does not depend on a reservation package.

- ✅ **Products** with dimensions (mm) and unit pricing (piece/meter/m²/m³)  
- ✅ **Attributes & Values** (e.g. Color, Size)  
- ✅ **Product Variants** with per-variant price/stock/capacity/dimensions overrides  
- ✅ **Variant Matrix Generator** (cartesian builder)  
- ✅ Zero routes; pure Eloquent package with migrations & config

> Opinionated: integers for dimensions (mm), alphabetically sorted keys in arrays, clean comments.

---

## Installation

```bash
composer require darvis/manta-product
php artisan vendor:publish --tag=manta-product-config
php artisan migrate
```

Laravel will auto-discover the service provider.

---

## Configuration

`config/manta-product.php`

- `default_tax_rate` — Used if neither product nor variant has a tax rate.  
- `currency` — Display currency code (not enforced in DB).  
- `sku_pattern` — Pattern for generated SKUs. Tokens: `{'}}product_id{{'}, {'}}codes{{'}, {'}}values{{'}`.  
- `default_rounding_mode` — `ceil|floor|round` for unit calculations.  
- `default_unit_step` — Minimal step size when not set on product/variant.

---

## Database Overview

- `products` — base product including unit pricing and dimensions (mm).  
- `attributes`, `attribute_values` — define properties like Color/Size.  
- `product_attributes` — pivot assigning attributes to a product.  
- `product_variants`, `product_variant_values` — variant matrix + values.

_This package does not include orders or reservations — keep those in your app._

---

## Models & Helpers

### Product
```php
use Manta\Products\Models\Product;

$product = Product::create([
    'active'         => true,
    'title'          => 'Aluminium profiel',
    'slug'           => 'aluminium-profiel',
    'product_type'   => 'sellable',        // or bookable|both
    'unit_type'      => 'meter',           // meter|m2|m3|piece
    'price_per_unit' => 14.95,
    'tax_rate'       => 21.00,
    'calc_mode'      => 'direct_length',   // direct_length|dimensions_2d|dimensions_3d
    'unit_step'      => 0.01,
]);

// Set dimensions in meters via accessors (stored as mm)
$product->lengthM = 2.5; // => length_mm = 2500
$product->save();

$units  = $product->normalizeUnits(['length_mm' => 2750]); // 2.75m -> step/rounding applied
$prices = $product->priceForUnits($units);
```

### Attributes & Values
```php
use Manta\Products\Models\Attribute;
use Manta\Products\Models\AttributeValue;

$color = Attribute::create(['name' => 'Kleur','code' => 'color','type' => 'color_swatches']);
AttributeValue::create(['attribute_id'=>$color->id,'value'=>'Rood','code'=>'red','hex'=>'#ff0000']);
AttributeValue::create(['attribute_id'=>$color->id,'value'=>'Blauw','code'=>'blue','hex'=>'#0000ff']);
```

Attach attribute to product (defines axis order in UI):
```php
$product->attributes()->attach($color->id, ['is_required'=>true, 'sort'=>1]);
```

### Variants
```php
use Manta\Products\Services\VariantMatrixService;

$service = new VariantMatrixService();
$service->generate($product, [
  'color' => ['red','blue'],
]);
```

Find variant by selected values:
```php
use Manta\Products\Models\ProductVariant;

$variant = ProductVariant::where('product_id', $product->id)
  ->where('variant_key', 'color:red')
  ->first();
```

Variants can override pricing/dimensions/capacity:
```php
$variant->update([
  'price_override_excl' => 15.95,
  'capacity'            => 2,       // useful for bookable stock
  'length_mm'           => 3000,
]);
```

---

## Tips

- Store raw **mm** in DB; use `lengthM/widthM/heightM` to work in meters from code.
- Use `variant_key` (e.g. `color:red|size:l`) for fast lookups in UI.
- Keep orders/reservations in your app, but store `product_id` and optional `product_variant_id` on line items.

---

## Extending

- Add your own Price Rules table and reference `product_id` (and optional `product_variant_id`) with a `priority` column.
- Add media tables to link images per product/variant.
- Create observers or policies as needed; package keeps concerns minimal.

---

## Testing

Package ships without tests for brevity. Recommended:
- Model factories for Product/Attribute/Variant
- Unit tests for `VariantMatrixService` and unit calc helpers

---

## License

MIT


---

## Reservations & Availability (optional add-on in this package)

This package includes neutral tables to support booking flows:

- `resources`, `rooms`
- `opening_hours`, `calendar_exceptions`
- `reservations`, `reservation_items`, `holds`

> The package does **not** define your `users`, `staff`, or `customers` tables. Foreign keys are nullable for portability.

### Basic availability
Use `Manta\Products\Services\AvailabilityService` to compute used quantity (reservations + active holds) for an interval. Compare that to your `products.capacity` or `rooms.capacity` to decide availability.



---

## Demo (optioneel)

Enable demo screens and routes:

```php
// config/manta-product.php
'enable_demo' => true,
'demo_prefix' => 'manta-product-demo',
```

Then visit `/manta-product-demo` to see:
- Product list
- 7-day slot generator based on opening hours & exceptions

> Seed demo data:
```bash
php artisan db:seed --class="Manta\Products\Database\Seeders\MantaProductsDemoSeeder"
```


---

## Tables (overview)

- products (now includes optional resource_id)
- attributes, attribute_values
- product_attributes (pivot product↔attribute)
- product_variants, product_variant_values
- resources, rooms
- opening_hours (polymorphic: products/resources/rooms)
- calendar_exceptions (polymorphic)
- price_rules
- reservations, reservation_items, holds

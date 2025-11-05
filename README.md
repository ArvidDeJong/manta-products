# Manta Products

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darvis/manta-product.svg?style=flat-square)](https://packagist.org/packages/darvis/manta-product)
[![Total Downloads](https://img.shields.io/packagist/dt/darvis/manta-product.svg?style=flat-square)](https://packagist.org/packages/darvis/manta-product)
[![PHP Version Require](https://img.shields.io/packagist/php-v/darvis/manta-product?style=flat-square)](https://packagist.org/packages/darvis/manta-product)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/darvis/manta-product.svg?style=flat-square)](https://packagist.org/packages/darvis/manta-product)

A powerful Laravel package for managing products, attributes and variants with advanced unit pricing (meter/m²/m³). Perfect for e-commerce and reservation systems.

## ✨ Features

- 🏷️ **Products** with dimensions (mm) and unit pricing (piece/meter/m²/m³)  
- 🎨 **Attributes & Values** (e.g. Color, Size, Material)  
- 🔄 **Product Variants** with per-variant price/stock/capacity/dimension overrides  
- 🧮 **Variant Matrix Generator** (cartesian product builder)  
- 🎁 **Gift Cards** with balance tracking and redemption system
- 🛒 **Shopping Cart** with product and gift card support
- 📅 **Reservations & Availability** (optional)
- 🚀 **Zero routes** - pure Eloquent package with migrations & config
- 🧪 **Laravel 12 compatible** with PHP 8.2+

> **Opinionated design**: Integers for dimensions (mm), alphabetically sorted keys, clean code comments.

## 📋 Table of Contents

- [Installation](#-installation)
- [Configuration](#-configuration)  
- [Quick Start](#-quick-start)
- [Documentation](#-documentation)
- [Demo](#-demo)
- [Extending](#-extending)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🚀 Installation

```bash
composer require darvis/manta-product
php artisan vendor:publish --tag=manta-product-config
php artisan migrate
```

Laravel will auto-discover the service provider.

## ⚙️ Configuration

See `config/manta-product.php` for all options like tax rate, rounding mode and SKU pattern.

[📖 Full configuration documentation](docs/02-configuration.md)

## ⚡ Quick Start

```php
use Manta\Products\Models\Product;
use Manta\Products\Models\Attribute;
use Manta\Products\Services\VariantMatrixService;

// 1. Create a product
$product = Product::create([
    'title' => 'Aluminum Profile',
    'unit_type' => 'meter',
    'price_per_unit' => 14.95,
    'calc_mode' => 'direct_length',
]);

// 2. Add attributes
$color = Attribute::create(['name' => 'Color', 'code' => 'color']);
$product->attributes()->attach($color->id);

// 3. Generate variants
$service = new VariantMatrixService();
$service->generate($product, ['color' => ['red', 'blue']]);

// 4. Calculate pricing
$pricing = $product->priceForUnits(['length_mm' => 2500]); // 2.5m
echo "Price: €{$pricing['price_incl']}";
```

## 📚 Documentation

- [📦 Installation](docs/01-installation.md)
- [⚙️ Configuration](docs/02-configuration.md)  
- [🏗️ Models](docs/03-models.md)
- [💡 Usage](docs/04-usage.md)
- [🔄 Variants](docs/05-variants.md)
- [💰 Unit Pricing](docs/06-unit-pricing.md)
- [🔧 Extending](docs/07-extending.md)
- [📅 Reservations](docs/08-reservations.md)
- [📊 Availability](docs/09-availability.md)
- [🔧 Troubleshooting](docs/10-troubleshooting.md)
- [🎁 Gift Cards](docs/11-giftcards.md)

## 🗄️ Database Overview

- `products` — Base product with unit pricing and dimensions (mm)
- `attributes`, `attribute_values` — Properties like Color/Size  
- `product_attributes` — Pivot table for product ↔ attribute linking
- `product_variants`, `product_variant_values` — Variant matrix + values
- `manta_gift_cards` — Digital gift cards with balance tracking
- `manta_carts`, `manta_cart_items` — Shopping cart functionality

_This package does not include orders or reservations — keep those in your app._

---

## 🏗️ Models & Helpers

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

## 🧪 Testing

The package includes basic test setup. Recommended tests:

```bash
# Run tests (when available)
composer test

# Run static analysis
composer analyse
```

Recommended test coverage:
- Model factories for Product/Attribute/Variant
- Unit tests for `VariantMatrixService` 
- Unit calc helpers testing
- Integration tests for pricing

## 🤝 Contributing

We welcome contributions! See [CONTRIBUTING.md](CONTRIBUTING.md) for details.

- 🐛 **Bug reports**: Use the [bug report template](.github/ISSUE_TEMPLATE/bug_report.md)
- ✨ **Feature requests**: Use the [feature request template](.github/ISSUE_TEMPLATE/feature_request.md)  
- 🔧 **Pull requests**: Follow the [PR template](.github/pull_request_template.md)

## 🔒 Security

For security issues, see [SECURITY.md](SECURITY.md) for responsible disclosure.

## 📄 License

This package is open source software licensed under the [MIT license](LICENSE).


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

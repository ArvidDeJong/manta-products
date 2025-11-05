# Configuration

The configuration file `config/manta-product.php` contains all important settings for the package.

## Publishing Configuration

```bash
php artisan vendor:publish --tag=manta-product-config
```

## Configuration Options

### Basic Settings

```php
// config/manta-product.php

return [
    // Default tax rate if product/variant has no own rate
    'default_tax_rate' => 21.00,
    
    // Currency code for display (not enforced in database)
    'currency' => 'EUR',
    
    // Pattern for generated SKUs
    // Tokens: {{product_id}}, {{codes}}, {{values}}
    'sku_pattern' => '{{product_id}}-{{codes}}',
    
    // Default rounding method for unit calculations
    'default_rounding_mode' => 'ceil', // ceil|floor|round
    
    // Minimum step size when not set on product/variant
    'default_unit_step' => 0.01,
];
```

### Demo Settings

```php
// Enable demo functionality
'enable_demo' => env('MANTA_PRODUCT_DEMO', false),
'demo_prefix' => 'manta-product-demo',
```

### Environment Variables

Add these to your `.env` file:

```env
MANTA_PRODUCT_DEMO=false
```

## Examples

### Custom SKU Pattern
```php
'sku_pattern' => 'PRD-{{product_id}}-{{codes}}-{{values}}'
// Result: PRD-1-color-red
```

### Different Rounding Modes
- `ceil`: Always round up (2.1 → 3)
- `floor`: Always round down (2.9 → 2)  
- `round`: Standard rounding (2.4 → 2, 2.6 → 3)

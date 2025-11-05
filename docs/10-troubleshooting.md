# Troubleshooting

Common problems and solutions for Manta Products.

## 🚨 Installation Issues

### Package Not Found
```bash
# Error: Package darvis/manta-product not found
```

**Solution:**
```bash
# Check if you're using the correct package name
composer require darvis/manta-product

# Or try with explicit version
composer require darvis/manta-product:^0.1
```

### Migration Errors
```bash
# Error: Base table or view not found
```

**Solution:**
```bash
# Make sure you've run the migrations
php artisan migrate

# Or force the migrations
php artisan migrate --force
```

## 🔧 Configuration Issues

### Config Not Found
```php
// Error: config/manta-product.php not found
```

**Solution:**
```bash
# Publish the configuration
php artisan vendor:publish --tag=manta-product-config

# Or force overwrite
php artisan vendor:publish --tag=manta-product-config --force
```

### Service Provider Not Loaded
```php
// Error: Class 'Manta\Products\Models\Product' not found
```

**Solution:**
```bash
# Clear config cache
php artisan config:clear

# Dump autoload
composer dump-autoload

# Clear all caches
php artisan optimize:clear
```

## 💰 Pricing Issues

### Wrong Unit Calculation
```php
// Expected: 2.5m × €10 = €25
// Getting: 2.8m × €10 = €28
```

**Cause:** Unit step rounding

**Solution:**
```php
// Check unit_step setting
$product->unit_step = 0.01; // For 1cm precision
$product->save();

// Or change rounding mode
// config/manta-product.php
'default_rounding_mode' => 'round', // instead of 'ceil'
```

### Tax Calculation Incorrect
```php
// Expected: €10 + 21% = €12.10
// Getting: €10 + 21% = €12.00
```

**Solution:**
```php
// Check tax_rate on product
$product->tax_rate = 21.00; // Not 0.21

// Or check default in config
// config/manta-product.php
'default_tax_rate' => 21.00,
```

## 🔄 Variant Issues

### Variants Not Being Generated
```php
$service = new VariantMatrixService();
$service->generate($product, ['color' => ['red', 'blue']]);
// No variants created
```

**Solution:**
```php
// Check if attributes are linked
$product->attributes()->attach($colorAttribute->id);

// Check if attribute values exist
$red = AttributeValue::where('code', 'red')->first();
if (!$red) {
    AttributeValue::create([
        'attribute_id' => $colorAttribute->id,
        'code' => 'red',
        'value' => 'Red'
    ]);
}
```

### Variant Key Format Issues
```php
// Error: Variant key 'color:red|size:large' not found
```

**Solution:**
```php
// Check exact format
$variant = ProductVariant::where('variant_key', 'color:red|size:large')->first();

// Or use helper method
$variant = $product->findVariantByValues(['color' => 'red', 'size' => 'large']);
```

## 📈 Performance Issues

### Slow Variant Queries
```php
// N+1 query problem with variants
foreach ($product->variants as $variant) {
    echo $variant->values->first()->value; // N+1!
}
```

**Solution:**
```php
// Eager load relations
$variants = $product->variants()
    ->with(['values.attribute'])
    ->get();

foreach ($variants as $variant) {
    echo $variant->values->first()->value; // No extra queries
}
```

### Large Variant Matrices
```php
// 5 attributes × 10 values = 100,000 variants!
$service->generate($product, [
    'color' => range(1, 10),
    'size' => range(1, 10),
    'material' => range(1, 10),
    'finish' => range(1, 10),
    'grade' => range(1, 10),
]);
```

**Solution:**
```php
// Limit combinations
$service->generate($product, [
    'color' => ['red', 'blue'], // Max 2-3 values per attribute
    'size' => ['s', 'm', 'l']
]);

// Or use conditional generation
$validCombinations = [
    ['color' => 'red', 'size' => 's'],
    ['color' => 'blue', 'size' => 'l'],
];

foreach ($validCombinations as $combo) {
    $service->generateSingle($product, $combo);
}
```

## 🗄️ Database Problemen

### Foreign key constraints
```bash
# Error: Cannot add foreign key constraint
```

**Oplossing:**
```bash
# Controleer database engine (InnoDB required)
# Controleer character set (utf8mb4)

# In migration:
Schema::create('products', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->charset = 'utf8mb4';
    $table->collation = 'utf8mb4_unicode_ci';
});
```

### Memory limit bij grote datasets
```bash
# Fatal error: Allowed memory size exhausted
```

**Oplossing:**
```php
// Gebruik chunking voor grote datasets
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process product
    }
});

// Of verhoog memory limit tijdelijk
ini_set('memory_limit', '512M');
```

## 🧪 Testing Problemen

### Factories niet gevonden
```php
// Error: Unable to locate factory for [Manta\Products\Models\Product]
```

**Oplossing:**
```php
// Gebruik package factories
use Manta\Products\Database\Factories\ProductFactory;

$product = ProductFactory::new()->create();

// Of maak eigen factory
// database/factories/ProductFactory.php
class ProductFactory extends Factory {
    protected $model = Product::class;
    // ...
}
```

## 🔍 Debug Tips

### Enable query logging
```php
// In je controller/service
DB::enableQueryLog();

// Je code hier
$product->priceForUnits(['length_mm' => 2500]);

// Check queries
dd(DB::getQueryLog());
```

### Variant debugging
```php
// Debug variant generation
$service = new VariantMatrixService();
$service->setDebug(true); // Als beschikbaar
$service->generate($product, $matrix);

// Check variant keys
$product->variants->pluck('variant_key')->dump();
```

### Model debugging
```php
// Check model attributes
$product->getAttributes();

// Check relations
$product->getRelations();

// Check dirty attributes
$product->getDirty();
```

## 📞 Need Help?

If you continue to experience issues:

1. **Check the logs**: `storage/logs/laravel.log`
2. **Enable debug mode**: `APP_DEBUG=true` in `.env`
3. **Open an issue**: [GitHub Issues](https://github.com/ArvidDeJong/manta-products/issues)
4. **Send an email**: [info@arvid.nl](mailto:info@arvid.nl)

Always include:
- Laravel version
- PHP version  
- Package version
- Error message
- Code example

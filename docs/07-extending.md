# Extending

Het Manta Products package is ontworpen om uitbreidbaar te zijn. Hier zijn verschillende manieren om het package uit te breiden.

## Price Rules

Voeg je eigen prijsregels toe voor complexe pricing logica.

```php
// app/Models/PriceRule.php
class PriceRule extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id', // optioneel
        'name',
        'type', // discount|markup|fixed
        'value',
        'priority',
        'starts_at',
        'ends_at',
        'min_quantity',
        'max_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
```

### Price Rule Service

```php
// app/Services/PriceRuleService.php
class PriceRuleService
{
    public function applyRules(Product $product, array $context = [])
    {
        $rules = PriceRule::where('product_id', $product->id)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->orderBy('priority')
            ->get();

        $basePrice = $product->price_per_unit;
        
        foreach ($rules as $rule) {
            $basePrice = $this->applyRule($rule, $basePrice, $context);
        }

        return $basePrice;
    }

    private function applyRule(PriceRule $rule, float $price, array $context): float
    {
        switch ($rule->type) {
            case 'discount':
                return $price * (1 - $rule->value / 100);
            case 'markup':
                return $price * (1 + $rule->value / 100);
            case 'fixed':
                return $rule->value;
            default:
                return $price;
        }
    }
}
```

## Media & Images

Voeg media ondersteuning toe voor producten en varianten.

```php
// app/Models/ProductMedia.php
class ProductMedia extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id', // optioneel
        'type', // image|video|document
        'path',
        'filename',
        'mime_type',
        'size',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
```

### Product Model Extension

```php
// Extend het Product model via traits of inheritance
trait HasMedia
{
    public function media()
    {
        return $this->hasMany(ProductMedia::class);
    }

    public function images()
    {
        return $this->media()->where('type', 'image');
    }

    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    public function addMedia(string $path, array $attributes = []): ProductMedia
    {
        return $this->media()->create(array_merge([
            'path' => $path,
            'type' => 'image',
        ], $attributes));
    }
}

// In je AppServiceProvider
Product::mixin(new HasMedia());
```

## Custom Attributes

Voeg custom attribute types toe.

```php
// app/Enums/AttributeType.php
enum AttributeType: string
{
    case ColorSwatches = 'color_swatches';
    case Dropdown = 'dropdown';
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Date = 'date';
    case File = 'file';
}
```

### Attribute Renderer Service

```php
// app/Services/AttributeRendererService.php
class AttributeRendererService
{
    public function render(Attribute $attribute, $value = null): string
    {
        return match($attribute->type) {
            'color_swatches' => $this->renderColorSwatches($attribute, $value),
            'dropdown' => $this->renderDropdown($attribute, $value),
            'number' => $this->renderNumber($attribute, $value),
            'boolean' => $this->renderBoolean($attribute, $value),
            default => $this->renderText($attribute, $value),
        };
    }

    private function renderColorSwatches(Attribute $attribute, $value): string
    {
        $html = '<div class="color-swatches">';
        foreach ($attribute->values as $attributeValue) {
            $selected = $value === $attributeValue->code ? 'selected' : '';
            $html .= sprintf(
                '<div class="color-swatch %s" data-value="%s" style="background-color: %s"></div>',
                $selected,
                $attributeValue->code,
                $attributeValue->hex
            );
        }
        $html .= '</div>';
        return $html;
    }
}
```

## Observers

Voeg observers toe voor automatische acties.

```php
// app/Observers/ProductObserver.php
class ProductObserver
{
    public function created(Product $product): void
    {
        // Auto-generate SKU
        if (empty($product->sku)) {
            $product->sku = 'PRD-' . str_pad($product->id, 6, '0', STR_PAD_LEFT);
            $product->save();
        }

        // Create default variant if no attributes
        if ($product->attributes()->count() === 0) {
            $product->variants()->create([
                'variant_key' => 'default',
                'is_default' => true,
            ]);
        }
    }

    public function updating(Product $product): void
    {
        // Update variant prices when product price changes
        if ($product->isDirty('price_per_unit')) {
            $product->variants()
                ->whereNull('price_override_excl')
                ->update(['needs_price_update' => true]);
        }
    }
}

// In AppServiceProvider::boot()
Product::observe(ProductObserver::class);
```

## Policies

Voeg authorization toe.

```php
// app/Policies/ProductPolicy.php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('products.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermission('products.view') && 
               ($product->active || $user->hasPermission('products.view_inactive'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('products.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermission('products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermission('products.delete') && 
               $product->variants()->count() === 0;
    }
}
```

## Events

Voeg custom events toe.

```php
// app/Events/ProductPriceChanged.php
class ProductPriceChanged
{
    public function __construct(
        public Product $product,
        public float $oldPrice,
        public float $newPrice
    ) {}
}

// app/Listeners/UpdateVariantPrices.php
class UpdateVariantPrices
{
    public function handle(ProductPriceChanged $event): void
    {
        $event->product->variants()
            ->whereNull('price_override_excl')
            ->each(function (ProductVariant $variant) use ($event) {
                // Update cached prices or trigger recalculation
                $variant->touch();
            });
    }
}
```

## API Resources

Voeg API resources toe voor JSON responses.

```php
// app/Http/Resources/ProductResource.php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => [
                'per_unit' => $this->price_per_unit,
                'currency' => config('manta-product.currency'),
                'tax_rate' => $this->tax_rate,
            ],
            'dimensions' => [
                'length' => $this->lengthM,
                'width' => $this->widthM,
                'height' => $this->heightM,
                'unit' => 'meter',
            ],
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'attributes' => AttributeResource::collection($this->whenLoaded('attributes')),
        ];
    }
}
```

## Best Practices

1. **Gebruik traits** voor herbruikbare functionaliteit
2. **Observers** voor automatische acties
3. **Policies** voor authorization
4. **Events** voor loose coupling
5. **Services** voor complexe business logic
6. **Resources** voor API responses
7. **Houd het simpel** - voeg alleen toe wat je nodig hebt

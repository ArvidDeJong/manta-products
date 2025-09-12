<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Manta\Products\Traits\HasDimensions;

class ProductVariant extends Model
{
    use HasFactory;
    use HasDimensions;

    protected $table = 'product_variants';

    protected $fillable = [
        'active',
        'capacity',
        'height_mm',
        'length_mm',
        'meta',
        'price_override_excl',
        'product_id',
        'sku',
        'stock_qty',
        'tax_rate',
        'title',
        'unit_step',
        'unit_type',
        'calc_mode',
        'wastage_pct',
        'rounding_mode',
        'variant_key',
        'width_mm',
    ];

    protected $casts = [
        'active'             => 'bool',
        'capacity'           => 'int',
        'meta'               => 'array',
        'price_override_excl'=> 'decimal:2',
        'stock_qty'          => 'int',
        'tax_rate'           => 'decimal:2',
        'wastage_pct'        => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class)->with(['attribute','attributeValue']);
    }

    public function effectiveUnitPriceExcl(): float
    {
        return (float) ($this->price_override_excl ?? $this->product->price_per_unit ?? 0);
    }

    public function effectiveTaxRate(): float
    {
        return (float) ($this->tax_rate ?? $this->product->tax_rate ?? config('manta-products.default_tax_rate', 21.00));
    

    protected static function newFactory()
    {
        return \Manta\Products\Database\Factories\ProductVariantFactory::new();
    }
}

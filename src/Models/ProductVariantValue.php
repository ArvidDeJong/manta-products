<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    protected $table = 'manta_product_variant_values';

    protected $fillable = [
        'attribute_id',
        'attribute_value_id',
        'product_variant_id',
    ];

    public $timestamps = false;

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}

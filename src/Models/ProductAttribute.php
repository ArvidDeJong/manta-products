<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'manta_product_attributes';
    public $timestamps = false;

    protected $fillable = [
        'attribute_id',
        'is_required',
        'product_id',
        'sort',
    ];

    protected $casts = [
        'is_required' => 'bool',
        'sort'        => 'int',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

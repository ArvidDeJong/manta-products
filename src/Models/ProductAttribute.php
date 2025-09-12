<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';
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
}

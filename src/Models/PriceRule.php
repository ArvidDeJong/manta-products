<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceRule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_price_rules';

    protected $fillable = [
        'amount',
        'combinable',
        'days_of_week',
        'max_blocks',
        'max_persons',
        'metadata',
        'min_blocks',
        'min_persons',
        'name',
        'priority',
        'product_id',
        'rule_type',
        'tax_rate',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'tax_rate'    => 'decimal:2',
        'priority'    => 'int',
        'metadata'    => 'array',
        'valid_from'  => 'datetime',
        'valid_until' => 'datetime',
        'combinable'  => 'bool',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\PriceRuleFactory::new();
    }
}

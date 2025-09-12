<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_attribute_values';

    protected $fillable = [
        'attribute_id',
        'code',
        'hex',
        'sort',
        'value',
    ];

    protected $casts = [
        'sort' => 'int',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\AttributeValueFactory::new();
    }
}

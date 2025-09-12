<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;
    protected $table = 'attribute_values';

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
    

    protected static function newFactory()
    {
        return \Manta\Products\Database\Factories\AttributeValueFactory::new();
    }
}

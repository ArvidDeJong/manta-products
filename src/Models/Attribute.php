<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_attributes';

    protected $fillable = [
        'code',
        'config',
        'name',
        'sort',
        'type',
    ];

    protected $casts = [
        'config' => 'array',
        'sort'   => 'int',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\AttributeFactory::new();
    }
}

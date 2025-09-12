<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_resources';

    protected $fillable = [
        'active',
        'capacity',
        'location',
        'meta',
        'title',
    ];

    protected $casts = [
        'active'   => 'bool',
        'capacity' => 'int',
        'meta'     => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function openingHours()
    {
        return $this->morphMany(OpeningHour::class, 'owner');
    }

    public function exceptions()
    {
        return $this->morphMany(CalendarException::class, 'owner');
    }

    public static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\ResourceFactory::new();
    }
}

<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpeningHour extends Model
{
    use HasFactory;

    protected $table = 'manta_opening_hours';
    public $timestamps = false;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'weekday',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'weekday' => 'int',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\OpeningHourFactory::new();
    }
}

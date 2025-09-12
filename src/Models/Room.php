<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_rooms';

    protected $fillable = [
        'active',
        'capacity',
        'meta',
        'name',
        'slug',
    ];

    protected $casts = [
        'active'   => 'bool',
        'capacity' => 'int',
        'meta'     => 'array',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
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
        return \Darvis\MantaProduct\Database\Factories\RoomFactory::new();
    }
}

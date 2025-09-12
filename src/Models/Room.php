<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';

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
    

    protected static function newFactory()
    {
        return \Manta\Products\Database\Factories\RoomFactory::new();
    }
}

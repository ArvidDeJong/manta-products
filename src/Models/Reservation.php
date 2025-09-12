<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'manta_reservations';

    protected $fillable = [
        'channel',
        'currency',
        'customer_id',
        'ends_at',
        'meta',
        'notes',
        'paid_at',
        'reference',
        'room_id',
        'staff_id',
        'starts_at',
        'status',
        'total_excl',
        'total_incl',
        'total_tax',
        'user_id',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'paid_at'    => 'datetime',
        'meta'       => 'array',
        'total_excl' => 'decimal:2',
        'total_tax'  => 'decimal:2',
        'total_incl' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\ReservationFactory::new();
    }
}

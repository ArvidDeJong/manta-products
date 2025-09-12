<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarException extends Model
{
    use HasFactory;
    protected $table = 'calendar_exceptions';

    protected $fillable = [
        'date',
        'end_time',
        'is_closed',
        'note',
        'owner_id',
        'owner_type',
        'start_time',
    ];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'bool',
    ];

    public function owner()
    {
        return $this->morphTo();
    

    protected static function newFactory()
    {
        return \Manta\Products\Database\Factories\CalendarExceptionFactory::new();
    }
}

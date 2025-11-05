<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hold extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_holds';

    protected $fillable = [
        'ends_at',
        'expires_at',
        'product_id',
        'quantity',
        'resource_id',
        'starts_at',
        'token',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'expires_at' => 'datetime',
        'quantity'   => 'int',
    ];

    protected static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\HoldFactory::new();
    }
}

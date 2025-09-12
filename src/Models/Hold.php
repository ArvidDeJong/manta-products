<?php

namespace Manta\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hold extends Model
{
    use HasFactory;
    protected $table = 'holds';

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
        return \Manta\Products\Database\Factories\HoldFactory::new();
    }
}

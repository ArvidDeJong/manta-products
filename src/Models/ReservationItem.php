<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manta_reservation_items';

    protected $fillable = [
        'blocks',
        'ends_at',
        'line_total_excl',
        'line_total_incl',
        'line_total_tax',
        'persons',
        'price_breakdown',
        'product_id',
        'product_variant_id',
        'quantity',
        'reservation_id',
        'starts_at',
        'tax_rate',
        'unit_price_excl',
    ];

    protected $casts = [
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
        'price_breakdown' => 'array',
        'unit_price_excl' => 'decimal:2',
        'tax_rate'        => 'decimal:2',
        'line_total_excl' => 'decimal:2',
        'line_total_tax'  => 'decimal:2',
        'line_total_incl' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\ReservationItemFactory::new();
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

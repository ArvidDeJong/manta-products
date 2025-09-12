<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Darvis\MantaProduct\Traits\HasDimensions;

class Product extends Model
{
    use HasFactory;
    use HasDimensions;
    use SoftDeletes;

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(\Darvis\MantaProduct\Models\Resource::class, 'product_resource');
    }

    protected $table = 'manta_products';

    protected $fillable = [
        'active',
        'block_size',
        'capacity',
        'calc_mode',
        'dimension_unit',
        'height_mm',
        'length_mm',
        'max_order_qty',
        'max_persons',
        'meta',
        'min_order_qty',
        'min_persons',
        'price_per_unit',
        'product_type',
        'rounding_mode',
        'slug',
        'tax_rate',
        'time_unit',
        'title',
        'unit_step',
        'unit_type',
        'wastage_pct',
        'width_mm',
    ];

    protected $casts = [
        'active'        => 'bool',
        'capacity'      => 'int',
        'meta'          => 'array',
        'price_per_unit' => 'decimal:2',
        'tax_rate'      => 'decimal:2',
        'wastage_pct'   => 'decimal:2',
    ];

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(\Darvis\MantaProduct\Models\Attribute::class, 'product_attributes')
            ->withPivot('value', 'sort_order')
            ->orderBy('pivot_sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(\Darvis\MantaProduct\Models\ProductVariant::class);
    }

    public function isSellable(): bool
    {
        return in_array($this->product_type, ['sellable', 'both'], true);
    }

    public function normalizeUnits(array $input): float
    {
        $unitType = $this->unit_type;
        $round    = $this->rounding_mode ?: config('manta-products.default_rounding_mode', 'round');
        $step     = $this->unit_step ?: (float) config('manta-products.default_unit_step', 0.01);

        $units = match ($this->calc_mode) {
            'direct_length' => ($input['length_mm'] ?? $this->length_mm ?? 0) / 1000,
            'dimensions_2d' => (($input['length_mm'] ?? $this->length_mm ?? 0) / 1000) * (($input['width_mm'] ?? $this->width_mm ?? 0) / 1000),
            'dimensions_3d' => (($input['length_mm'] ?? $this->length_mm ?? 0) / 1000) * (($input['width_mm'] ?? $this->width_mm ?? 0) / 1000) * (($input['height_mm'] ?? $this->height_mm ?? 0) / 1000),
            default          => (float) ($input['units'] ?? 1),
        };

        if ($this->wastage_pct) {
            $units *= (1 + ($this->wastage_pct / 100));
        }

        $factor = 1 / max($step, 0.0001);
        $units = match ($round) {
            'ceil'  => ceil($units * $factor) / $factor,
            'floor' => floor($units * $factor) / $factor,
            default => round($units * $factor) / $factor,
        };

        if ($this->min_order_qty) $units = max($units, (float)$this->min_order_qty);
        if ($this->max_order_qty) $units = min($units, (float)$this->max_order_qty);

        return (float) $units;
    }

    public function priceForUnits(float $units): array
    {
        $excl = (float) ($this->price_per_unit ?? 0) * $units;
        $taxRate = (float) ($this->tax_rate ?? config('manta-products.default_tax_rate', 21.00));
        $tax  = $excl * ($taxRate / 100);
        return [
            'excl' => round($excl, 2),
            'tax'  => round($tax, 2),
            'incl' => round($excl + $tax, 2),
        ];
    }

    public static function newFactory()
    {
        return \Darvis\MantaProduct\Database\Factories\ProductFactory::new();
    }
}

<?php

namespace Manta\Products\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasDimensions
{
    public function lengthM(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->length_mm ? $this->length_mm / 1000 : null,
            set: fn ($value) => ['length_mm' => $value !== null ? (int) round($value * 1000) : null],
        );
    }

    public function widthM(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->width_mm ? $this->width_mm / 1000 : null,
            set: fn ($value) => ['width_mm' => $value !== null ? (int) round($value * 1000) : null],
        );
    }

    public function heightM(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->height_mm ? $this->height_mm / 1000 : null,
            set: fn ($value) => ['height_mm' => $value !== null ? (int) round($value * 1000) : null],
        );
    }

    public function areaM2(): ?float
    {
        if (!$this->length_mm || !$this->width_mm) return null;
        return ($this->length_mm / 1000) * ($this->width_mm / 1000);
    }

    public function volumeM3(): ?float
    {
        if (!$this->length_mm || !$this->width_mm || !$this->height_mm) return null;
        return ($this->length_mm / 1000) * ($this->width_mm / 1000) * ($this->height_mm / 1000);
    }
}

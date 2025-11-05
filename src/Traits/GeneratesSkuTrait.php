<?php

namespace Darvis\MantaProduct\Traits;

use Illuminate\Support\Str;
use Darvis\MantaProduct\Models\ProductVariant;

trait GeneratesSkuTrait
{
    /**
     * Auto-generate SKU when variant title changes
     */
    public function updatedVariantTitle()
    {
        if (!empty($this->variantTitle) && empty($this->variantSku)) {
            $this->variantSku = $this->generateSkuFromTitle($this->variantTitle);
        }
    }

    /**
     * Generate SKU from title
     */
    protected function generateSkuFromTitle(string $title, ?string $productTitle = null, ?string $productSlug = null): string
    {
        // Gebruik product info van huidige item of parameters
        if (isset($this->item) && $this->item) {
            $productSlug = $this->item->slug ?? Str::slug($this->item->title ?? 'PRODUCT');
        } elseif ($productTitle) {
            $productSlug = $productSlug ?? Str::slug($productTitle);
        } else {
            $productSlug = 'PRODUCT';
        }
        
        $variantSlug = Str::slug($title);
        
        // Combineer en maak uppercase
        $baseSku = strtoupper($productSlug . '-' . $variantSlug);
        
        // Vervang streepjes door underscores voor betere leesbaarheid
        $baseSku = str_replace('-', '_', $baseSku);
        
        // Limiteer lengte tot 50 karakters
        $baseSku = substr($baseSku, 0, 50);
        
        // Check of SKU al bestaat en voeg nummer toe indien nodig
        $finalSku = $baseSku;
        $counter = 1;
        
        while (ProductVariant::where('sku', $finalSku)->exists()) {
            $finalSku = $baseSku . '_' . $counter;
            $counter++;
            
            // Voorkom oneindige loop
            if ($counter > 999) {
                $finalSku = $baseSku . '_' . time();
                break;
            }
        }
        
        return $finalSku;
    }

    /**
     * Generate SKU for product (not variant)
     */
    protected function generateProductSku(string $title): string
    {
        $slug = Str::slug($title);
        $baseSku = strtoupper(str_replace('-', '_', $slug));
        
        // Limiteer lengte tot 30 karakters voor product SKU
        $baseSku = substr($baseSku, 0, 30);
        
        // Check of SKU al bestaat en voeg nummer toe indien nodig
        $finalSku = $baseSku;
        $counter = 1;
        
        while (\Darvis\MantaProduct\Models\Product::where('sku', $finalSku)->exists()) {
            $finalSku = $baseSku . '_' . $counter;
            $counter++;
            
            if ($counter > 999) {
                $finalSku = $baseSku . '_' . time();
                break;
            }
        }
        
        return $finalSku;
    }
}

<?php

namespace Darvis\MantaProduct\Services;

use Illuminate\Support\Str;
use Darvis\MantaProduct\Models\Attribute;
use Darvis\MantaProduct\Models\AttributeValue;
use Darvis\MantaProduct\Models\Product;
use Darvis\MantaProduct\Models\ProductVariant;
use Darvis\MantaProduct\Models\ProductVariantValue;

class VariantMatrixService
{
    /**
     * Generate variants by crossing attribute axes.
     * $axes = [
     *   'color' => ['red','blue'],
     *   'size'  => ['s','m','l'],
     * ];
     */
    public function generate(Product $product, array $axes): array
    {
        $combinations = $this->cartesian($axes);
        $created = [];
        foreach ($combinations as $combo) {
            $codes = implode('-', array_keys($combo));
            $values = implode('-', array_values($combo));

            $pattern = config('manta-products.sku_pattern', '{product_id}-{codes}-{values}');
            $sku = strtoupper(str_replace(
                ['{product_id}', '{codes}', '{values}'],
                [$product->id, $codes, $values],
                $pattern
            ));

            $variantKey = $this->variantKey($combo);

            $variant = ProductVariant::firstOrCreate(
                ['product_id' => $product->id, 'sku' => $sku],
                ['title' => implode(' / ', array_values($combo)), 'variant_key' => $variantKey, 'active' => true]
            );

            foreach ($combo as $attrCode => $valSlug) {
                $attribute = Attribute::where('code', $attrCode)->firstOrFail();
                $value = AttributeValue::where('attribute_id', $attribute->id)->where('code', $valSlug)->firstOrFail();
                ProductVariantValue::updateOrCreate(
                    ['product_variant_id' => $variant->id, 'attribute_id' => $attribute->id],
                    ['attribute_value_id' => $value->id]
                );
            }

            $created[] = $variant;
        }

        return $created;
    }

    protected function cartesian(array $axes): array
    {
        $result = [[]];
        foreach ($axes as $attrCode => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $value) {
                    $product[$attrCode] = $value;
                    $append[] = $product;
                }
            }
            $result = $append;
        }
        return $result;
    }

    public function variantKey(array $combo): string
    {
        ksort($combo);
        return collect($combo)->map(fn($v, $k) => $k . ':' . $v) . join('|');
    }
}

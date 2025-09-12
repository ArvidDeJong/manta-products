<?php

return [
    'default_tax_rate' => 21.00,
    'currency' => 'EUR',
    'sku_pattern' => '{product_id}-{codes}-{values}',
    'default_rounding_mode' => 'round',
    'default_unit_step' => 0.01,

    // Enable demo routes (Livewire screens, simple slot generator showcase)
    'enable_demo' => false,

    // Route prefix for demo
    'demo_prefix' => 'manta-products-demo',
];

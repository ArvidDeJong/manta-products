<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Manta Product Configuration
    |--------------------------------------------------------------------------
    |
    | Hier kan je de configuratie voor de Manta Product package aanpassen.
    |
    */

    // Route prefix voor de product module
    'route_prefix' => 'cms/producten',

    // Database instellingen
    'database' => [
        'table_name' => 'manta_products',
    ],
    'cat' => [
        'database' => [
            'table_name' => 'manta_productcats',
        ],
    ],
    'join' => [
        'database' => [
            'table_name' => 'manta_productcatjoins',
        ],
    ],

    // UI instellingen
    'ui' => [
        'items_per_page' => 25,
        'show_breadcrumbs' => true,
    ],


];

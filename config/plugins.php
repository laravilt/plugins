<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Discovery
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic plugin discovery. When enabled, Laravilt
    | will automatically discover and register plugins from installed packages.
    |
    */
    'discovery' => [
        'enabled' => env('LARAVILT_PLUGIN_DISCOVERY', true),
        'cache' => env('LARAVILT_PLUGIN_CACHE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Paths
    |--------------------------------------------------------------------------
    |
    | Define custom paths where Laravilt should look for plugins.
    |
    */
    'paths' => [
        base_path('packages'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for generated plugins.
    |
    */
    'defaults' => [
        'vendor' => 'laravilt',
        'author' => 'Laravilt Team',
        'email' => 'hello@laravilt.com',
        'license' => 'MIT',
    ],
];

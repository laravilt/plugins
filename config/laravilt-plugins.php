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
        'enabled' => env('LARAVILT_PLUGINS_DISCOVERY_ENABLED', true),
        'cache' => env('LARAVILT_PLUGINS_CACHE_ENABLED', true),
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
        base_path('vendor'),
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
        'vendor' => env('LARAVILT_PLUGINS_DEFAULT_VENDOR', 'laravilt'),
        'author' => env('LARAVILT_PLUGINS_DEFAULT_AUTHOR', 'Fady Mondy'),
        'email' => env('LARAVILT_PLUGINS_DEFAULT_EMAIL', 'info@3x1.io'),
        'license' => env('LARAVILT_PLUGINS_DEFAULT_LICENSE', 'MIT'),
    ],
];

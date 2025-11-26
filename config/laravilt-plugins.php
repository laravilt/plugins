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
        'github_sponsor' => env('LARAVILT_PLUGINS_DEFAULT_GITHUB_SPONSOR', 'fadymondy'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Features
    |--------------------------------------------------------------------------
    |
    | This array defines all available features for plugin generation.
    | Features are loaded in priority order (lower priority = earlier).
    |
    | You can add custom features by adding them to this array.
    |
    */

    'features' => [
        // Core Files
        \Laravilt\Plugins\Features\ComposerJsonFeature::class,
        \Laravilt\Plugins\Features\GitignoreFeature::class,
        \Laravilt\Plugins\Features\ServiceProviderFeature::class,
        \Laravilt\Plugins\Features\PluginClassFeature::class,
        \Laravilt\Plugins\Features\InstallCommandFeature::class,
        \Laravilt\Plugins\Features\ConfigFeature::class,

        // Structure Files
        \Laravilt\Plugins\Features\MigrationsFeature::class,
        \Laravilt\Plugins\Features\RoutesFeature::class,
        \Laravilt\Plugins\Features\ViewsFeature::class,
        \Laravilt\Plugins\Features\LanguageFeature::class,

        // Asset Files
        \Laravilt\Plugins\Features\CssFeature::class,
        \Laravilt\Plugins\Features\JsFeature::class,
        \Laravilt\Plugins\Features\ArtsFeature::class,

        // Testing Files
        \Laravilt\Plugins\Features\TestingFeature::class,
        \Laravilt\Plugins\Features\TestbenchFeature::class,
        \Laravilt\Plugins\Features\PintFeature::class,

        // Documentation Files
        \Laravilt\Plugins\Features\ReadmeFeature::class,
        \Laravilt\Plugins\Features\GitHubFeature::class,
        \Laravilt\Plugins\Features\DocumentationFeature::class,
    ],
];

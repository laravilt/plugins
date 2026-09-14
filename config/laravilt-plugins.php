<?php

use Laravilt\Plugins\Features\ArtsFeature;
use Laravilt\Plugins\Features\ComposerJsonFeature;
use Laravilt\Plugins\Features\ConfigFeature;
use Laravilt\Plugins\Features\CssFeature;
use Laravilt\Plugins\Features\DocumentationFeature;
use Laravilt\Plugins\Features\GitHubFeature;
use Laravilt\Plugins\Features\GitignoreFeature;
use Laravilt\Plugins\Features\InstallCommandFeature;
use Laravilt\Plugins\Features\JsFeature;
use Laravilt\Plugins\Features\LanguageFeature;
use Laravilt\Plugins\Features\MigrationsFeature;
use Laravilt\Plugins\Features\PintFeature;
use Laravilt\Plugins\Features\PluginClassFeature;
use Laravilt\Plugins\Features\ReadmeFeature;
use Laravilt\Plugins\Features\RoutesFeature;
use Laravilt\Plugins\Features\ServiceProviderFeature;
use Laravilt\Plugins\Features\TestbenchFeature;
use Laravilt\Plugins\Features\TestingFeature;
use Laravilt\Plugins\Features\ViewsFeature;

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
        ComposerJsonFeature::class,
        GitignoreFeature::class,
        ServiceProviderFeature::class,
        PluginClassFeature::class,
        InstallCommandFeature::class,
        ConfigFeature::class,

        // Structure Files
        MigrationsFeature::class,
        RoutesFeature::class,
        ViewsFeature::class,
        LanguageFeature::class,

        // Asset Files
        CssFeature::class,
        JsFeature::class,
        ArtsFeature::class,

        // Testing Files
        TestingFeature::class,
        TestbenchFeature::class,
        PintFeature::class,

        // Documentation Files
        ReadmeFeature::class,
        GitHubFeature::class,
        DocumentationFeature::class,
    ],
];

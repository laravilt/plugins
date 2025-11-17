<?php

namespace Laravilt\Plugins;

use Illuminate\Support\ServiceProvider;
use Laravilt\Plugins\Contracts\PluginManager as PluginManagerContract;
use Laravilt\Plugins\Support\PluginManager;

class PluginsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravilt-plugins.php',
            'laravilt-plugins'
        );

        $this->app->singleton(PluginManagerContract::class, function ($app) {
            return new PluginManager($app);
        });

        $this->app->alias(PluginManagerContract::class, 'laravilt.plugins');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakePluginCommand::class,
                Commands\MakePluginModelCommand::class,
                Commands\MakePluginMigrationCommand::class,
                Commands\MakePluginResourceCommand::class,
                Commands\MakePluginWidgetCommand::class,
                Commands\MakePluginPageCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/laravilt-plugins.php' => config_path('laravilt-plugins.php'),
            ], 'laravilt-plugins-config');
        }

        // Auto-discover and register plugins
        $this->discoverPlugins();
    }

    /**
     * Discover and register plugins.
     */
    protected function discoverPlugins(): void
    {
        $manager = $this->app->make(PluginManagerContract::class);

        $manager->discover();
        $manager->bootAll();
    }
}

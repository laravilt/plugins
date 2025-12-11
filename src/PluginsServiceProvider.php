<?php

namespace Laravilt\Plugins;

use Illuminate\Support\ServiceProvider;
use Laravilt\Plugins\Contracts\PluginManager as PluginManagerContract;
use Laravilt\Plugins\Services\Generation\StubProcessor;
use Laravilt\Plugins\Services\PluginFeatureFactory;
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

        // Register PluginFeatureFactory with features from config
        $this->app->singleton(PluginFeatureFactory::class, function ($app) {
            $factory = new PluginFeatureFactory;
            $stubProcessor = $app->make(StubProcessor::class);

            // Load features from config
            $featureClasses = config('laravilt-plugins.features', []);

            foreach ($featureClasses as $featureClass) {
                $feature = new $featureClass($stubProcessor);
                $factory->register($feature);
            }

            return $factory;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $commands = [
                Commands\MakePluginCommand::class,
                Commands\MakeComponentCommand::class,
            ];

            // Only register MCP command if Laravel MCP is installed
            if (class_exists(\Laravel\Mcp\Server::class)) {
                $commands[] = Commands\InstallMcpServerCommand::class;
            }

            $this->commands($commands);

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

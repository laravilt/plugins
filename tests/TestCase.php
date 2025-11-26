<?php

namespace Laravilt\Plugins\Tests;

use Illuminate\Support\Facades\File;
use Laravilt\Plugins\PluginsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create packages directory
        $packagesPath = $this->app->basePath('packages/laravilt');
        if (! File::isDirectory($packagesPath)) {
            File::makeDirectory($packagesPath, 0755, true);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            PluginsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');

        // Load config
        $pluginConfig = require __DIR__.'/../config/laravilt-plugins.php';
        $app['config']->set('laravilt-plugins', $pluginConfig);
        $app['config']->set('laravilt-plugins.defaults.author', $pluginConfig['defaults']['author'] ?? 'Fady Mondy');
        $app['config']->set('laravilt-plugins.defaults.email', $pluginConfig['defaults']['email'] ?? 'info@3x1.io');
        $app['config']->set('laravilt-plugins.defaults.license', $pluginConfig['defaults']['license'] ?? 'MIT');
    }

    /**
     * Resolve application base path implementation.
     */
    protected function resolveApplicationBasePath($app)
    {
        // Use the actual Laravel application base path (4 directories up from tests/)
        return realpath(__DIR__.'/../../../../') ?: $app->basePath();
    }

    protected function defineDatabaseMigrations(): void
    {
        //
    }

    protected function getTestPluginPath(): string
    {
        return $this->app->basePath('packages/laravilt/test-plugin');
    }

    protected function cleanupTestPlugin(): void
    {
        $path = $this->getTestPluginPath();
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
    }
}

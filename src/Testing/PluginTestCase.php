<?php

namespace Laravilt\Plugins\Testing;

use Laravilt\Plugins\PluginsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class PluginTestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->setUpPluginAssets();
    }

    protected function getPackageProviders($app): array
    {
        return [
            PluginsServiceProvider::class,
            $this->getPluginServiceProvider(),
        ];
    }

    /**
     * Get the plugin service provider class.
     */
    abstract protected function getPluginServiceProvider(): string;

    /**
     * Setup the test database.
     */
    protected function setUpDatabase(): void
    {
        $migrationsPath = $this->getPluginMigrationsPath();

        if ($migrationsPath && is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    /**
     * Get the plugin migrations path.
     */
    protected function getPluginMigrationsPath(): ?string
    {
        return null;
    }

    /**
     * Setup plugin assets for testing.
     */
    protected function setUpPluginAssets(): void
    {
        // Publish plugin assets if needed
    }

    /**
     * Assert that a plugin is registered.
     */
    protected function assertPluginRegistered(string $pluginId): void
    {
        $manager = app('laravilt.plugins');

        $this->assertTrue(
            $manager->has($pluginId),
            "Plugin '{$pluginId}' is not registered."
        );
    }

    /**
     * Assert that a plugin is enabled.
     */
    protected function assertPluginEnabled(string $pluginId): void
    {
        $manager = app('laravilt.plugins');
        $plugin = $manager->get($pluginId);

        $this->assertTrue(
            $plugin->isEnabled(),
            "Plugin '{$pluginId}' is not enabled."
        );
    }
}

<?php

namespace Laravilt\Plugins\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Laravilt\Plugins\Contracts\Plugin;

class PluginDiscovery
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Discover all plugins.
     *
     * @return Collection<Plugin>
     */
    public function discover(): Collection
    {
        $plugins = collect();

        // Check installed packages for Laravilt plugins
        $installedPackages = $this->getInstalledPackages();

        foreach ($installedPackages as $package) {
            if ($this->isLaraviltPlugin($package)) {
                $plugin = $this->loadPlugin($package);

                if ($plugin) {
                    $plugins->push($plugin);
                }
            }
        }

        return $plugins;
    }

    /**
     * Get installed packages from composer.lock.
     */
    protected function getInstalledPackages(): Collection
    {
        $composerLockPath = base_path('composer.lock');

        if (! file_exists($composerLockPath)) {
            return collect();
        }

        $composerLock = json_decode(file_get_contents($composerLockPath), true);

        return collect($composerLock['packages'] ?? []);
    }

    /**
     * Check if a package is a Laravilt plugin.
     */
    protected function isLaraviltPlugin(array $package): bool
    {
        $extra = $package['extra'] ?? [];
        $laravel = $extra['laravel'] ?? [];

        if (isset($laravel['providers'])) {
            foreach ($laravel['providers'] as $provider) {
                if ($this->isPluginProvider($provider)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a provider is a plugin provider.
     */
    protected function isPluginProvider(string $provider): bool
    {
        if (! class_exists($provider)) {
            return false;
        }

        $interfaces = class_implements($provider);

        return is_array($interfaces) && in_array(Plugin::class, $interfaces);
    }

    /**
     * Load a plugin from a package.
     */
    protected function loadPlugin(array $package): ?Plugin
    {
        $extra = $package['extra'] ?? [];
        $laravel = $extra['laravel'] ?? [];
        $providers = $laravel['providers'] ?? [];

        foreach ($providers as $provider) {
            if ($this->isPluginProvider($provider)) {
                return $this->app->make($provider);
            }
        }

        return null;
    }
}

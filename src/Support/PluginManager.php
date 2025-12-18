<?php

namespace Laravilt\Plugins\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Laravilt\Plugins\Contracts\Plugin;
use Laravilt\Plugins\Contracts\PluginManager as PluginManagerContract;

class PluginManager implements PluginManagerContract
{
    protected Application $app;

    /**
     * Registered plugins.
     *
     * @var Collection<string, Plugin>
     */
    protected Collection $plugins;

    /**
     * Booted plugins.
     *
     * @var Collection<string>
     */
    protected Collection $booted;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->plugins = collect();
        $this->booted = collect();
    }

    /**
     * Register a plugin.
     */
    public function register(Plugin $plugin): void
    {
        $id = $plugin->getId();

        if ($this->plugins->has($id)) {
            throw new \RuntimeException("Plugin '{$id}' is already registered.");
        }

        // Check dependencies
        if (! $plugin->dependenciesSatisfied()) {
            $dependencies = implode(', ', $plugin->getDependencies());
            throw new \RuntimeException(
                "Plugin '{$id}' dependencies not satisfied: {$dependencies}"
            );
        }

        $this->plugins->put($id, $plugin);
    }

    /**
     * Boot a plugin.
     */
    public function boot(string $id): void
    {
        if ($this->booted->contains($id)) {
            return;
        }

        $plugin = $this->get($id);

        if (! $plugin->isEnabled()) {
            return;
        }

        // Boot dependencies first
        foreach ($plugin->getDependencies() as $dependency) {
            $this->boot($dependency);
        }

        $this->booted->push($id);
    }

    /**
     * Boot all plugins.
     */
    public function bootAll(): void
    {
        foreach ($this->plugins->keys() as $id) {
            $this->boot($id);
        }
    }

    /**
     * Get a plugin by ID.
     */
    public function get(string $id): Plugin
    {
        if (! $this->has($id)) {
            throw new \RuntimeException("Plugin '{$id}' is not registered.");
        }

        return $this->plugins->get($id);
    }

    /**
     * Check if a plugin is registered.
     */
    public function has(string $id): bool
    {
        return $this->plugins->has($id);
    }

    /**
     * Get all plugins.
     *
     * @return Collection<string, Plugin>
     */
    public function all(): Collection
    {
        return $this->plugins;
    }

    /**
     * Get enabled plugins.
     *
     * @return Collection<string, Plugin>
     */
    public function enabled(): Collection
    {
        return $this->plugins->filter(fn (Plugin $plugin) => $plugin->isEnabled());
    }

    /**
     * Discover plugins.
     */
    public function discover(): void
    {
        $discovery = new PluginDiscovery($this->app);
        $plugins = $discovery->discover();

        foreach ($plugins as $plugin) {
            $this->register($plugin);
        }
    }

    /**
     * Get plugin manifest.
     */
    public function getManifest(): PluginManifest
    {
        return new PluginManifest($this->plugins);
    }

    /**
     * Register all enabled plugins with a panel.
     */
    public function registerWithPanel(\Laravilt\Panel\Panel $panel): void
    {
        foreach ($this->enabled() as $plugin) {
            $plugin->panelRegister($panel);
            $plugin->panelBoot($panel);
        }
    }
}

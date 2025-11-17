<?php

namespace Laravilt\Plugins\Contracts;

use Illuminate\Support\Collection;

interface PluginManager
{
    /**
     * Register a plugin.
     */
    public function register(Plugin $plugin): void;

    /**
     * Boot a plugin by ID.
     */
    public function boot(string $id): void;

    /**
     * Boot all plugins.
     */
    public function bootAll(): void;

    /**
     * Get a plugin by ID.
     */
    public function get(string $id): Plugin;

    /**
     * Check if a plugin is registered.
     */
    public function has(string $id): bool;

    /**
     * Get all plugins.
     *
     * @return Collection<string, Plugin>
     */
    public function all(): Collection;

    /**
     * Get enabled plugins.
     *
     * @return Collection<string, Plugin>
     */
    public function enabled(): Collection;

    /**
     * Discover plugins.
     */
    public function discover(): void;
}

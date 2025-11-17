<?php

namespace Laravilt\Plugins\Facades;

use Illuminate\Support\Facades\Facade;
use Laravilt\Plugins\Contracts\PluginManager;

/**
 * LaraviltPlugins Facade for Plugin Management
 *
 * Usage:
 * - LaraviltPlugins::plugin() - Get plugin manager
 * - LaraviltPlugins::plugin('plugin-id') - Get specific plugin
 *
 * @method static void register(\Laravilt\Plugins\Contracts\Plugin $plugin)
 * @method static void boot(string $id)
 * @method static void bootAll()
 * @method static \Laravilt\Plugins\Contracts\Plugin get(string $id)
 * @method static bool has(string $id)
 * @method static \Illuminate\Support\Collection all()
 * @method static \Illuminate\Support\Collection enabled()
 * @method static void discover()
 * @method static \Laravilt\Plugins\Support\PluginManifest getManifest()
 *
 * @see \Laravilt\Plugins\Support\PluginManager
 */
class LaraviltPlugins extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PluginManager::class;
    }

    /**
     * Get plugin manager instance or specific plugin.
     *
     * @param  string|null  $id  Plugin ID
     */
    public static function plugin(?string $id = null): mixed
    {
        $manager = static::getFacadeRoot();

        if ($id === null) {
            return $manager;
        }

        return $manager->get($id);
    }
}

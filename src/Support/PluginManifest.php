<?php

namespace Laravilt\Plugins\Support;

use Illuminate\Support\Collection;
use Laravilt\Plugins\Contracts\Plugin;

class PluginManifest
{
    /**
     * @var Collection<string, Plugin>
     */
    protected Collection $plugins;

    /**
     * @param  Collection<string, Plugin>  $plugins
     */
    public function __construct(Collection $plugins)
    {
        $this->plugins = $plugins;
    }

    /**
     * Get manifest as array.
     */
    public function toArray(): array
    {
        return $this->plugins->map(function (Plugin $plugin) {
            return [
                'id' => $plugin->getId(),
                'name' => $plugin->getName(),
                'version' => $plugin->getVersion(),
                'description' => $plugin->getDescription(),
                'author' => $plugin->getAuthor(),
                'enabled' => $plugin->isEnabled(),
                'dependencies' => $plugin->getDependencies(),
            ];
        })->all();
    }

    /**
     * Get manifest as JSON.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}

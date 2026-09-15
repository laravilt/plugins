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
            // Only getId()/isEnabled() are on the Plugin contract; the rest come from PluginProvider
            $call = fn (string $method, mixed $default) => method_exists($plugin, $method) ? $plugin->{$method}() : $default;

            return [
                'id' => $plugin->getId(),
                'name' => $call('getName', ''),
                'version' => $call('getVersion', ''),
                'description' => $call('getDescription', ''),
                'author' => $call('getAuthor', ''),
                'enabled' => $plugin->isEnabled(),
                'dependencies' => $call('getDependencies', []),
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

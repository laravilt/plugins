<?php

namespace Laravilt\Plugins\Concerns;

trait HasCommands
{
    protected array $pluginCommands = [];

    public function pluginCommands(array $commands): static
    {
        $this->pluginCommands = $commands;

        return $this;
    }

    public function getPluginCommands(): array
    {
        return $this->pluginCommands;
    }

    public function registerPluginCommands(): void
    {
        if (empty($this->pluginCommands)) {
            return;
        }

        $this->commands($this->pluginCommands);
    }
}

<?php

namespace Laravilt\Plugins\Contracts;

use Laravilt\Panel\Panel;

interface Plugin
{
    /**
     * Get the plugin ID.
     */
    public function getId(): string;

    /**
     * Get the plugin name.
     */
    public function getName(): string;

    /**
     * Get the plugin version.
     */
    public function getVersion(): string;

    /**
     * Get the plugin description.
     */
    public function getDescription(): string;

    /**
     * Get the plugin author.
     */
    public function getAuthor(): string;

    /**
     * Register the plugin with a panel.
     */
    public function panelRegister(Panel $panel): void;

    /**
     * Boot the plugin for a panel.
     */
    public function panelBoot(Panel $panel): void;

    /**
     * Check if the plugin is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Enable the plugin.
     */
    public function enable(): static;

    /**
     * Disable the plugin.
     */
    public function disable(): static;

    /**
     * Get plugin dependencies.
     *
     * @return array<string>
     */
    public function getDependencies(): array;

    /**
     * Check if dependencies are satisfied.
     */
    public function dependenciesSatisfied(): bool;
}

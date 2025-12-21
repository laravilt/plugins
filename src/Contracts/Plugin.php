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
     * Register the plugin with a panel.
     */
    public function register(Panel $panel): void;

    /**
     * Boot the plugin for a panel.
     */
    public function boot(Panel $panel): void;

    /**
     * Check if the plugin is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Create a new instance of the plugin.
     */
    public static function make(): static;
}

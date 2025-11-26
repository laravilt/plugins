<?php

namespace Laravilt\Plugins\Contracts;

/**
 * Interface for plugin features that can be generated.
 *
 * Each feature (migrations, routes, assets, etc.) implements this interface
 * to determine if it should be generated and how to generate it.
 */
interface PluginFeatureInterface
{
    /**
     * Get the feature name/identifier.
     */
    public function getName(): string;

    /**
     * Determine if this feature should be generated based on config.
     */
    public function shouldGenerate(array $config): bool;

    /**
     * Generate the feature files.
     */
    public function generate(array $config): void;

    /**
     * Get the directories this feature needs.
     */
    public function getDirectories(array $config): array;

    /**
     * Get the priority/order for generation (lower = earlier).
     */
    public function getPriority(): int;
}

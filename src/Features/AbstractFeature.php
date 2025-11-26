<?php

namespace Laravilt\Plugins\Features;

use Laravilt\Plugins\Contracts\PluginFeatureInterface;
use Laravilt\Plugins\Services\Generation\StubProcessor;

/**
 * Abstract base class for plugin features.
 *
 * Provides common functionality for all features.
 */
abstract class AbstractFeature implements PluginFeatureInterface
{
    public function __construct(protected StubProcessor $processor) {}

    /**
     * Default priority is 50 (middle priority).
     * Override in subclasses for different priorities:
     * - 0-20: Core files (composer, service provider, etc.)
     * - 21-40: Structure files (migrations, routes, etc.)
     * - 41-60: Asset files (CSS, JS, etc.)
     * - 61-80: Testing files
     * - 81-100: Documentation files
     */
    public function getPriority(): int
    {
        return 50;
    }

    /**
     * Default: no directories needed.
     * Override in subclasses to specify directories.
     */
    public function getDirectories(array $config): array
    {
        return [];
    }
}

<?php

namespace Laravilt\Plugins\Services;

use Laravilt\Plugins\Contracts\PluginFeatureInterface;

/**
 * Factory for managing and executing plugin features.
 *
 * This class follows the Factory Pattern to allow dynamic registration
 * and execution of plugin features without modifying core code.
 */
class PluginFeatureFactory
{
    /**
     * @var array<PluginFeatureInterface>
     */
    protected array $features = [];

    /**
     * Register a feature for generation.
     */
    public function register(PluginFeatureInterface $feature): void
    {
        $this->features[$feature->getName()] = $feature;
    }

    /**
     * Register multiple features at once.
     *
     * @param  array<PluginFeatureInterface>  $features
     */
    public function registerMany(array $features): void
    {
        foreach ($features as $feature) {
            $this->register($feature);
        }
    }

    /**
     * Get all registered features sorted by priority.
     *
     * @return array<PluginFeatureInterface>
     */
    public function getFeatures(): array
    {
        $features = $this->features;

        usort($features, fn ($a, $b) => $a->getPriority() <=> $b->getPriority());

        return $features;
    }

    /**
     * Get directories needed by all features that should be generated.
     */
    public function getDirectories(array $config): array
    {
        $directories = [];

        foreach ($this->getFeatures() as $feature) {
            if ($feature->shouldGenerate($config)) {
                $directories = array_merge($directories, $feature->getDirectories($config));
            }
        }

        return array_unique($directories);
    }

    /**
     * Generate all features that should be generated.
     */
    public function generateAll(array $config): void
    {
        foreach ($this->getFeatures() as $feature) {
            if ($feature->shouldGenerate($config)) {
                $feature->generate($config);
            }
        }
    }

    /**
     * Generate a specific feature by name.
     */
    public function generate(string $featureName, array $config): void
    {
        if (! isset($this->features[$featureName])) {
            throw new \InvalidArgumentException("Feature '{$featureName}' not registered");
        }

        $feature = $this->features[$featureName];

        if ($feature->shouldGenerate($config)) {
            $feature->generate($config);
        }
    }
}

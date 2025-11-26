<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates views directory for the plugin.
 *
 * Creates resources/views structure when views are enabled.
 */
class ViewsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'views';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_views'] ?? false;
    }

    public function getPriority(): int
    {
        return 35; // Structure file
    }

    public function getDirectories(array $config): array
    {
        return $this->shouldGenerate($config)
            ? ['resources/views']
            : [];
    }

    public function generate(array $config): void
    {
        // Create a .gitkeep file to preserve the directory
        $this->processor->files->put(
            $config['base_path'].'/resources/views/.gitkeep',
            ''
        );
    }
}

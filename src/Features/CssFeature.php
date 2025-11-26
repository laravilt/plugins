<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates CSS assets for the plugin.
 *
 * Creates CSS files with Tailwind v4 support.
 */
class CssFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'css';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_css'] ?? false;
    }

    public function getPriority(): int
    {
        return 50; // Asset files
    }

    public function getDirectories(array $config): array
    {
        return $this->shouldGenerate($config)
            ? ['resources/css', 'dist']
            : [];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/resources/css/app.css',
            'css/app',
            [
                'plugin_name' => $config['studly_name'],
            ]
        );
    }
}

<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates composer.json for the plugin.
 *
 * Creates package configuration following Laravel package standards.
 */
class ComposerJsonFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'composer-json';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate composer.json
    }

    public function getPriority(): int
    {
        return 1; // Very early - needed by other features
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/composer.json',
            'composer.json',
            [
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Laravilt",
                'author' => $config['author'],
                'email' => $config['author_email'] ?? $config['email'],
                'license' => $config['license'],
                'namespace' => str_replace('\\', '\\\\', $config['namespace']),
                'class' => $config['studly_name'].'Plugin',
                'service_provider' => $config['studly_name'].'ServiceProvider',
            ]
        );
    }
}

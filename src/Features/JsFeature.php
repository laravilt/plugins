<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates JavaScript assets for the plugin.
 *
 * Creates Vue.js plugin structure and Vite configuration.
 */
class JsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'js';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_js'] ?? false;
    }

    public function getPriority(): int
    {
        return 51; // Asset files - after CSS
    }

    public function getDirectories(array $config): array
    {
        return $this->shouldGenerate($config)
            ? ['resources/js', 'dist']
            : [];
    }

    public function generate(array $config): void
    {
        // Generate package.json
        $this->generatePackageJson($config);

        // Generate Vite plugin configuration
        $this->generateViteConfig($config);

        // Generate JS file as Vue.js plugin
        $this->generateJsFile($config);
    }

    protected function generatePackageJson(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/package.json',
            'package.json',
            [
                'package_name' => $config['kebab_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Laravilt",
            ]
        );
    }

    protected function generateViteConfig(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/vite.plugin.js',
            'vite.plugin',
            [
                'plugin_name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }

    protected function generateJsFile(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/resources/js/app.js',
            'js/app',
            [
                'plugin_name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }
}

<?php

namespace Laravilt\Plugins\Features;

use Laravilt\Support\Frontend;

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
            ? [$this->usesReact($config) ? 'resources/react' : 'resources/js', 'dist']
            : [];
    }

    public function generate(array $config): void
    {
        // Generate package.json
        $this->generatePackageJson($config);

        // Generate Vite plugin configuration
        $this->generateViteConfig($config);

        // Generate the plugin entry (Vue.js plugin or React registration module)
        $this->generateJsFile($config);
    }

    /**
     * Whether the plugin targets React (the `frontend` option, else the application's stack).
     */
    protected function usesReact(array $config): bool
    {
        $stack = $config['frontend'] ?? (class_exists(Frontend::class)
            ? Frontend::stack()
            : 'vue');

        return $stack === 'react';
    }

    protected function generatePackageJson(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/package.json',
            $this->usesReact($config) ? 'package.react.json' : 'package.json',
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
            $this->usesReact($config) ? 'vite.plugin.react' : 'vite.plugin',
            [
                'plugin_name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }

    protected function generateJsFile(array $config): void
    {
        $react = $this->usesReact($config);

        $this->processor->generateFile(
            $config['base_path'].($react ? '/resources/react/app.ts' : '/resources/js/app.js'),
            $react ? 'js/app.react' : 'js/app',
            [
                'plugin_name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }
}

<?php

namespace Laravilt\Plugins\Services\Generation;

/**
 * Generates frontend asset files and build configuration.
 *
 * Creates Vite, PostCSS, Tailwind, and npm configuration for
 * plugin asset compilation and management.
 */
class AssetFilesGenerator
{
    public function __construct(protected StubProcessor $processor) {}

    /**
     * Generate package.json for npm dependencies.
     */
    public function generatePackageJson(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/package.json',
            'package.json',
            [
                'package' => $config['kebab_name'],
            ]
        );
    }

    /**
     * Generate vite.config.js for Vite build tool.
     *
     * Configures Vite with Laravel plugin for asset compilation.
     */
    public function generateViteConfig(array $config): void
    {
        $content = <<<'JS'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            buildDirectory: 'dist',
        }),
    ],
});
JS;
        $this->processor->files->put($config['base_path'].'/vite.config.js', $content);
    }

    /**
     * Generate postcss.config.js for PostCSS processing.
     *
     * Configures PostCSS with Tailwind CSS and Autoprefixer plugins.
     */
    public function generatePostCssConfig(string $basePath): void
    {
        $content = "export default {\n    plugins: {\n        tailwindcss: {},\n        autoprefixer: {},\n    },\n};\n";
        $this->processor->files->put($basePath.'/postcss.config.js', $content);
    }

    /**
     * Generate tailwind.config.js for Tailwind CSS.
     *
     * Configures Tailwind CSS content paths for plugin templates.
     */
    public function generateTailwindConfig(string $basePath): void
    {
        $content = "export default {\n    content: ['./resources/**/*.blade.php', './resources/**/*.js'],\n    theme: { extend: {} },\n    plugins: [],\n};\n";
        $this->processor->files->put($basePath.'/tailwind.config.js', $content);
    }

    /**
     * Generate sample CSS file.
     */
    public function generateCssFile(string $basePath): void
    {
        $content = "/* Plugin styles */\n";
        $this->processor->files->put($basePath.'/resources/css/app.css', $content);
    }

    /**
     * Generate sample JavaScript file.
     */
    public function generateJsFile(string $basePath, string $kebabName): void
    {
        $content = "// Plugin JavaScript\nconsole.log('Plugin loaded: {$kebabName}');\n";
        $this->processor->files->put($basePath.'/resources/js/app.js', $content);
    }
}

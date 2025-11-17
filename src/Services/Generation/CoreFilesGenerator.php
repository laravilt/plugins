<?php

namespace Laravilt\Plugins\Services\Generation;

/**
 * Generates core plugin files including plugin class, composer.json, and config.
 *
 * This generator creates the essential files needed for a Laravilt plugin
 * to function properly within the Laravel/Filament ecosystem.
 */
class CoreFilesGenerator
{
    public function __construct(public StubProcessor $processor) {}

    /**
     * Generate the main plugin class file.
     *
     * Creates the plugin service provider that extends PluginProvider and
     * implements the Plugin interface for Filament compatibility.
     */
    public function generatePluginClass(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/src/'.$config['studly_name'].'Plugin.php',
            'plugin',
            [
                'namespace' => $config['namespace'],
                'class' => $config['studly_name'].'Plugin',
                'id' => $config['kebab_name'],
                'name' => $config['studly_name'],
                'description' => "{$config['studly_name']} plugin for Laravilt",
                'author' => $config['author'],
                'config' => $config['config_name'],
                'assets_tag' => $config['assets_tag'],
            ]
        );
    }

    /**
     * Generate composer.json with dependencies and autoloading.
     *
     * Creates package configuration following Laravel package standards
     * without requiring filament/filament directly.
     */
    public function generateComposerJson(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/composer.json',
            'composer.json',
            [
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'description' => "{$config['studly_name']} plugin for Laravilt",
                'author' => $config['author'],
                'email' => $config['email'],
                'license' => $config['license'],
                'namespace' => str_replace('\\', '\\\\', $config['namespace']),
                'class' => $config['studly_name'].'Plugin',
            ]
        );
    }

    /**
     * Generate plugin configuration file.
     *
     * Creates a Laravel config file with environment variable support.
     */
    public function generateConfig(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/config/'.$config['config_name'].'.php',
            'config',
            [
                'env_prefix' => $config['env_prefix'],
            ]
        );
    }

    /**
     * Generate README.md with installation and usage instructions.
     */
    public function generateReadme(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/README.md',
            'README.md',
            [
                'name' => $config['studly_name'],
                'description' => "{$config['studly_name']} plugin for Laravilt",
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'namespace' => $config['namespace'],
                'class' => $config['studly_name'].'Plugin',
                'config' => $config['config_name'],
                'assets_tag' => $config['assets_tag'],
            ]
        );
    }

    /**
     * Generate .gitignore file with common exclusions.
     */
    public function generateGitignore(string $basePath): void
    {
        $content = "/node_modules\n/dist\n/vendor\ncomposer.lock\npackage-lock.json\n.env\n.DS_Store\n.idea\n.vscode\n";
        $this->processor->files->put($basePath.'/.gitignore', $content);
    }
}

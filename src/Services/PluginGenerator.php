<?php

namespace Laravilt\Plugins\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Services\Generation\AssetFilesGenerator;
use Laravilt\Plugins\Services\Generation\CoreFilesGenerator;
use Laravilt\Plugins\Services\Generation\DocumentationGenerator;
use Laravilt\Plugins\Services\Generation\GitHubFilesGenerator;
use Laravilt\Plugins\Services\Generation\TestingFilesGenerator;

/**
 * Orchestrates plugin generation by coordinating specialized generators.
 *
 * This service delegates file generation to focused generator classes,
 * each responsible for a specific aspect of the plugin structure.
 */
class PluginGenerator
{
    public function __construct(
        protected Filesystem $files,
        protected CoreFilesGenerator $coreGenerator,
        protected TestingFilesGenerator $testingGenerator,
        protected DocumentationGenerator $docGenerator,
        protected GitHubFilesGenerator $githubGenerator,
        protected AssetFilesGenerator $assetGenerator
    ) {}

    /**
     * Create the plugin directory structure.
     */
    public function createDirectoryStructure(string $basePath): void
    {
        $directories = [
            'src', 'src/Models', 'src/Resources', 'src/Pages', 'src/Widgets', 'src/Components',
            'config', 'database/migrations', 'database/factories', 'database/seeders',
            'resources/views', 'resources/lang/en', 'resources/lang/ar',
            'resources/css', 'resources/js', 'dist',
            'tests/Feature', 'tests/Unit', 'tests/database/factories', 'tests/database/seeders',
            '.github/workflows', '.github/ISSUE_TEMPLATE',
            'arts', 'docs', 'workbench',
        ];

        foreach ($directories as $dir) {
            $this->files->makeDirectory("{$basePath}/{$dir}", 0755, true, true);
        }

        // Add .gitkeep to empty directories
        foreach (['arts', 'workbench'] as $dir) {
            $this->files->put("{$basePath}/{$dir}/.gitkeep", '');
        }
    }

    /**
     * Generate core plugin files.
     */
    public function generateCoreFiles(array $config): void
    {
        $this->coreGenerator->generatePluginClass($config);
        $this->coreGenerator->generateComposerJson($config);
        $this->coreGenerator->generateConfig($config);
        $this->coreGenerator->generateReadme($config);
        $this->coreGenerator->generateGitignore($config['base_path']);
    }

    /**
     * Generate GitHub workflow files.
     */
    public function generateGitHubFiles(array $config): void
    {
        $this->githubGenerator->generateWorkflows($config);
        $this->githubGenerator->generateTemplates($config);
    }

    /**
     * Generate testing configuration files.
     */
    public function generateTestingFiles(array $config): void
    {
        $this->testingGenerator->generatePhpUnit($config['base_path']);
        $this->testingGenerator->generatePint($config['base_path']);
        $this->testingGenerator->generatePhpStan($config['base_path']);
        $this->testingGenerator->generateTestbench($config['base_path']);
        $this->testingGenerator->generatePestConfig($config);
    }

    /**
     * Generate documentation files.
     */
    public function generateDocumentationFiles(array $config): void
    {
        $this->docGenerator->generateChangelog($config['base_path']);
        $this->docGenerator->generateLicense($config);
        $this->docGenerator->generateSecurity($config);
        $this->docGenerator->generateCodeOfConduct($config['base_path']);
    }

    /**
     * Generate sample components.
     */
    public function generateSampleComponents(array $config): void
    {
        // Sample widget
        $this->files->ensureDirectoryExists($config['base_path'].'/src/Widgets');
        $widgetContent = $this->coreGenerator->processor->process('widget', [
            'namespace' => $config['namespace'],
            'class' => 'StatsWidget',
            'view' => $config['kebab_name'].'::widgets.stats',
        ]);
        $this->files->put($config['base_path'].'/src/Widgets/StatsWidget.php', $widgetContent);

        // Sample widget view
        $viewPath = $config['base_path'].'/resources/views/widgets';
        $this->files->ensureDirectoryExists($viewPath);
        $this->files->put($viewPath.'/stats.blade.php', "<div>\n    <!-- Stats Widget Content -->\n</div>\n");
    }

    /**
     * Generate asset files (Vite, PostCSS, etc).
     */
    public function generateAssetFiles(array $config): void
    {
        $this->assetGenerator->generatePackageJson($config);
        $this->assetGenerator->generateViteConfig($config);
        $this->assetGenerator->generatePostCssConfig($config['base_path']);
        $this->assetGenerator->generateTailwindConfig($config['base_path']);
        $this->assetGenerator->generateCssFile($config['base_path']);
        $this->assetGenerator->generateJsFile($config['base_path'], $config['kebab_name']);
    }

    /**
     * Prepare configuration array for generators.
     */
    public function prepareConfig(string $name, string $vendor, string $basePath): array
    {
        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);
        $vendorLower = Str::lower($vendor);
        $namespace = Str::studly($vendorLower).'\\'.$studlyName;

        return [
            'name' => $name,
            'studly_name' => $studlyName,
            'kebab_name' => $kebabName,
            'snake_name' => $snakeName,
            'vendor' => $vendor,
            'vendor_lower' => $vendorLower,
            'namespace' => $namespace,
            'base_path' => $basePath,
            'config_name' => 'laravilt-'.$kebabName,
            'env_prefix' => 'LARAVILT_'.strtoupper($snakeName),
            'assets_tag' => 'laravilt-'.$kebabName.'-assets',
            'author' => config('laravilt-plugins.defaults.author', 'Fady Mondy'),
            'email' => config('laravilt-plugins.defaults.email', 'info@3x1.io'),
            'license' => config('laravilt-plugins.defaults.license', 'MIT'),
        ];
    }
}

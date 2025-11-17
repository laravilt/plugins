<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Services\PluginGenerator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class MakePluginCommand extends Command
{
    protected $signature = 'laravilt:plugin
                            {name? : The name of the plugin}
                            {--vendor= : The vendor name}
                            {--path= : The base path where the plugin will be created}
                            {--no-components : Skip creating sample components}
                            {--no-assets : Skip asset scaffolding}';

    protected $description = 'Create a new Laravilt plugin package';

    public function __construct(
        protected Filesystem $files,
        protected PluginGenerator $generator
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        // Get plugin name
        $name = $this->getPluginName();
        if (! $name) {
            $this->error('Plugin name is required.');

            return self::FAILURE;
        }

        // Get vendor name
        $vendor = $this->getVendorName();

        // Prepare configuration
        $basePath = $this->option('path') ?: base_path('packages/'.Str::lower($vendor).'/'.Str::kebab($name));

        // Check if directory exists
        if ($this->files->exists($basePath)) {
            $this->error("Plugin directory already exists: {$basePath}");

            return self::FAILURE;
        }

        // Confirm creation
        if (! $this->confirmCreation($name, $basePath)) {
            $this->info('Plugin creation cancelled.');

            return self::SUCCESS;
        }

        $this->info("Creating plugin: {$name}...");

        // Generate the plugin using the service
        $config = $this->generator->prepareConfig($name, $vendor, $basePath);

        $this->generator->createDirectoryStructure($basePath);
        $this->generator->generateCoreFiles($config);
        $this->generator->generateGitHubFiles($config);
        $this->generator->generateTestingFiles($config);
        $this->generator->generateDocumentationFiles($config);

        if (! $this->option('no-components')) {
            $this->generator->generateSampleComponents($config);
        }

        if (! $this->option('no-assets')) {
            $this->generator->generateAssetFiles($config);
        }

        // Display success message
        $this->displaySuccessMessage($basePath);

        return self::SUCCESS;
    }

    protected function getPluginName(): ?string
    {
        if ($this->argument('name')) {
            return $this->argument('name');
        }

        if ($this->option('no-interaction')) {
            return null;
        }

        return text(
            label: 'Plugin name',
            placeholder: 'E.g., BlogExtensions',
            required: true
        );
    }

    protected function getVendorName(): string
    {
        if ($this->option('vendor')) {
            return $this->option('vendor');
        }

        if ($this->option('no-interaction')) {
            return config('laravilt-plugins.defaults.vendor', 'laravilt');
        }

        return text(
            label: 'Vendor name',
            default: config('laravilt-plugins.defaults.vendor', 'laravilt'),
            required: true
        );
    }

    protected function confirmCreation(string $name, string $basePath): bool
    {
        if ($this->option('no-interaction')) {
            return true;
        }

        return confirm(
            label: "Create plugin '".Str::studly($name)."' in {$basePath}?",
            default: true
        );
    }

    protected function displaySuccessMessage(string $basePath): void
    {
        $this->newLine();
        $this->info('✅ Plugin created successfully!');
        $this->newLine();

        $this->info('📦 Next steps:');
        $this->line("  1. cd {$basePath}");
        $this->line('  2. composer install');
        $this->line('  3. npm install && npm run build');
        $this->line('  4. Register the plugin in your panel provider');
        $this->newLine();

        $this->info('📖 Plugin location: '.$basePath);
    }
}

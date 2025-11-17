<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Commands\Concerns\ManagesStubs;

class MakePluginPageCommand extends Command
{
    use ManagesStubs;

    protected $signature = 'laravilt:plugin-page
                            {name : The name of the page}
                            {--plugin= : The plugin directory}';

    protected $description = 'Create a new page for a Laravilt plugin';

    protected Filesystem $files;

    public function handle(): int
    {
        $this->files = app(Filesystem::class);

        $name = $this->argument('name');
        $pluginPath = $this->option('plugin') ?: getcwd();

        if (! $this->files->exists($pluginPath.'/src')) {
            $this->error('Invalid plugin directory.');

            return self::FAILURE;
        }

        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $namespace = $this->getNamespace($pluginPath);
        $pluginId = $this->getPluginId($pluginPath);

        // Generate Page
        $this->generateFromStub('page', $pluginPath."/src/Pages/{$studlyName}.php", [
            'namespace' => $namespace,
            'class' => $studlyName,
            'view' => $pluginId.'::pages.'.$kebabName,
        ]);

        // Generate Page View
        $viewPath = $pluginPath."/resources/views/pages/{$kebabName}.blade.php";
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, "<x-filament-panels::page>\n    <!-- Page content here -->\n</x-filament-panels::page>\n");

        $this->info("Page {$studlyName} created successfully!");

        return self::SUCCESS;
    }

    protected function getNamespace(string $pluginPath): string
    {
        $composer = json_decode($this->files->get($pluginPath.'/composer.json'), true);
        $autoload = $composer['autoload']['psr-4'] ?? [];

        return rtrim(array_key_first($autoload), '\\');
    }

    protected function getPluginId(string $pluginPath): string
    {
        $composer = json_decode($this->files->get($pluginPath.'/composer.json'), true);
        $packageName = $composer['name'] ?? '';

        return Str::afterLast($packageName, '/');
    }
}

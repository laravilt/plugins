<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Commands\Concerns\ManagesStubs;

class MakePluginWidgetCommand extends Command
{
    use ManagesStubs;

    protected $signature = 'laravilt:plugin-widget
                            {name : The name of the widget}
                            {--plugin= : The plugin directory}';

    protected $description = 'Create a new widget for a Laravilt plugin';

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

        // Generate Widget
        $this->generateFromStub('widget', $pluginPath."/src/Widgets/{$studlyName}Widget.php", [
            'namespace' => $namespace,
            'class' => $studlyName.'Widget',
            'view' => $pluginId.'::widgets.'.$kebabName,
        ]);

        // Generate Widget View
        $viewPath = $pluginPath."/resources/views/widgets/{$kebabName}.blade.php";
        $this->files->ensureDirectoryExists(dirname($viewPath));
        $this->files->put($viewPath, "<div>\n    <!-- Widget content here -->\n</div>\n");

        $this->info("Widget {$studlyName}Widget created successfully!");

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

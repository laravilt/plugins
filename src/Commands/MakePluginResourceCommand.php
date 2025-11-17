<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Commands\Concerns\ManagesStubs;

class MakePluginResourceCommand extends Command
{
    use ManagesStubs;

    protected $signature = 'laravilt:plugin-resource
                            {name : The name of the resource}
                            {--plugin= : The plugin directory}
                            {--model= : The model name}';

    protected $description = 'Create a new Filament resource for a Laravilt plugin';

    protected Filesystem $files;

    public function handle(): int
    {
        $this->files = app(Filesystem::class);

        $name = $this->argument('name');
        $pluginPath = $this->option('plugin') ?: getcwd();
        $modelName = $this->option('model') ?: $name;

        if (! $this->files->exists($pluginPath.'/src')) {
            $this->error('Invalid plugin directory. Please run this command from a plugin directory or specify --plugin option.');

            return self::FAILURE;
        }

        $studlyName = Str::studly($name);
        $namespace = $this->getNamespace($pluginPath);

        // Generate Resource
        $this->generateFromStub('resource', $pluginPath."/src/Resources/{$studlyName}Resource.php", [
            'namespace' => $namespace,
            'class' => $studlyName,
            'model' => Str::studly($modelName),
            'model_namespace' => $namespace.'\\Models',
        ]);

        // Generate Resource Pages
        $this->generateResourcePages($pluginPath, $studlyName, $namespace);

        $this->info("Resource {$studlyName}Resource created successfully!");

        return self::SUCCESS;
    }

    protected function getNamespace(string $pluginPath): string
    {
        $composer = json_decode($this->files->get($pluginPath.'/composer.json'), true);
        $autoload = $composer['autoload']['psr-4'] ?? [];

        return rtrim(array_key_first($autoload), '\\');
    }

    protected function generateResourcePages(string $pluginPath, string $studlyName, string $namespace): void
    {
        $pages = ['List', 'Create', 'Edit'];
        $pagesDir = $pluginPath."/src/Resources/{$studlyName}Resource/Pages";

        $this->files->ensureDirectoryExists($pagesDir);

        foreach ($pages as $page) {
            $pageClass = $page.$studlyName;
            $action = strtolower($page);

            $content = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$studlyName}Resource\\Pages;

use {$namespace}\\Resources\\{$studlyName}Resource;
use Filament\\Resources\\Pages\\{$page}Record;

class {$pageClass} extends {$page}Record
{
    protected static string \$resource = {$studlyName}Resource::class;
}
PHP;

            $this->files->put($pagesDir."/{$pageClass}.php", $content);
        }
    }
}

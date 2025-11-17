<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Commands\Concerns\ManagesStubs;

class MakePluginModelCommand extends Command
{
    use ManagesStubs;

    protected $signature = 'laravilt:plugin-model
                            {name : The name of the model}
                            {--plugin= : The plugin directory}
                            {--migration : Create a migration file}';

    protected $description = 'Create a new model for a Laravilt plugin';

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
        $namespace = $this->getNamespace($pluginPath);

        // Generate Model
        $this->generateFromStub('model', $pluginPath."/src/Models/{$studlyName}.php", [
            'namespace' => $namespace.'\\Models',
            'class' => $studlyName,
        ]);

        $this->info("Model {$studlyName} created successfully!");

        // Generate migration if requested
        if ($this->option('migration')) {
            $this->call('laravilt:plugin-migration', [
                'name' => 'create_'.Str::snake(Str::pluralStudly($name)).'_table',
                '--plugin' => $pluginPath,
                '--table' => Str::snake(Str::pluralStudly($name)),
            ]);
        }

        return self::SUCCESS;
    }

    protected function getNamespace(string $pluginPath): string
    {
        $composer = json_decode($this->files->get($pluginPath.'/composer.json'), true);
        $autoload = $composer['autoload']['psr-4'] ?? [];

        return rtrim(array_key_first($autoload), '\\');
    }
}

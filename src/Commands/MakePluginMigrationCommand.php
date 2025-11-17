<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Laravilt\Plugins\Commands\Concerns\ManagesStubs;

class MakePluginMigrationCommand extends Command
{
    use ManagesStubs;

    protected $signature = 'laravilt:plugin-migration
                            {name : The name of the migration}
                            {--plugin= : The plugin directory}
                            {--table= : The table name}';

    protected $description = 'Create a new migration for a Laravilt plugin';

    protected Filesystem $files;

    public function handle(): int
    {
        $this->files = app(Filesystem::class);

        $name = $this->argument('name');
        $pluginPath = $this->option('plugin') ?: getcwd();
        $tableName = $this->option('table') ?: Str::snake(Str::plural($name));

        if (! $this->files->exists($pluginPath.'/database/migrations')) {
            $this->files->makeDirectory($pluginPath.'/database/migrations', 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $migrationName = $timestamp.'_'.$name.'.php';

        $this->generateFromStub('migration', $pluginPath."/database/migrations/{$migrationName}", [
            'table' => $tableName,
        ]);

        $this->info("Migration {$migrationName} created successfully!");

        return self::SUCCESS;
    }
}

<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates install command for the plugin.
 *
 * Creates an Artisan command to install the plugin with conditional features.
 */
class InstallCommandFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'install-command';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate install command
    }

    public function getPriority(): int
    {
        return 12; // Core file - after composer.json
    }

    public function getDirectories(array $config): array
    {
        return ['src/Commands'];
    }

    public function generate(array $config): void
    {
        // Check if any assets are enabled
        $hasAssets = ($config['generate_css'] ?? false) || ($config['generate_js'] ?? false);

        // Build conditional signature options
        $signatureOptions = [];
        if ($hasAssets) {
            $signatureOptions[] = '{--without-assets : Skip asset publishing}';
        }
        if ($config['generate_migrations'] ?? false) {
            $signatureOptions[] = '{--without-migrations : Skip running migrations}';
            $signatureOptions[] = '{--without-seeders : Skip running seeders}';
        }

        // Build conditional method calls
        $calls = [];
        if ($hasAssets) {
            $calls[] = '$this->publishAssets();';
        }
        if ($config['generate_migrations'] ?? false) {
            $calls[] = "\n        if (! \$this->option('without-migrations')) {\n            \$this->runMigrations();\n        }";
            $calls[] = "\n        if (! \$this->option('without-seeders')) {\n            \$this->runSeeders();\n        }";
        }
        if ($hasAssets) {
            $calls[] = "\n        if (! \$this->option('without-assets')) {\n            \$this->buildAssets();\n        }";
        }

        // Build conditional methods
        $methods = [];

        if ($hasAssets) {
            $methods[] = "protected function publishAssets(): void\n    {\n        \$this->info('Publishing assets...');\n\n        \$this->call('vendor:publish', [\n            '--tag' => '{$config['kebab_name']}-assets',\n            '--force' => true,\n        ]);\n\n        \$this->components->success('Assets published successfully!');\n    }";
        }

        if ($config['generate_migrations'] ?? false) {
            $methods[] = "protected function runMigrations(): void\n    {\n        \$this->info('Running migrations...');\n\n        \$this->call('migrate');\n\n        \$this->components->success('Migrations ran successfully!');\n    }";

            $methods[] = "protected function runSeeders(): void\n    {\n        \$this->info('Running seeders...');\n\n        // Add your seeder class here\n        // \$this->call('db:seed', ['--class' => YourSeeder::class]);\n\n        \$this->components->success('Seeders ran successfully!');\n    }";
        }

        if ($hasAssets) {
            $methods[] = "protected function buildAssets(): void\n    {\n        \$this->info('Building assets...');\n\n        \$process = Process::path(base_path('packages/{$config['vendor_lower']}/{$config['kebab_name']}'))\n            ->run('npm install && npm run build');\n\n        if (\$process->successful()) {\n            \$this->components->success('Assets built successfully!');\n        } else {\n            \$this->components->error('Failed to build assets: '.\$process->errorOutput());\n        }\n    }";
        }

        $this->processor->generateFile(
            $config['base_path'].'/src/Commands/Install'.$config['studly_name'].'Command.php',
            'install-command',
            [
                'namespace' => $config['namespace'],
                'class' => 'Install'.$config['studly_name'].'Command',
                'command_name' => $config['kebab_name'].':install',
                'plugin_name' => $config['studly_name'],
                'signature_options' => ! empty($signatureOptions) ? "\n                            ".implode("\n                            ", $signatureOptions) : '',
                'publish_assets_call' => in_array('$this->publishAssets();', $calls) ? '$this->publishAssets();' : '',
                'run_migrations_block' => $this->findInArray($calls, 'runMigrations'),
                'run_seeders_block' => $this->findInArray($calls, 'runSeeders'),
                'build_assets_block' => $this->findInArray($calls, 'buildAssets'),
                'publish_assets_method' => $this->findInArray($methods, 'publishAssets'),
                'run_migrations_method' => $this->findInArray($methods, 'runMigrations'),
                'run_seeders_method' => $this->findInArray($methods, 'runSeeders'),
                'build_assets_method' => $this->findInArray($methods, 'buildAssets'),
            ]
        );
    }

    /**
     * Find a string in array that contains the search term.
     */
    protected function findInArray(array $array, string $search): string
    {
        foreach ($array as $item) {
            if (str_contains($item, $search)) {
                return $item;
            }
        }

        return '';
    }
}

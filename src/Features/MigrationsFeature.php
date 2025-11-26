<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates migration directories for the plugin.
 *
 * Creates database/migrations, database/factories, and database/seeders directories.
 */
class MigrationsFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'migrations';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_migrations'] ?? false;
    }

    public function getPriority(): int
    {
        return 25; // Structure file
    }

    public function getDirectories(array $config): array
    {
        if ($this->shouldGenerate($config)) {
            return [
                'database/migrations',
                'database/factories',
                'database/seeders',
            ];
        }

        return [];
    }

    public function generate(array $config): void
    {
        // Migrations are created via artisan commands later
        // This feature just ensures the directories exist
    }
}

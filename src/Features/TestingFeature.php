<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates testing files for the plugin.
 *
 * Creates PHPUnit, Pest, TestCase, and PHPStan configuration files.
 */
class TestingFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'testing';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate testing files
    }

    public function getPriority(): int
    {
        return 70; // Testing files
    }

    public function getDirectories(array $config): array
    {
        return ['tests', 'tests/Feature', 'tests/Unit'];
    }

    public function generate(array $config): void
    {
        // Generate PHPUnit configuration
        $this->generatePhpUnitXml($config);

        // Generate Pest configuration
        $this->generatePestPhp($config);

        // Generate TestCase
        $this->generateTestCase($config);

        // Generate PHPStan configuration if enabled
        if ($config['generate_phpstan'] ?? false) {
            $this->generatePhpStanNeon($config);
        }

        // Generate debug test
        $this->generateDebugTest($config);
    }

    protected function generatePhpUnitXml(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/phpunit.xml',
            'phpunit.xml',
            [
                'namespace' => str_replace('\\', '\\\\', $config['namespace']),
            ]
        );
    }

    protected function generatePestPhp(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/tests/Pest.php',
            'Pest',
            [
                'namespace' => $config['namespace'],
            ]
        );
    }

    protected function generateTestCase(array $config): void
    {
        $hasMigrations = $config['generate_migrations'] ?? false;

        $this->processor->generateFile(
            $config['base_path'].'/tests/TestCase.php',
            'TestCase',
            [
                'namespace' => $config['namespace'],
                'service_provider' => $config['studly_name'].'ServiceProvider',
                'use_refresh_database' => $hasMigrations ? "use Illuminate\Foundation\Testing\RefreshDatabase;\n" : '',
                'use_refresh_database_trait' => $hasMigrations ? "    use RefreshDatabase;\n" : '',
                'load_migrations' => $hasMigrations ? "\n        \$this->loadMigrationsFrom(__DIR__.'/../database/migrations');" : '',
                'database_config' => $hasMigrations ? "\n        config()->set('database.default', 'sqlite');\n        config()->set('database.connections.sqlite.database', ':memory:');" : '',
            ]
        );
    }

    protected function generatePhpStanNeon(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/phpstan.neon',
            'phpstan.neon',
            []
        );
    }

    protected function generateDebugTest(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/tests/Feature/DebugTest.php',
            'DebugTest',
            []
        );
    }
}

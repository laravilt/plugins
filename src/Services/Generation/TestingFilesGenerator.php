<?php

namespace Laravilt\Plugins\Services\Generation;

/**
 * Generates testing configuration files for plugin quality assurance.
 *
 * Creates PHPUnit, Pest, PHPStan, Pint, and Testbench configuration
 * files to ensure plugin code quality and compatibility.
 */
class TestingFilesGenerator
{
    public function __construct(protected StubProcessor $processor) {}

    /**
     * Generate phpunit.xml configuration.
     *
     * Creates PHPUnit configuration with test suite setup and coverage options.
     */
    public function generatePhpUnit(string $basePath): void
    {
        $content = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
XML;
        $this->processor->files->put($basePath.'/phpunit.xml', $content);
    }

    /**
     * Generate pint.json for Laravel Pint code styling.
     *
     * Configures Laravel Pint to use Laravel preset for consistent formatting.
     */
    public function generatePint(string $basePath): void
    {
        $content = "{\n    \"preset\": \"laravel\"\n}\n";
        $this->processor->files->put($basePath.'/pint.json', $content);
    }

    /**
     * Generate phpstan.neon for static analysis.
     *
     * Configures PHPStan at level 5 for comprehensive code analysis.
     */
    public function generatePhpStan(string $basePath): void
    {
        $content = "parameters:\n    level: 5\n    paths:\n        - src\n";
        $this->processor->files->put($basePath.'/phpstan.neon', $content);
    }

    /**
     * Generate testbench.yaml for Orchestra Testbench.
     *
     * Configures Testbench workbench for plugin development and testing.
     */
    public function generateTestbench(string $basePath): void
    {
        $content = "workbench:\n    start: '/'\n    install: true\n";
        $this->processor->files->put($basePath.'/testbench.yaml', $content);
    }

    /**
     * Generate Pest.php configuration for Pest testing framework.
     *
     * Sets up Pest to use specific test directories.
     */
    public function generatePestConfig(array $config): void
    {
        $content = "<?php\n\nuses()->in('Feature', 'Unit');\n";
        $this->processor->files->put($config['base_path'].'/tests/Pest.php', $content);
    }
}

<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates documentation files for the plugin.
 *
 * Creates LICENSE, CHANGELOG, and other documentation files.
 */
class DocumentationFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'documentation';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate documentation files
    }

    public function getPriority(): int
    {
        return 95; // Documentation file - one of the last
    }

    public function generate(array $config): void
    {
        // Generate LICENSE file
        $this->generateLicense($config);

        // Generate CHANGELOG file
        $this->generateChangelog($config);

        // Generate CODE_OF_CONDUCT file
        $this->generateCodeOfConduct($config);
    }

    protected function generateLicense(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/LICENSE.md',
            'LICENSE.md',
            [
                'license' => $config['license'],
                'author' => $config['author'],
                'year' => date('Y'),
            ]
        );
    }

    protected function generateChangelog(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/CHANGELOG.md',
            'CHANGELOG.md',
            [
                'plugin_name' => $config['studly_name'],
            ]
        );
    }

    protected function generateCodeOfConduct(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/CODE_OF_CONDUCT.md',
            'CODE_OF_CONDUCT.md',
            []
        );
    }
}

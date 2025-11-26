<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates Laravel Pint configuration file.
 *
 * Creates pint.json for code formatting.
 */
class PintFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'pint';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate Pint configuration
    }

    public function getPriority(): int
    {
        return 76; // Testing/code quality file
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/pint.json',
            'pint.json',
            []
        );
    }
}

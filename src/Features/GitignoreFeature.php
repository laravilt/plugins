<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates .gitignore file for the plugin.
 *
 * Creates standard ignore patterns for Laravel packages.
 */
class GitignoreFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'gitignore';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate .gitignore
    }

    public function getPriority(): int
    {
        return 2; // Very early - needed for git initialization
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/.gitignore',
            '.gitignore',
            []
        );
    }
}

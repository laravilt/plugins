<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates Orchestra Testbench configuration file.
 *
 * Creates testbench.yaml for package testing setup.
 */
class TestbenchFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'testbench';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate testbench configuration
    }

    public function getPriority(): int
    {
        return 75; // Testing configuration file
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/testbench.yaml',
            'testbench.yaml',
            [
                'namespace' => $config['namespace'],
                'service_provider' => $config['studly_name'].'ServiceProvider',
            ]
        );
    }
}

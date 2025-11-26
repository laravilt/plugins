<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates config file for the plugin.
 *
 * Creates a Laravel configuration file for plugin settings.
 */
class ConfigFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'config';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate config
    }

    public function getPriority(): int
    {
        return 15; // Core file
    }

    public function getDirectories(array $config): array
    {
        return ['config'];
    }

    public function generate(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/config/'.$config['config_name'].'.php',
            'config',
            [
                'package_name' => $config['studly_name'],
                'env_prefix' => $config['env_prefix'],
            ]
        );
    }
}

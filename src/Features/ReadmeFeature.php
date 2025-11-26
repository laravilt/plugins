<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates README.md file for the plugin.
 *
 * Creates comprehensive documentation with badges, installation, and usage instructions.
 */
class ReadmeFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'readme';
    }

    public function shouldGenerate(array $config): bool
    {
        return true; // Always generate README
    }

    public function getPriority(): int
    {
        return 85; // Documentation file
    }

    public function generate(array $config): void
    {
        // Build badges
        $badges = $this->buildBadges($config);

        // Build Laravilt usage section if plugin is generated
        $laraviltUsage = '';
        if ($config['generate_plugin']) {
            $laraviltUsage = $this->buildLaraviltUsage($config);
        }

        $this->processor->generateFile(
            $config['base_path'].'/README.md',
            'README.md',
            [
                'plugin_name' => $config['studly_name'],
                'description' => $config['plugin_description'] ?? "{$config['studly_name']} plugin for Laravilt",
                'vendor' => $config['vendor_lower'],
                'package' => $config['kebab_name'],
                'config' => $config['kebab_name'],
                'assets_tag' => $config['kebab_name'].'-assets',
                'badges' => $badges,
                'laravilt_usage' => $laraviltUsage,
            ]
        );
    }

    protected function buildBadges(array $config): string
    {
        $vendor = $config['vendor_lower'];
        $package = $config['kebab_name'];
        $githubOrg = $vendor; // Assuming GitHub org matches vendor name

        return <<<BADGES
[![Latest Stable Version](https://poser.pugx.org/{$vendor}/{$package}/version.svg)](https://packagist.org/packages/{$vendor}/{$package})
[![License](https://poser.pugx.org/{$vendor}/{$package}/license.svg)](https://packagist.org/packages/{$vendor}/{$package})
[![Downloads](https://poser.pugx.org/{$vendor}/{$package}/d/total.svg)](https://packagist.org/packages/{$vendor}/{$package})
[![Dependabot Updates](https://github.com/{$githubOrg}/{$package}/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/{$githubOrg}/{$package}/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/{$githubOrg}/{$package}/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/{$githubOrg}/{$package}/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/{$githubOrg}/{$package}/actions/workflows/tests.yml/badge.svg)](https://github.com/{$githubOrg}/{$package}/actions/workflows/tests.yml)
BADGES;
    }

    protected function buildLaraviltUsage(array $config): string
    {
        return <<<USAGE

## Usage

Register the plugin in your Filament panel provider:

```php
use {$config['namespace']}\\{$config['studly_name']}Plugin;

public function panel(Panel \$panel): Panel
{
    return \$panel
        // ...
        ->plugin(new {$config['studly_name']}Plugin());
}
```
USAGE;
    }
}

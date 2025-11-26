<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates language files for the plugin.
 *
 * Creates translation files for selected languages.
 */
class LanguageFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'language';
    }

    public function shouldGenerate(array $config): bool
    {
        return ! empty($config['languages']);
    }

    public function getPriority(): int
    {
        return 40; // Structure file
    }

    public function getDirectories(array $config): array
    {
        if (! $this->shouldGenerate($config)) {
            return [];
        }

        $directories = [];
        foreach ($config['languages'] as $lang) {
            $directories[] = "lang/{$lang}";
        }

        return $directories;
    }

    public function generate(array $config): void
    {
        foreach ($config['languages'] as $lang) {
            $this->processor->generateFile(
                $config['base_path']."/lang/{$lang}/{$config['kebab_name']}.php",
                'lang',
                [
                    'package_name' => $config['studly_name'],
                ]
            );
        }
    }
}

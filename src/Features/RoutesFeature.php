<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates route files (web and API) for the plugin.
 *
 * Creates routes/web.php and/or routes/api.php based on configuration.
 */
class RoutesFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'routes';
    }

    public function shouldGenerate(array $config): bool
    {
        return ($config['generate_web_routes'] ?? false) || ($config['generate_api_routes'] ?? false);
    }

    public function getPriority(): int
    {
        return 30; // Structure file
    }

    public function getDirectories(array $config): array
    {
        if ($this->shouldGenerate($config)) {
            return ['routes'];
        }

        return [];
    }

    public function generate(array $config): void
    {
        if ($config['generate_web_routes'] ?? false) {
            $this->generateWebRoutes($config);
        }

        if ($config['generate_api_routes'] ?? false) {
            $this->generateApiRoutes($config);
        }
    }

    protected function generateWebRoutes(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/routes/web.php',
            'routes/web',
            [
                'name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }

    protected function generateApiRoutes(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/routes/api.php',
            'routes/api',
            [
                'name' => $config['studly_name'],
                'kebab_name' => $config['kebab_name'],
            ]
        );
    }
}

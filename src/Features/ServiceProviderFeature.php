<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates the Laravel ServiceProvider for the plugin.
 *
 * The ServiceProvider handles package registration, views, translations,
 * migrations, routes, and asset publishing.
 */
class ServiceProviderFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'service-provider';
    }

    public function shouldGenerate(array $config): bool
    {
        // ServiceProvider is always generated
        return true;
    }

    public function getPriority(): int
    {
        return 5; // Core file - generate early
    }

    public function getDirectories(array $config): array
    {
        return ['src'];
    }

    public function generate(array $config): void
    {
        $viewsLoading = '';
        if ($config['generate_views'] ?? false) {
            $viewsLoading = "        // Load views\n        \$this->loadViewsFrom(__DIR__ . '/../resources/views', '{$config['kebab_name']}');\n";
        }

        $migrationsLoading = '';
        if ($config['generate_migrations'] ?? false) {
            $migrationsLoading = "        // Load migrations\n        \$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');\n";
        }

        $routesLoading = '';
        if ($config['generate_web_routes'] ?? false) {
            $routesLoading .= "\n        // Load web routes\n        \$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');\n";
        }
        if ($config['generate_api_routes'] ?? false) {
            $routesLoading .= "\n        // Load API routes\n        \$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');\n";
        }

        $assetsPublishes = '';
        $hasAssets = ($config['generate_css'] ?? false) || ($config['generate_js'] ?? false);
        if ($hasAssets) {
            $assetsPublishes = "            // Publish assets\n            \$this->publishes([\n                __DIR__ . '/../dist' => public_path('vendor/laravilt/{$config['kebab_name']}'),\n            ], '{$config['assets_tag']}');\n\n";
        }

        $viewsPublishes = '';
        if ($config['generate_views'] ?? false) {
            $viewsPublishes = "            // Publish views\n            \$this->publishes([\n                __DIR__ . '/../resources/views' => resource_path('views/vendor/{$config['kebab_name']}'),\n            ], '{$config['kebab_name']}-views');\n\n";
        }

        $migrationsPublishes = '';
        if ($config['generate_migrations'] ?? false) {
            $migrationsPublishes = "            // Publish migrations\n            \$this->publishes([\n                __DIR__ . '/../database/migrations' => database_path('migrations'),\n            ], '{$config['kebab_name']}-migrations');\n\n";
        }

        $this->processor->generateFile(
            $config['base_path'].'/src/'.$config['studly_name'].'ServiceProvider.php',
            'service-provider',
            [
                'namespace' => $config['namespace'],
                'class' => $config['studly_name'].'ServiceProvider',
                'id' => $config['kebab_name'],
                'config' => $config['config_name'],
                'assets_tag' => $config['assets_tag'],
                'install_command_class' => 'Install'.$config['studly_name'].'Command',
                'views_loading' => $viewsLoading,
                'migrations_loading' => $migrationsLoading,
                'routes_loading' => $routesLoading,
                'assets_publishes' => $assetsPublishes,
                'views_publishes' => $viewsPublishes,
                'migrations_publishes' => $migrationsPublishes,
            ]
        );
    }
}

![Screenshot](https://raw.githubusercontent.com/laravilt/plugins/master/arts/cover.jpg)

# Plugins Manager

[![Latest Stable Version](https://poser.pugx.org/laravilt/plugins/version.svg)](https://packagist.org/packages/laravilt/plugins)
[![License](https://poser.pugx.org/laravilt/plugins/license.svg)](https://packagist.org/packages/laravilt/plugins)
[![Downloads](https://poser.pugx.org/laravilt/plugins/d/total.svg)](https://packagist.org/packages/laravilt/plugins)
[![Dependabot Updates](https://github.com/laravilt/plugins/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/plugins/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/plugins/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/tests.yml)

Complete plugin system with generator, management, and FilamentPHP v4 compatibility for Laravilt.

## Features

- **🎨 Plugin Generator**: Scaffold complete plugins with `laravilt:plugin` command
- **📦 Component Generators**: Create resources, models, migrations, widgets, and pages
- **🔌 FilamentPHP v4 Compatible**: Implements FilamentPHP v4 plugin architecture
- **🔍 Auto-Discovery**: Automatically discovers and registers plugins from installed packages
- **⚡ Asset Management**: Built-in Vite, Tailwind CSS, and PostCSS configuration
- **🧪 Testing Utilities**: Pest, PHPUnit, PHPStan, and Pint pre-configured
- **📝 Complete Documentation**: Auto-generated README, CHANGELOG, LICENSE, and more
- **🤖 GitHub Workflows**: Pre-configured CI/CD with tests and code styling
- **🎯 AI-Optimized**: Code structure optimized for AI agent readability

## Installation

```bash
composer require laravilt/plugins
```

The package will be auto-discovered by Laravel.

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=laravilt-plugins-config
```

Configuration options in `config/laravilt-plugins.php`:

```php
return [
    'discovery' => [
        'enabled' => env('LARAVILT_PLUGINS_DISCOVERY_ENABLED', true),
        'cache' => env('LARAVILT_PLUGINS_CACHE_ENABLED', true),
    ],
    'defaults' => [
        'vendor' => env('LARAVILT_PLUGINS_DEFAULT_VENDOR', 'laravilt'),
        'author' => env('LARAVILT_PLUGINS_DEFAULT_AUTHOR', 'Fady Mondy'),
        'email' => env('LARAVILT_PLUGINS_DEFAULT_EMAIL', 'info@3x1.io'),
        'license' => env('LARAVILT_PLUGINS_DEFAULT_LICENSE', 'MIT'),
    ],
];
```

## Usage

### Creating a New Plugin

```bash
php artisan laravilt:plugin BlogExtensions --vendor=mycompany
```

Interactive prompts will guide you through the process using Laravel Prompts.

**Options:**
- `--vendor`: Vendor name (default: from config)
- `--path`: Custom output path
- `--no-components`: Skip creating sample components
- `--no-assets`: Skip asset scaffolding

### Generated Plugin Structure

```
packages/mycompany/blog-extensions/
├── .github/
│   ├── workflows/
│   │   ├── tests.yml
│   │   └── fix-php-code-styling.yml
│   ├── FUNDING.yml
│   └── CONTRIBUTING.md
├── src/
│   ├── BlogExtensionsPlugin.php          # Main plugin class
│   ├── Models/
│   ├── Resources/
│   ├── Pages/
│   ├── Widgets/
│   │   └── StatsWidget.php              # Sample widget
│   └── Components/
├── resources/
│   ├── views/
│   │   └── widgets/
│   │       └── stats.blade.php
│   ├── lang/
│   │   ├── en/
│   │   └── ar/
│   ├── js/
│   │   └── app.js
│   └── css/
│       └── app.css
├── config/
│   └── laravilt-blog-extensions.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── tests/
│   ├── Feature/
│   ├── Unit/
│   └── Pest.php
├── docs/
├── arts/
├── workbench/
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── phpunit.xml
├── phpstan.neon
├── pint.json
├── testbench.yaml
├── README.md
├── CHANGELOG.md
├── LICENSE.md
├── SECURITY.md
└── CODE_OF_CONDUCT.md
```

### Component Generator Commands

Generate resources, models, and other components for your plugin:

#### Generate a Resource

```bash
php artisan laravilt:plugin-resource Post --plugin=blog-extensions
```

Creates a Filament resource with CRUD pages.

#### Generate a Model

```bash
php artisan laravilt:plugin-model Post --plugin=blog-extensions --migration
```

Creates a model and optionally a migration.

#### Generate a Migration

```bash
php artisan laravilt:plugin-migration create_posts_table --plugin=blog-extensions --table=posts
```

Creates a database migration.

#### Generate a Widget

```bash
php artisan laravilt:plugin-widget StatsOverview --plugin=blog-extensions
```

Creates a Filament widget with view.

#### Generate a Page

```bash
php artisan laravilt:plugin-page Settings --plugin=blog-extensions
```

Creates a Filament page with view.

All commands support Laravel Prompts for interactive input.

### Using the Plugin Manager

Access the plugin manager via the facade:

```php
use Laravilt\Plugins\Facades\LaraviltPlugins;

// Get the plugin manager
$manager = LaraviltPlugins::plugin();

// Get a specific plugin
$plugin = LaraviltPlugins::plugin('blog-extensions');

// Get all plugins
$plugins = LaraviltPlugins::all();

// Get enabled plugins only
$enabled = LaraviltPlugins::enabled();

// Check if plugin exists
if (LaraviltPlugins::has('blog-extensions')) {
    // ...
}

// Boot a specific plugin
LaraviltPlugins::boot('blog-extensions');

// Boot all plugins
LaraviltPlugins::bootAll();
```

### Creating a Plugin Class

Your plugin should extend `PluginProvider`:

```php
<?php

namespace MyCompany\BlogExtensions;

use Filament\Panel;
use Laravilt\Plugins\PluginProvider;

class BlogExtensionsPlugin extends PluginProvider
{
    protected static string $id = 'blog-extensions';
    protected static string $name = 'BlogExtensions';

    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravilt-blog-extensions.php',
            'laravilt-blog-extensions'
        );
    }

    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blog-extensions');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'blog-extensions');

        // Publish assets
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/laravilt/blog-extensions'),
        ], 'laravilt-blog-extensions-assets');
    }

    public function register(Panel $panel): void
    {
        // Register with Filament panel
        $panel->renderHook(
            'panels::body.end',
            fn () => view('blog-extensions::scripts')
        );
    }
}
```

### Registering Plugin in Panel

```php
use MyCompany\BlogExtensions\BlogExtensionsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            BlogExtensionsPlugin::make(),
        ]);
}
```

### Asset Building

Each plugin comes with Vite configuration:

```bash
# Development
cd packages/mycompany/blog-extensions
npm install
npm run dev

# Production build
npm run build
```

Assets will be compiled to the `dist/` directory.

### Testing

Each plugin includes comprehensive testing setup:

```bash
cd packages/mycompany/blog-extensions

# Run Pest tests
vendor/bin/pest

# Run tests with coverage
vendor/bin/pest --coverage

# Run PHPStan analysis
vendor/bin/phpstan analyse

# Fix code style with Pint
vendor/bin/pint
```

## Architecture

### Service Layer

The package uses a clean service-oriented architecture:

```
src/
  Services/
    PluginGenerator.php              # Orchestrates plugin generation
    Generation/
      StubProcessor.php              # Processes stub templates
      CoreFilesGenerator.php         # Generates plugin, composer, config
      TestingFilesGenerator.php      # Generates test configurations
      DocumentationGenerator.php     # Generates docs and licenses
      GitHubFilesGenerator.php       # Generates workflows and templates
      AssetFilesGenerator.php        # Generates asset configurations
  Commands/
    MakePluginCommand.php            # Main plugin generator command
    MakePluginResourceCommand.php    # Resource generator
    MakePluginModelCommand.php       # Model generator
    MakePluginMigrationCommand.php   # Migration generator
    MakePluginWidgetCommand.php      # Widget generator
    MakePluginPageCommand.php        # Page generator
    Concerns/
      ManagesStubs.php               # Stub management helpers
      RunsCommands.php               # Process execution helpers
      HandlesFiles.php               # File operation helpers
```

### AI-Optimized Code

All code follows strict AI-readability standards:
- ✅ No file exceeds 200 lines
- ✅ Each class has single responsibility
- ✅ Clear, descriptive naming
- ✅ Comprehensive documentation
- ✅ Well-organized structure

See `.ai-standards.md` for complete guidelines.

## Plugin Discovery

The package automatically discovers plugins using Composer's `extra.laravel.providers`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "MyCompany\\BlogExtensions\\BlogExtensionsPlugin"
            ]
        }
    }
}
```

Plugins are cached for performance. Clear cache with:

```bash
php artisan config:clear
```

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or 12.0
- Filament 3.0 or higher (for panel plugins)

## Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
# Format code
composer format

# Analyze code
composer analyse

# Run all checks
composer test && composer analyse && composer format
```

### Verifying AI Standards

```bash
./scripts/verify-ai-standards.sh
```

This script checks:
- File sizes (no file over 200 lines)
- Test coverage
- PHPStan analysis
- Code formatting
- Documentation presence

## Contributing

Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email info@3x1.io instead of using the issue tracker.

## Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

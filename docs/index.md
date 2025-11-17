# Laravilt Plugins Documentation

Complete plugin system with generator, management, and FilamentPHP compatibility for Laravilt.

## Installation

```bash
composer require laravilt/plugins
```

The package will be auto-discovered by Laravel.

## Usage

### Creating a New Plugin

Generate a new plugin with complete professional structure:

```bash
php artisan laravilt:plugin BlogExtensions --vendor=mycompany
```

### Options

- `--vendor`: Vendor name (default: laravilt)
- `--path`: Custom output path
- `--no-components`: Skip creating sample components
- `--no-assets`: Skip asset scaffolding

### Generated Structure

The command generates a complete professional package with:

- GitHub Actions workflows (tests, code styling, dependabot)
- Issue templates
- Testing configuration (PHPUnit, Pest, PHPStan, Pint)
- Documentation structure
- Laravel Workbench setup
- Sample components and widgets
- Asset build configuration (Vite, Vue, Tailwind)
- i18n support (English + Arabic)

## Plugin Development

### Service Provider

All generated plugins extend `PackageServiceProvider` and implement `Filament\Contracts\Plugin`:

```php
class MyPluginServiceProvider extends PackageServiceProvider implements Plugin
{
    public static string $name = 'my-plugin';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravilt-my-plugin')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([])
            ->hasCommands([]);
    }
}
```

### Naming Conventions

- **Config files**: `laravilt-{plugin}.php`
- **Env variables**: `LARAVILT_{PLUGIN}_ENABLED`
- **Publish tags**: `laravilt-{plugin}-config`, `laravilt-{plugin}-assets`
- **Asset paths**: `public/vendor/laravilt/{plugin}/`

## API Reference

### PluginManager

Manages plugin registration and lifecycle:

```php
use Laravilt\Plugins\Contracts\PluginManager;

$manager = app(PluginManager::class);

// Register a plugin
$manager->register($plugin);

// Boot a plugin
$manager->boot('plugin-id');

// Get all plugins
$plugins = $manager->all();

// Get enabled plugins
$enabled = $manager->enabled();
```

### Plugin Discovery

Automatically discovers plugins from installed packages:

```php
use Laravilt\Plugins\Support\PluginDiscovery;

$discovery = new PluginDiscovery($app);
$plugins = $discovery->discover();
```

## Testing

Run tests:

```bash
composer test
```

Code style:

```bash
composer format
```

Static analysis:

```bash
composer analyse
```

## Examples

### Creating a Custom Component

```php
namespace MyVendor\MyPlugin\Components;

use Filament\Forms\Components\Field;

class CustomInput extends Field
{
    protected string $view = 'my-plugin::components.custom-input';

    public function withIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }
}
```

### Creating a Widget

```php
namespace MyVendor\MyPlugin\Widgets;

use Filament\Widgets\Widget;

class StatsWidget extends Widget
{
    protected static string $view = 'my-plugin::widgets.stats-widget';

    public function getStats(): array
    {
        return [
            'Total Users' => 1234,
            'Active Sessions' => 56,
        ];
    }
}
```

## Configuration

The plugin system can be configured via `config/laravilt/plugins.php`:

```php
return [
    'discovery' => [
        'enabled' => true,
        'cache' => true,
    ],

    'paths' => [
        base_path('packages'),
    ],

    'defaults' => [
        'vendor' => 'laravilt',
        'author' => 'Laravilt Team',
        'email' => 'hello@laravilt.com',
    ],
];
```

## Contributing

See [CONTRIBUTING.md](../.github/CONTRIBUTING.md) for details.

## License

MIT License. See [LICENSE.md](../LICENSE.md) for details.

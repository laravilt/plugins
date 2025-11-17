# Laravilt Plugins Package

Complete plugin system with generator, management, and FilamentPHP compatibility for Laravilt.

## Features

- **Plugin Generator**: `php artisan laravilt:make-plugin` command to scaffold new plugins
- **FilamentPHP Compatible**: Implements FilamentPHP plugin provider pattern
- **Auto-Discovery**: Automatically discovers and registers plugins from installed packages
- **Asset Management**: Built-in Vite configuration for plugin assets
- **Testing Utilities**: Base test case for plugin development

## Installation

```bash
composer require laravilt/plugins
```

The package will be auto-discovered by Laravel.

## Usage

### Creating a New Plugin

```bash
php artisan laravilt:make-plugin BlogExtensions --vendor=mycompany
```

Options:
- `--vendor`: Vendor name (default: laravilt)
- `--path`: Custom output path
- `--no-components`: Skip creating sample components
- `--no-assets`: Skip asset scaffolding

### Generated Plugin Structure

```
packages/mycompany/blog-extensions/
├── src/
│   ├── BlogExtensionsPluginServiceProvider.php
│   ├── Components/
│   │   └── CustomInput.php
│   └── Widgets/
│       └── StatsWidget.php
├── resources/
│   ├── views/
│   ├── js/
│   └── css/
├── config/
├── tests/
├── composer.json
├── package.json
└── README.md
```

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=laravilt-plugins-config
```

## License

MIT

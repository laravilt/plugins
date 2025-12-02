![Screenshot](https://raw.githubusercontent.com/laravilt/plugins/master/arts/cover.jpg)

# Laravilt Plugins

[![Latest Stable Version](https://poser.pugx.org/laravilt/plugins/version.svg)](https://packagist.org/packages/laravilt/plugins)
[![License](https://poser.pugx.org/laravilt/plugins/license.svg)](https://packagist.org/packages/laravilt/plugins)
[![Downloads](https://poser.pugx.org/laravilt/plugins/d/total.svg)](https://packagist.org/packages/laravilt/plugins)
[![Dependabot Updates](https://github.com/laravilt/plugins/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/plugins/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/plugins/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/plugins/actions/workflows/tests.yml)

Complete plugin system with generator, management, and FilamentPHP v4 compatibility for Laravilt.ipsum

## ✨ Features

### 🎨 Plugin Generation
- **Interactive CLI** - Laravel Prompts with smart defaults
- **Factory Pattern** - Extensible feature-based architecture
- **Priority System** - Ordered feature execution (0-100)
- **Stub Processing** - Template-based file generation
- **Auto-Discovery** - Automatic plugin registration

### 🧩 Component Generators
Generate 13 component types within plugins:
- **Migration** - Database migrations with timestamps
- **Model** - Eloquent models with proper namespacing
- **Controller** - HTTP controllers
- **Command** - Artisan commands
- **Job** - Queueable jobs
- **Event** - Event classes
- **Listener** - Event listeners
- **Notification** - Notifications with mail support
- **Seeder** - Database seeders
- **Factory** - Model factories
- **Test** - Feature/Unit tests
- **Lang** - Language files
- **Route** - Route files

### 🤖 MCP Server Integration
- **AI Agent Support** - Built-in MCP server for Claude, GPT, etc.
- **6 Tools Available** - list-plugins, plugin-info, generate-plugin, generate-component, list-component-types, plugin-structure
- **Natural Language** - Generate plugins through conversation
- **Auto-Discovery** - AI agents can explore plugin ecosystem

### 🎨 Professional Assets
- **Cover Images** - Auto-generated 1200x630px screenshots
- **Dark Theme** - Professional gradient backgrounds
- **Plugin Branding** - Cyan icon with Laravilt branding
- **Social Media Ready** - Optimized for GitHub/Twitter previews
- **README Integration** - Auto-embedded in documentation

### ⚙️ GitHub Integration
- **Workflows** - tests.yml, fix-php-code-styling.yml, dependabot-auto-merge.yml
- **Issue Templates** - Bug reports, feature requests (GitHub forms)
- **Dependabot** - Automated dependency updates
- **FUNDING.yml** - GitHub Sponsors support
- **CONTRIBUTING.md** - Contribution guidelines
- **SECURITY.md** - Security vulnerability reporting

### 📦 Complete Package Setup
- **Service Provider** - Auto-discovery compatible
- **Configuration** - Publishable config with env support
- **Composer** - PSR-4 autoloading, version constraints
- **Testing** - Pest, PHPStan, Pint, Testbench
- **Assets** - Vite, Tailwind v4, Vue.js plugin support
- **Documentation** - README, CHANGELOG, LICENSE, CODE_OF_CONDUCT

## 📋 Requirements

- PHP 8.3+
- Laravel 12+
- FilamentPHP v4+ (for plugins features)
- Composer 2+
- Node.js 18+ (for asset compilation)

## 🚀 Installation

```bash
composer require laravilt/plugins
```

The service provider is auto-discovered and will register automatically.

### Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=laravilt-plugins-config
```

Configure defaults in `config/laravilt-plugins.php`:

```php
return [
    'defaults' => [
        'vendor' => env('LARAVILT_PLUGINS_DEFAULT_VENDOR', 'laravilt'),
        'author' => env('LARAVILT_PLUGINS_DEFAULT_AUTHOR', 'Your Name'),
        'email' => env('LARAVILT_PLUGINS_DEFAULT_EMAIL', 'your@email.com'),
        'license' => env('LARAVILT_PLUGINS_DEFAULT_LICENSE', 'MIT'),
        'github_sponsor' => env('LARAVILT_PLUGINS_DEFAULT_GITHUB_SPONSOR', 'yourusername'),
    ],
];
```

### MCP Server Setup (for AI Agents)

Install the MCP server configuration:

```bash
php artisan laravilt:install-mcp
```

This command will:
- Publish `routes/ai.php` (if needed)
- Register the MCP server in your routes
- Update `.mcp.json` for AI clients

After installation, restart your AI agent to access the plugin management tools.

## 📖 Usage

### Generate a Plugin

Interactive mode (recommended):

```bash
php artisan laravilt:plugin MyPlugin
```

Non-interactive mode:

```bash
php artisan laravilt:plugin MyPlugin --no-interaction
```

The command will guide you through:
1. Plugin name and description
2. Feature selection (migrations, views, routes, assets, etc.)
3. Author details (optional)
4. GitHub sponsor (optional)
5. Language selection

### Generate Components

Use the unified component generator:

```bash
php artisan laravilt:make
```

Or specify directly:

```bash
# Generate a model
php artisan laravilt:make my-plugin model Post

# Generate a controller
php artisan laravilt:make my-plugin controller PostController

# Generate a migration
php artisan laravilt:make my-plugin migration CreatePostsTable

# Generate a command
php artisan laravilt:make my-plugin command ProcessPostsCommand

# Generate a job
php artisan laravilt:make my-plugin job ProcessPost

# Generate a test
php artisan laravilt:make my-plugin test PostTest
```

All 13 component types are supported with proper namespace detection and PSR-4 structure.

### Generated Plugin Structure

```
my-plugin/
├── .github/
│   ├── workflows/
│   │   ├── tests.yml
│   │   ├── fix-php-code-styling.yml
│   │   └── dependabot-auto-merge.yml
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug.yml
│   │   └── config.yml
│   ├── CONTRIBUTING.md
│   ├── FUNDING.yml
│   ├── SECURITY.md
│   └── dependabot.yml
├── arts/
│   └── screenshot.jpg                    # Auto-generated cover image
├── config/
│   └── laravilt-my-plugin.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   │   └── app.css                       # Tailwind v4
│   ├── js/
│   │   └── app.js                        # Vue.js plugin
│   ├── lang/
│   │   └── en/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
├── src/
│   ├── Commands/
│   │   └── InstallMyPluginCommand.php
│   ├── Http/
│   │   └── Controllers/
│   ├── Models/
│   ├── MyPluginPlugin.php                # Main plugin class
│   └── MyPluginServiceProvider.php
├── tests/
│   ├── Feature/
│   │   └── DebugTest.php
│   ├── Pest.php
│   └── TestCase.php
├── CHANGELOG.md
├── CODE_OF_CONDUCT.md
├── LICENSE.md
├── README.md
├── composer.json
├── package.json                          # If JS selected
├── phpstan.neon
├── pint.json
├── testbench.yaml
└── vite.plugin.js                        # If JS selected
```

## 🏗️ Architecture

### Factory Pattern

The plugin system uses a Factory Pattern for extensible feature generation:

```
Features (Priority 0-100)
├── Core Files (0-20)
│   ├── ComposerJsonFeature (1)
│   ├── GitignoreFeature (2)
│   ├── ServiceProviderFeature (5)
│   ├── PluginClassFeature (10)
│   ├── InstallCommandFeature (12)
│   └── ConfigFeature (15)
├── Structure Files (21-40)
│   ├── MigrationsFeature (25)
│   ├── RoutesFeature (30)
│   ├── ViewsFeature (35)
│   └── LanguageFeature (40)
├── Asset Files (41-60)
│   ├── CssFeature (50)
│   ├── JsFeature (51)
│   └── ArtsFeature (55)
├── Testing Files (61-80)
│   ├── TestingFeature (70)
│   ├── TestbenchFeature (75)
│   └── PintFeature (76)
└── Documentation Files (81-100)
    ├── ReadmeFeature (85)
    ├── GitHubFeature (90)
    └── DocumentationFeature (95)
```

### Extending with Custom Features

Create a custom feature:

```php
<?php

namespace App\PluginFeatures;

use Laravilt\Plugins\Features\AbstractFeature;

class CustomFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'custom';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_custom'] ?? false;
    }

    public function getPriority(): int
    {
        return 99; // Execute near the end
    }

    public function generate(array $config): void
    {
        // Your generation logic
        $this->processor->generateFile(
            $config['base_path'].'/custom/file.php',
            'custom/file',
            ['key' => 'value']
        );
    }
}
```

Register in config:

```php
'features' => [
    // ... existing features
    \App\PluginFeatures\CustomFeature::class,
],
```

## 🤖 MCP Server

### Available Tools

#### `list-plugins`
List all installed Laravilt plugins.

#### `plugin-info`
Get detailed information about a specific plugin.

**Arguments:**
- `plugin` (string): Plugin name in kebab-case

#### `generate-plugin`
Generate a new plugin with specified features.

**Arguments:**
- `name` (string): Plugin name in StudlyCase
- `description` (string, optional)
- `migrations` (bool, default: false)
- `views` (bool, default: false)
- `webRoutes` (bool, default: false)
- `apiRoutes` (bool, default: false)
- `css` (bool, default: false)
- `js` (bool, default: false)
- `arts` (bool, default: true)
- `github` (bool, default: true)
- `phpstan` (bool, default: true)

#### `generate-component`
Generate a component within a plugin.

**Arguments:**
- `plugin` (string): Plugin name in kebab-case
- `type` (string): Component type (migration, model, controller, etc.)
- `name` (string): Component name

#### `list-component-types`
List all available component types.

#### `plugin-structure`
Get the complete directory structure of a plugin.

**Arguments:**
- `plugin` (string): Plugin name in kebab-case

### AI Agent Examples

```
You: "List all my plugins"
AI: [calls list-plugins tool]

You: "Create a blog plugin with migrations and views"
AI: [calls generate-plugin with appropriate parameters]

You: "Generate a Post model in the blog plugin"
AI: [calls generate-component]
```

## 🧪 Testing

Run tests in the plugins package:

```bash
cd packages/laravilt/plugins
composer test
```

Run tests in a generated plugin:

```bash
cd packages/myvendor/my-plugin
composer test          # Run Pest tests
composer format        # Format code with Pint
composer analyse       # Run PHPStan analysis
```

## 📚 Documentation

Comprehensive documentation is available in the `docs/` directory:

- [Getting Started](docs/getting-started.md)
- [Architecture](docs/architecture.md)
- [Plugin Generation](docs/plugin-generation.md)
- [Component Generators](docs/component-generators.md)
- [Factory Pattern](docs/factory-pattern.md)
- [Features System](docs/features-system.md)
- [MCP Server](docs/mcp-server.md)
- [API Reference](docs/api-reference.md)

## 🤝 Contributing

Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.

## 🔒 Security

If you discover any security-related issues, please email info@3x1.io instead of using the issue tracker.

## 📝 Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for recent changes.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 👥 Credits

- [Fady Mondy](https://github.com/fadymondy)
- [All Contributors](../../contributors)

## 🌟 Sponsors

Support this project via [GitHub Sponsors](https://github.com/sponsors/fadymondy).

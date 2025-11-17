<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

class MakePluginCommand extends Command
{
    protected $signature = 'laravilt:plugin
                            {name? : The name of the plugin}
                            {--vendor= : The vendor name}
                            {--path= : The base path where the plugin will be created}
                            {--no-components : Skip creating sample components}
                            {--no-assets : Skip asset scaffolding}';

    protected $description = 'Create a new Laravilt plugin package';

    protected Filesystem $files;

    public function handle(): int
    {
        $this->files = app(Filesystem::class);

        // Get plugin name
        $name = $this->argument('name') ?: text(
            label: 'Plugin name',
            placeholder: 'E.g., BlogExtensions',
            required: true
        );

        if (! $name) {
            $this->error('Plugin name is required.');
            return self::FAILURE;
        }

        // Get vendor name
        $vendor = $this->option('vendor') ?: text(
            label: 'Vendor name',
            default: config('laravilt.plugins.defaults.vendor', 'laravilt'),
            required: true
        );

        // Prepare names
        $studlyName = Str::studly($name);
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);
        $vendorLower = Str::lower($vendor);

        // Determine path
        $basePath = $this->option('path') ?: base_path("packages/{$vendorLower}/{$kebabName}");

        // Check if directory exists
        if ($this->files->exists($basePath)) {
            $this->error("Plugin directory already exists: {$basePath}");
            return self::FAILURE;
        }

        // Confirm creation
        if (! confirm(
            label: "Create plugin '{$studlyName}' in {$basePath}?",
            default: true
        )) {
            $this->info('Plugin creation cancelled.');
            return self::SUCCESS;
        }

        $this->info("Creating plugin: {$studlyName}...");

        // Create directory structure
        $this->createDirectoryStructure($basePath);

        // Generate files
        $this->generateServiceProvider($basePath, $studlyName, $vendorLower, $kebabName);
        $this->generateComposerJson($basePath, $studlyName, $vendorLower, $kebabName);

        if (! $this->option('no-components')) {
            $this->generateSampleComponents($basePath, $studlyName, $vendorLower, $kebabName);
        }

        if (! $this->option('no-assets')) {
            $this->generateAssetFiles($basePath, $kebabName);
        }

        $this->generateConfigFile($basePath, $snakeName, $kebabName);
        $this->generateReadme($basePath, $studlyName, $vendorLower, $kebabName);
        $this->generateGitignore($basePath);

        // Generate additional files
        $this->generateGitHubWorkflows($basePath, $studlyName, $vendorLower, $kebabName);
        $this->generateGitHubFiles($basePath, $studlyName, $vendorLower, $kebabName);
        $this->generateTestingConfig($basePath, $vendorLower, $kebabName);
        $this->generateDocumentation($basePath, $studlyName);
        $this->generateChangelogAndLicense($basePath, $studlyName);

        // Success message
        $this->newLine();
        $this->info('✅ Plugin created successfully!');
        $this->newLine();

        $this->info('📦 Next steps:');
        $this->line("  1. cd {$basePath}");
        $this->line('  2. composer install');
        $this->line('  3. npm install && npm run build');
        $this->line('  4. Register the plugin in your panel provider');
        $this->newLine();

        $this->info('📖 Plugin location: ' . $basePath);

        return self::SUCCESS;
    }

    protected function createDirectoryStructure(string $basePath): void
    {
        $directories = [
            '.github/workflows',
            '.github/ISSUE_TEMPLATE',
            'arts',
            'src',
            'src/Components',
            'src/Widgets',
            'src/Pages',
            'src/Resources',
            'src/Themes',
            'src/Commands',
            'resources/views',
            'resources/views/components',
            'resources/views/widgets',
            'resources/lang/en',
            'resources/lang/ar',
            'resources/js',
            'resources/css',
            'database/migrations',
            'config',
            'tests/Feature',
            'tests/Unit',
            'docs',
            'workbench',
        ];

        foreach ($directories as $directory) {
            $this->files->makeDirectory("{$basePath}/{$directory}", 0755, true);
        }

        // Add .gitkeep to specific empty directories
        $gitkeepDirs = ['arts', 'docs', 'workbench'];
        foreach ($gitkeepDirs as $dir) {
            $this->files->put("{$basePath}/{$dir}/.gitkeep", '');
        }

        $this->info('Created directory structure.');
    }

    protected function generateServiceProvider(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        $namespace = Str::studly($vendorLower) . '\\' . $studlyName;
        $configName = 'laravilt-' . $kebabName;
        $assetsTag = 'laravilt-' . $kebabName . '-assets';

        $content = <<<PHP
<?php

namespace {$namespace};

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class {$studlyName}PluginServiceProvider extends PackageServiceProvider implements Plugin
{
    use EvaluatesClosures;

    /**
     * The plugin ID (must be unique).
     */
    public static string \$name = '{$kebabName}';

    /**
     * Configure the package.
     */
    public function configurePackage(Package \$package): void
    {
        \$package
            ->name('{$configName}')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                // Add your migrations here
            ])
            ->hasCommands([
                // Add your commands here
            ]);
    }

    /**
     * Register the plugin with Laravilt.
     */
    public function register(): void
    {
        parent::register();

        // Register any additional services
    }

    /**
     * Boot the plugin.
     */
    public function boot(): void
    {
        parent::boot();

        // Publish assets
        \$this->publishes([
            __DIR__ . '/../dist' => public_path('vendor/laravilt/{$kebabName}'),
        ], '{$assetsTag}');

        // Register any additional boot logic
    }

    /**
     * Get the plugin ID.
     */
    public function getId(): string
    {
        return static::\$name;
    }

    /**
     * Register the plugin with a panel.
     */
    public function register(Panel \$panel): void
    {
        \$panel
            ->renderHook(
                'panels::body.end',
                fn () => view('{$kebabName}::scripts')
            );
    }

    /**
     * Boot the plugin for a panel.
     */
    public function boot(Panel \$panel): void
    {
        // Add panel-specific boot logic
    }

    /**
     * Create a new instance of the plugin.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get the default instance of the plugin.
     */
    public static function get(): static
    {
        return filament(static::\$name);
    }
}
PHP;

        $this->files->put("{$basePath}/src/{$studlyName}PluginServiceProvider.php", $content);
        $this->info('Generated service provider.');
    }

    protected function generateComposerJson(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        $namespace = Str::studly($vendorLower) . "\\\\{$studlyName}\\\\";
        $author = config('laravilt.plugins.defaults.author', 'Laravilt Team');
        $email = config('laravilt.plugins.defaults.email', 'hello@laravilt.com');

        $content = <<<JSON
{
    "name": "{$vendorLower}/{$kebabName}",
    "description": "{$studlyName} plugin for Laravilt",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "{$author}",
            "email": "{$email}"
        }
    ],
    "require": {
        "php": "^8.2",
        "filament/filament": "^3.0",
        "spatie/laravel-package-tools": "^1.14"
    },
    "autoload": {
        "psr-4": {
            "{$namespace}": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "{$namespace}{$studlyName}PluginServiceProvider"
            ]
        }
    }
}
JSON;

        $this->files->put("{$basePath}/composer.json", $content);
        $this->info('Generated composer.json.');
    }

    protected function generateSampleComponents(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        $namespace = Str::studly($vendorLower) . '\\' . $studlyName;

        // Custom Input Component
        $customInput = <<<PHP
<?php

namespace {$namespace}\\Components;

use Filament\\Forms\\Components\\Field;

class CustomInput extends Field
{
    protected string \$view = '{$kebabName}::components.custom-input';

    protected string|null \$icon = null;

    public function withIcon(string \$icon): static
    {
        \$this->icon = \$icon;

        return \$this;
    }

    public function getIcon(): string|null
    {
        return \$this->icon;
    }
}
PHP;

        $this->files->put("{$basePath}/src/Components/CustomInput.php", $customInput);

        // Stats Widget
        $statsWidget = <<<PHP
<?php

namespace {$namespace}\\Widgets;

use Filament\\Widgets\\Widget;

class StatsWidget extends Widget
{
    protected static string \$view = '{$kebabName}::widgets.stats-widget';

    protected int|\string|\Closure \$columnSpan = 'full';

    public function getStats(): array
    {
        return [
            'Total Users' => 1234,
            'Active Sessions' => 56,
            'Revenue' => '\$12,345',
        ];
    }
}
PHP;

        $this->files->put("{$basePath}/src/Widgets/StatsWidget.php", $statsWidget);

        // Custom Input View
        $customInputView = <<<'BLADE'
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="custom-input-wrapper">
        @if($getIcon())
            <span class="input-icon">
                <x-filament::icon :icon="$getIcon()" class="w-5 h-5" />
            </span>
        @endif

        <input
            {!! $attributes->merge([
                'id' => $getId(),
                'type' => 'text',
                'class' => 'fi-input block w-full',
            ]) !!}
        />
    </div>
</x-dynamic-component>
BLADE;

        $this->files->put(
            "{$basePath}/resources/views/components/custom-input.blade.php",
            $customInputView
        );

        // Stats Widget View
        $statsWidgetView = <<<'BLADE'
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="stats-widget grid grid-cols-3 gap-4">
            @foreach($this->getStats() as $label => $value)
                <div class="stat-item">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div>
                    <div class="text-2xl font-bold">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
BLADE;

        $this->files->put(
            "{$basePath}/resources/views/widgets/stats-widget.blade.php",
            $statsWidgetView
        );

        // Scripts view for asset loading
        $scriptsView = <<<BLADE
@if(app()->environment('production'))
    <link rel="stylesheet" href="{{ asset('vendor/{$kebabName}/app.css') }}">
    <script src="{{ asset('vendor/{$kebabName}/app.js') }}" defer></script>
@else
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ], 'vendor/{$kebabName}')
@endif
BLADE;

        $this->files->put(
            "{$basePath}/resources/views/scripts.blade.php",
            $scriptsView
        );

        $this->info('Generated sample components and widgets.');
    }

    protected function generateAssetFiles(string $basePath, string $kebabName): void
    {
        // package.json
        $packageJson = <<<JSON
{
    "name": "{$kebabName}",
    "version": "1.0.0",
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "@vitejs/plugin-vue": "^5.0.0",
        "autoprefixer": "^10.4.16",
        "laravel-vite-plugin": "^1.0.0",
        "postcss": "^8.4.32",
        "tailwindcss": "^3.4.0",
        "vite": "^5.0.0",
        "vue": "^3.4.0"
    }
}
JSON;

        $this->files->put("{$basePath}/package.json", $packageJson);

        // vite.config.js
        $viteConfig = <<<JS
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            buildDirectory: 'dist',
        }),
        vue(),
    ],
    build: {
        manifest: true,
        outDir: 'dist',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});
JS;

        $this->files->put("{$basePath}/vite.config.js", $viteConfig);

        // CSS file
        $css = <<<CSS
/* Plugin styles */
.custom-input-wrapper {
    /* Add your custom styles here */
}

.stats-widget {
    /* Add widget styles here */
}
CSS;

        $this->files->put("{$basePath}/resources/css/app.css", $css);

        // JS file
        $js = <<<JS
// Plugin JavaScript
console.log('Plugin loaded: {$kebabName}');

// Add your JavaScript here
JS;

        $this->files->put("{$basePath}/resources/js/app.js", $js);

        // tailwind.config.js
        $tailwindConfig = <<<JS
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
JS;

        $this->files->put("{$basePath}/tailwind.config.js", $tailwindConfig);

        // postcss.config.js
        $postcssConfig = <<<JS
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
JS;

        $this->files->put("{$basePath}/postcss.config.js", $postcssConfig);

        $this->info('Generated asset files and build configuration.');
    }

    protected function generateConfigFile(
        string $basePath,
        string $snakeName,
        string $kebabName
    ): void {
        $envPrefix = 'LARAVILT_' . strtoupper($snakeName);
        $configName = 'laravilt-' . $kebabName;

        $content = <<<PHP
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Settings
    |--------------------------------------------------------------------------
    |
    | Configure your plugin settings here.
    |
    */

    'enabled' => env('{$envPrefix}_ENABLED', true),

    // Add your configuration options here
];
PHP;

        $this->files->put("{$basePath}/config/{$configName}.php", $content);
        $this->info('Generated configuration file.');
    }

    protected function generateReadme(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        $configTag = 'laravilt-' . $kebabName . '-config';
        $assetsTag = 'laravilt-' . $kebabName . '-assets';

        $content = <<<MD
# {$studlyName} Plugin for Laravilt

A Laravilt plugin that provides {$studlyName} functionality.

## Installation

Install the package via composer:

```bash
composer require {$vendorLower}/{$kebabName}
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="{$configTag}"
```

Publish the assets:

```bash
php artisan vendor:publish --tag="{$assetsTag}"
```

## Usage

Register the plugin in your panel provider:

```php
use {$vendorLower}\\{$studlyName}\\{$studlyName}PluginServiceProvider;

public function panel(Panel \$panel): Panel
{
    return \$panel
        ->plugins([
            {$studlyName}PluginServiceProvider::make(),
        ]);
}
```

### Using Components

```php
use {$vendorLower}\\{$studlyName}\\Components\\CustomInput;

public function form(Form \$form): Form
{
    return \$form
        ->schema([
            CustomInput::make('field_name')
                ->label('Field Label')
                ->withIcon('heroicon-o-star'),
        ]);
}
```

### Using Widgets

```php
use {$vendorLower}\\{$studlyName}\\Widgets\\StatsWidget;

class Dashboard extends Page
{
    protected function getHeaderWidgets(): array
    {
        return [
            StatsWidget::class,
        ];
    }
}
```

## Development

Build assets for development:

```bash
npm install
npm run dev
```

Build assets for production:

```bash
npm run build
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
MD;

        $this->files->put("{$basePath}/README.md", $content);
        $this->info('Generated README.md.');
    }

    protected function generateGitignore(string $basePath): void
    {
        $content = <<<GITIGNORE
/node_modules
/dist
/vendor
composer.lock
package-lock.json
.env
.DS_Store
.idea
.vscode
GITIGNORE;

        $this->files->put("{$basePath}/.gitignore", $content);
        $this->info('Generated .gitignore.');
    }

    protected function generateGitHubWorkflows(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        // Tests workflow
        $testsWorkflow = <<<YAML
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: \${{ matrix.os }}
    strategy:
      fail-fast: true
      matrix:
        os: [ubuntu-latest]
        php: [8.2, 8.3]
        laravel: [11.*, 12.*]
        dependency-version: [prefer-stable]

    name: P\${{ matrix.php }} - L\${{ matrix.laravel }} - \${{ matrix.dependency-version }}

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: \${{ matrix.php }}
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite
          coverage: none

      - name: Install dependencies
        run: |
          composer require "laravel/framework:\${{ matrix.laravel }}" --no-interaction --no-update
          composer update --\${{ matrix.dependency-version }} --prefer-dist --no-interaction

      - name: Execute tests
        run: vendor/bin/pest
YAML;

        $this->files->put("{$basePath}/.github/workflows/tests.yml", $testsWorkflow);

        // PHP Code Styling workflow
        $codeStylingWorkflow = <<<YAML
name: Fix PHP Code Styling

on: [push]

jobs:
  php-code-styling:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout code
        uses: actions/checkout@v4
        with:
          ref: \${{ github.head_ref }}

      - name: Fix PHP code style issues
        uses: aglipanci/laravel-pint-action@2.4
        with:
          preset: laravel

      - name: Commit changes
        uses: stefanzweifel/git-auto-commit-action@v5
        with:
          commit_message: Fix styling
YAML;

        $this->files->put("{$basePath}/.github/workflows/fix-php-code-styling.yml", $codeStylingWorkflow);

        // Dependabot auto-merge workflow
        $dependabotWorkflow = <<<YAML
name: Dependabot Auto-Merge

on: pull_request_target

permissions:
  pull-requests: write
  contents: write

jobs:
  dependabot:
    runs-on: ubuntu-latest
    if: \${{ github.actor == 'dependabot[bot]' }}
    steps:
      - name: Dependabot metadata
        id: metadata
        uses: dependabot/fetch-metadata@v2
        with:
          github-token: "\${{ secrets.GITHUB_TOKEN }}"

      - name: Auto-merge Dependabot PRs for semver-minor updates
        if: \${{steps.metadata.outputs.update-type == 'version-update:semver-minor'}}
        run: gh pr merge --auto --merge "\$PR_URL"
        env:
          PR_URL: \${{github.event.pull_request.html_url}}
          GITHUB_TOKEN: \${{secrets.GITHUB_TOKEN}}

      - name: Auto-merge Dependabot PRs for semver-patch updates
        if: \${{steps.metadata.outputs.update-type == 'version-update:semver-patch'}}
        run: gh pr merge --auto --merge "\$PR_URL"
        env:
          PR_URL: \${{github.event.pull_request.html_url}}
          GITHUB_TOKEN: \${{secrets.GITHUB_TOKEN}}
YAML;

        $this->files->put("{$basePath}/.github/workflows/dependabot-auto-merge.yml", $dependabotWorkflow);

        $this->info('Generated GitHub workflows.');
    }

    protected function generateGitHubFiles(
        string $basePath,
        string $studlyName,
        string $vendorLower,
        string $kebabName
    ): void {
        // CONTRIBUTING.md
        $contributing = <<<MD
# Contributing to {$studlyName}

Thank you for considering contributing to {$studlyName}!

## Pull Requests

1. Fork the repository
2. Create a new branch for your feature
3. Write tests for your changes
4. Ensure all tests pass
5. Submit a pull request

## Coding Standards

- Follow PSR-12 coding standards
- Run `composer format` before committing
- Write tests for new features

## Running Tests

```bash
composer test
```

## Code Style

```bash
composer format
```
MD;

        $this->files->put("{$basePath}/.github/CONTRIBUTING.md", $contributing);

        // dependabot.yml
        $dependabot = <<<YAML
version: 2
updates:
  - package-ecosystem: "composer"
    directory: "/"
    schedule:
      interval: "weekly"
    open-pull-requests-limit: 10

  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
YAML;

        $this->files->put("{$basePath}/.github/dependabot.yml", $dependabot);

        // FUNDING.yml
        $funding = <<<YAML
github: fadymondy
YAML;

        $this->files->put("{$basePath}/.github/FUNDING.yml", $funding);

        // SECURITY.md
        $security = <<<MD
# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability within {$studlyName}, please send an email to security@laravilt.com. All security vulnerabilities will be promptly addressed.

Please do not publicly disclose the issue until it has been addressed by the team.
MD;

        $this->files->put("{$basePath}/.github/SECURITY.md", $security);

        // Bug report template
        $bugTemplate = <<<YAML
name: Bug Report
description: Report a bug
title: "[Bug]: "
labels: ["bug"]
body:
  - type: markdown
    attributes:
      value: |
        Thanks for taking the time to fill out this bug report!
  - type: textarea
    id: description
    attributes:
      label: Description
      description: A clear and concise description of the bug
    validations:
      required: true
  - type: textarea
    id: steps
    attributes:
      label: Steps to Reproduce
      description: Steps to reproduce the behavior
      placeholder: |
        1. Go to '...'
        2. Click on '....'
        3. See error
    validations:
      required: true
  - type: textarea
    id: expected
    attributes:
      label: Expected Behavior
      description: What you expected to happen
    validations:
      required: true
  - type: textarea
    id: actual
    attributes:
      label: Actual Behavior
      description: What actually happened
    validations:
      required: true
  - type: input
    id: version
    attributes:
      label: Package Version
      description: What version of the package are you using?
    validations:
      required: true
  - type: input
    id: php-version
    attributes:
      label: PHP Version
      description: What version of PHP are you using?
    validations:
      required: true
  - type: input
    id: laravel-version
    attributes:
      label: Laravel Version
      description: What version of Laravel are you using?
    validations:
      required: true
YAML;

        $this->files->put("{$basePath}/.github/ISSUE_TEMPLATE/bug.yml", $bugTemplate);

        // Config for issue templates
        $issueConfig = <<<YAML
blank_issues_enabled: false
contact_links:
  - name: Ask a question
    url: https://github.com/{$vendorLower}/{$kebabName}/discussions/new?category=q-a
    about: Ask the community for help
  - name: Request a feature
    url: https://github.com/{$vendorLower}/{$kebabName}/discussions/new?category=ideas
    about: Share ideas for new features
YAML;

        $this->files->put("{$basePath}/.github/ISSUE_TEMPLATE/config.yml", $issueConfig);

        $this->info('Generated GitHub configuration files.');
    }

    protected function generateTestingConfig(
        string $basePath,
        string $vendorLower,
        string $kebabName
    ): void {
        // phpunit.xml
        $phpunit = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Test Suite">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
    <coverage>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
    </coverage>
</phpunit>
XML;

        $this->files->put("{$basePath}/phpunit.xml", $phpunit);

        // pint.json
        $pint = <<<JSON
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": false,
        "new_with_braces": {
            "anonymous_class": false,
            "named_class": false
        }
    }
}
JSON;

        $this->files->put("{$basePath}/pint.json", $pint);

        // phpstan.neon
        $phpstan = <<<NEON
includes:
    - phpstan-baseline.neon

parameters:
    level: 5
    paths:
        - src
    tmpDir: build/phpstan
    checkOctaneCompatibility: true
    checkModelProperties: true
NEON;

        $this->files->put("{$basePath}/phpstan.neon", $phpstan);

        // testbench.yaml
        $testbench = <<<YAML
providers:
  - Laravilt\\Plugins\\PluginsServiceProvider

workbench:
  start: '/'
  install: true
  welcome: false
YAML;

        $this->files->put("{$basePath}/testbench.yaml", $testbench);

        $this->info('Generated testing configuration files.');
    }

    protected function generateDocumentation(string $basePath, string $studlyName): void
    {
        // docs/index.md
        $docs = <<<MD
# {$studlyName} Documentation

Welcome to the {$studlyName} documentation.

## Installation

```bash
composer require laravilt/{$studlyName}
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="{$studlyName}-config"
```

## Usage

[Add usage documentation here]

## API Reference

[Add API reference here]

## Examples

[Add examples here]
MD;

        $this->files->put("{$basePath}/docs/index.md", $docs);

        // CODE_OF_CONDUCT.md
        $codeOfConduct = <<<MD
# Code of Conduct

## Our Pledge

We as members, contributors, and leaders pledge to make participation in our community a harassment-free experience for everyone.

## Our Standards

Examples of behavior that contributes to a positive environment:
- Being respectful of differing opinions
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

## Enforcement

Instances of abusive, harassing, or otherwise unacceptable behavior may be reported to the community leaders responsible for enforcement at conduct@laravilt.com.

## Attribution

This Code of Conduct is adapted from the Contributor Covenant, version 2.1.
MD;

        $this->files->put("{$basePath}/CODE_OF_CONDUCT.md", $codeOfConduct);

        $this->info('Generated documentation files.');
    }

    protected function generateChangelogAndLicense(string $basePath, string $studlyName): void
    {
        // CHANGELOG.md
        $changelog = <<<MD
# Changelog

All notable changes to `{$studlyName}` will be documented in this file.

## 1.0.0 - TBD

- Initial release
MD;

        $this->files->put("{$basePath}/CHANGELOG.md", $changelog);

        // LICENSE.md
        $license = <<<MD
# The MIT License (MIT)

Copyright (c) Laravilt <hello@laravilt.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
MD;

        $this->files->put("{$basePath}/LICENSE.md", $license);

        // SECURITY.md (root level)
        $security = <<<MD
# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability within {$studlyName}, please send an email to security@laravilt.com.

All security vulnerabilities will be promptly addressed.

Please do not publicly disclose the issue until it has been addressed by the team.
MD;

        $this->files->put("{$basePath}/SECURITY.md", $security);

        $this->info('Generated CHANGELOG, LICENSE, and SECURITY files.');
    }
}

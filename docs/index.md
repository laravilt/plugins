# Laravilt Plugins Documentation

Complete plugin system with generator, management, and FilamentPHP v4 compatibility for Laravilt.ipsum

## Table of Contents

1. [Getting Started](getting-started.md)
2. [Architecture](architecture.md)
3. [Plugin Generation](plugin-generation.md)
4. [Component Generators](component-generators.md)
5. [Factory Pattern](factory-pattern.md)
6. [Features System](features-system.md)
7. [MCP Server Integration](mcp-server.md)
8. [API Reference](api-reference.md)

## Overview

Laravilt Plugins is a comprehensive plugin management system that provides:

- **Plugin Generator**: Create complete Laravel packages with service providers, configuration, and FilamentPHP integration
- **Component Generators**: Generate migrations, models, controllers, commands, and more within plugins
- **Factory Pattern Architecture**: Extensible feature system for plugin generation
- **MCP Server**: AI agent integration for plugin management
- **GitHub Integration**: Automated workflows, issue templates, and sponsorship
- **Professional Assets**: Cover images, documentation, and coding standards

## Quick Start

```bash
# Generate a new plugin
php artisan laravilt:plugin BlogExtensions

# Generate components within a plugin
php artisan laravilt:make blog-extensions model Post
php artisan laravilt:make blog-extensions controller PostController
php artisan laravilt:make blog-extensions migration CreatePostsTable

# Install MCP server for AI agents
php artisan laravilt:install-mcp
```

## Key Features

### 🎨 Plugin Generator
- Complete package structure
- Service provider with auto-discovery
- Configuration files
- Optional migrations, views, routes, assets

### 🧩 Component Generators
13 component types:
- Migration, Model, Controller, Command
- Job, Event, Listener, Notification
- Seeder, Factory, Test
- Language, Route

### 🏭 Factory Pattern
- Extensible architecture
- Priority-based execution
- Feature registration system
- Stub-based generation

### 🤖 MCP Server
- AI agent integration
- Plugin management tools
- Component generation
- Structure inspection

### 🎨 Professional Assets
- Cover images (1200x630px)
- Dark theme with branding
- README integration
- Social media ready

### ⚙️ GitHub Integration
- Automated workflows
- Issue templates
- Dependabot configuration
- FUNDING.yml support

## System Requirements

- PHP 8.3+
- Laravel 12+
- FilamentPHP v4 (for plugin features)
- Composer 2+

## Installation

```bash
composer require laravilt/plugins
```

The service provider is auto-discovered and will register automatically.

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag=laravilt-plugins-config
```

## Next Steps

- [Getting Started Guide](getting-started.md) - First steps with plugin generation
- [Architecture Overview](architecture.md) - Understanding the system design
- [Component Generators](component-generators.md) - Detailed component generation guide
- [MCP Server](mcp-server.md) - AI agent integration

## Support

- GitHub Issues: github.com/laravilt/plugins
- Documentation: docs.laravilt.com
- Discord: discord.laravilt.com

# MCP Server Integration

The Laravilt Plugins package includes a built-in MCP (Model Context Protocol) server that allows AI agents to interact with the plugin system.

## Installation

Install the MCP server configuration:

```bash
php artisan laravilt:install-mcp
```

This command will:

1. **Publish `routes/ai.php`** (if it doesn't exist) - Laravel MCP routes file
2. **Register the server** in `routes/ai.php`:
   ```php
   use Laravel\Mcp\Facades\Mcp;
   use Laravilt\Plugins\Mcp\LaraviltPluginsServer;

   Mcp::local('laravilt-plugins', LaraviltPluginsServer::class);
   ```
3. **Update `.mcp.json`** for AI clients:
   ```json
   {
     "mcpServers": {
       "laravilt-plugins": {
         "command": "php",
         "args": [
           "artisan",
           "mcp:serve",
           "laravilt-plugins"
         ]
       }
     }
   }
   ```

After installation, restart your AI agent application (Claude Desktop, etc.) to load the new MCP server.

## Available Tools

### list-plugins
List all installed Laravilt plugins in the packages directory.

**Usage:**
```
list-plugins
```

**Output:**
```
Found 3 plugin(s):

📦 actions
   Package: laravilt/actions
   Version: 1.0.0
   Description: Actions plugin for Laravilt
   Path: /path/to/packages/laravilt/actions
...
```

### plugin-info
Get detailed information about a specific plugin including structure, features, and configuration.

**Arguments:**
- `plugin` (string): The plugin name in kebab-case

**Usage:**
```
plugin-info(plugin="blog-extensions")
```

**Output:**
- Package information (name, description, version)
- Directory structure
- Available features (migrations, models, views, etc.)

### generate-plugin
Generate a new Laravilt plugin with specified features.

**Arguments:**
- `name` (string, required): Plugin name in StudlyCase
- `description` (string, optional): Plugin description
- `migrations` (bool, default: false): Include database migrations
- `views` (bool, default: false): Include Blade views
- `webRoutes` (bool, default: false): Include web routes
- `apiRoutes` (bool, default: false): Include API routes
- `css` (bool, default: false): Include CSS assets
- `js` (bool, default: false): Include JavaScript assets
- `arts` (bool, default: true): Include arts folder with cover photo
- `github` (bool, default: true): Include GitHub workflows
- `phpstan` (bool, default: true): Include PHPStan configuration

**Usage:**
```
generate-plugin(
  name="BlogExtensions",
  description="Blog extensions for Laravilt",
  migrations=true,
  views=true,
  css=true
)
```

### generate-component
Generate a Laravel component within a plugin.

**Arguments:**
- `plugin` (string, required): Plugin name in kebab-case
- `type` (string, required): Component type
- `name` (string, required): Component name

**Component Types:**
- `migration` - Database migration
- `model` - Eloquent model
- `controller` - HTTP controller
- `command` - Artisan command
- `job` - Queueable job
- `event` - Event class
- `listener` - Event listener
- `notification` - Notification
- `seeder` - Database seeder
- `factory` - Model factory
- `test` - Feature test
- `lang` - Language file
- `route` - Route file

**Usage:**
```
generate-component(
  plugin="blog-extensions",
  type="model",
  name="Post"
)
```

### list-component-types
List all available component types that can be generated.

**Usage:**
```
list-component-types
```

### plugin-structure
Get the complete directory structure of a plugin.

**Arguments:**
- `plugin` (string): Plugin name in kebab-case

**Usage:**
```
plugin-structure(plugin="blog-extensions")
```

## AI Agent Examples

### Claude Desktop / Claude Code

```
You: "List all installed plugins"
Claude: [calls list-plugins tool]

You: "Create a new plugin called BlogExtensions with migrations and views"
Claude: [calls generate-plugin with appropriate parameters]

You: "Generate a Post model in the blog-extensions plugin"
Claude: [calls generate-component with plugin="blog-extensions", type="model", name="Post"]
```

### Use Cases

1. **Plugin Discovery**: AI agents can explore installed plugins
2. **Automated Generation**: Create plugins and components through natural language
3. **Structure Analysis**: Inspect plugin architecture and features
4. **Development Assistance**: Quick component generation during development

## Security

The MCP server runs with the same permissions as your Laravel application. Ensure:
- Proper file permissions on the packages directory
- Secure configuration of the MCP server
- Limited access to the MCP configuration file

## Troubleshooting

### Server Not Found

If the AI agent can't find the server:
1. Check `.mcp/config.json` exists
2. Verify the artisan path is correct
3. Restart the AI agent application

### Permission Errors

Ensure the packages directory is writable:
```bash
chmod -R 775 packages/laravilt
```

### Tool Execution Failures

Check Laravel logs for detailed error messages:
```bash
tail -f storage/logs/laravel.log
```

<?php

namespace Laravilt\Plugins\Mcp;

use Laravel\Mcp\Server;
use Laravilt\Plugins\Mcp\Tools\GenerateComponentTool;
use Laravilt\Plugins\Mcp\Tools\GeneratePluginTool;
use Laravilt\Plugins\Mcp\Tools\ListComponentTypesTool;
use Laravilt\Plugins\Mcp\Tools\ListPluginsTool;
use Laravilt\Plugins\Mcp\Tools\PluginInfoTool;
use Laravilt\Plugins\Mcp\Tools\PluginStructureTool;

class LaraviltPluginsServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravilt Plugins';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        This server provides plugin management capabilities for Laravilt projects.

        You can:
        - List all installed plugins
        - Get detailed information about specific plugins
        - Generate new plugins with various features
        - Generate components within plugins (models, controllers, migrations, etc.)
        - View plugin directory structures
        - List available component types

        All plugins are located in the packages/laravilt directory.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        ListPluginsTool::class,
        PluginInfoTool::class,
        GeneratePluginTool::class,
        GenerateComponentTool::class,
        ListComponentTypesTool::class,
        PluginStructureTool::class,
    ];
}

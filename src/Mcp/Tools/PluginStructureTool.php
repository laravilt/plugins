<?php

namespace Laravilt\Plugins\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\File;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class PluginStructureTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Get the complete directory structure of a plugin';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $plugin = $request->string('plugin');
        $pluginPath = base_path("packages/laravilt/{$plugin}");

        if (! File::isDirectory($pluginPath)) {
            return Response::text("Plugin '{$plugin}' not found at {$pluginPath}");
        }

        $structure = $this->getDirectoryTree($pluginPath);

        $output = "Plugin Structure: {$plugin}\n\n";
        $output .= $structure;

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'plugin' => $schema->string()
                ->description('Plugin name (kebab-case)')
                ->required(),
        ];
    }

    protected function getDirectoryTree(string $path, string $prefix = ''): string
    {
        $tree = '';
        $items = File::allFiles($path);

        foreach ($items as $item) {
            $relativePath = str_replace($path.'/', '', $item->getPathname());
            $tree .= $prefix.$relativePath."\n";
        }

        return $tree;
    }
}

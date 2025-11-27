<?php

use Laravilt\Plugins\Mcp\LaraviltPluginsServer;
use Laravilt\Plugins\Mcp\Tools\GenerateComponentTool;
use Laravilt\Plugins\Mcp\Tools\GeneratePluginTool;
use Laravilt\Plugins\Mcp\Tools\ListComponentTypesTool;
use Laravilt\Plugins\Mcp\Tools\ListPluginsTool;
use Laravilt\Plugins\Mcp\Tools\PluginInfoTool;
use Laravilt\Plugins\Mcp\Tools\PluginStructureTool;
use Laravilt\Plugins\Mcp\Tools\SearchDocsTool;

test('mcp server extends laravel mcp server', function () {
    expect(is_subclass_of(LaraviltPluginsServer::class, \Laravel\Mcp\Server::class))
        ->toBeTrue();
});

test('mcp server has correct name and version', function () {
    $reflection = new ReflectionClass(LaraviltPluginsServer::class);
    $name = $reflection->getProperty('name')->getDefaultValue();
    $version = $reflection->getProperty('version')->getDefaultValue();

    expect($name)->toBe('Laravilt Plugins');
    expect($version)->toBe('1.0.0');
});

test('mcp server has all 7 tools registered', function () {
    $reflection = new ReflectionClass(LaraviltPluginsServer::class);
    $tools = $reflection->getProperty('tools')->getDefaultValue();

    expect($tools)->toHaveCount(7);
    expect($tools)->toContain(ListPluginsTool::class);
    expect($tools)->toContain(PluginInfoTool::class);
    expect($tools)->toContain(GeneratePluginTool::class);
    expect($tools)->toContain(GenerateComponentTool::class);
    expect($tools)->toContain(ListComponentTypesTool::class);
    expect($tools)->toContain(PluginStructureTool::class);
    expect($tools)->toContain(SearchDocsTool::class);
});

test('all tool classes exist and extend tool base class', function () {
    $tools = [
        ListPluginsTool::class,
        ListComponentTypesTool::class,
        PluginInfoTool::class,
        PluginStructureTool::class,
        GenerateComponentTool::class,
        GeneratePluginTool::class,
        SearchDocsTool::class,
    ];

    foreach ($tools as $tool) {
        expect(class_exists($tool))->toBeTrue("Tool class {$tool} should exist");
        expect(is_subclass_of($tool, \Laravel\Mcp\Server\Tool::class))
            ->toBeTrue("Tool {$tool} should extend Laravel MCP Tool class");
    }
});

test('list component types tool has correct description', function () {
    $reflection = new ReflectionClass(ListComponentTypesTool::class);
    $description = $reflection->getProperty('description')->getDefaultValue();

    expect($description)->toContain('available component types');
});

test('all tools have handle method', function () {
    $tools = [
        ListPluginsTool::class,
        ListComponentTypesTool::class,
        PluginInfoTool::class,
        PluginStructureTool::class,
        GenerateComponentTool::class,
        GeneratePluginTool::class,
        SearchDocsTool::class,
    ];

    foreach ($tools as $toolClass) {
        $reflection = new ReflectionClass($toolClass);
        expect($reflection->hasMethod('handle'))
            ->toBeTrue("Tool {$toolClass} should have handle method");
    }
});

test('all tools have description property', function () {
    $tools = [
        ListPluginsTool::class,
        ListComponentTypesTool::class,
        PluginInfoTool::class,
        PluginStructureTool::class,
        GenerateComponentTool::class,
        GeneratePluginTool::class,
        SearchDocsTool::class,
    ];

    foreach ($tools as $toolClass) {
        $reflection = new ReflectionClass($toolClass);
        expect($reflection->hasProperty('description'))
            ->toBeTrue("Tool {$toolClass} should have description property");

        $description = $reflection->getProperty('description')->getDefaultValue();
        expect($description)->not->toBeEmpty("Tool {$toolClass} description should not be empty");
    }
});

test('mcp server has instructions for llm', function () {
    $reflection = new ReflectionClass(LaraviltPluginsServer::class);
    $instructions = $reflection->getProperty('instructions')->getDefaultValue();

    expect($instructions)->toContain('plugin management');
    expect($instructions)->toContain('packages/laravilt');
});

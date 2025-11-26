<?php

use Laravilt\Plugins\Mcp\LaraviltPluginsServer;
use Laravilt\Plugins\Mcp\Tools\GenerateComponentTool;
use Laravilt\Plugins\Mcp\Tools\ListComponentTypesTool;
use Laravilt\Plugins\Mcp\Tools\ListPluginsTool;
use Laravilt\Plugins\Mcp\Tools\PluginInfoTool;
use Laravilt\Plugins\Mcp\Tools\PluginStructureTool;

test('list-plugins tool works', function () {
    $response = LaraviltPluginsServer::tool(ListPluginsTool::class);

    expect($response)
        ->assertOk()
        ->assertSee('Found');
});

test('list-component-types tool works', function () {
    $response = LaraviltPluginsServer::tool(ListComponentTypesTool::class);

    expect($response)
        ->assertOk()
        ->assertSee('Available Component Types')
        ->assertSee('migration')
        ->assertSee('model')
        ->assertSee('controller');
});

test('plugin-info tool works with valid plugin', function () {
    $response = LaraviltPluginsServer::tool(PluginInfoTool::class, [
        'plugin' => 'plugins',
    ]);

    expect($response)
        ->assertOk()
        ->assertSee('Plugin: plugins');
});

test('plugin-info tool handles non-existent plugin', function () {
    $response = LaraviltPluginsServer::tool(PluginInfoTool::class, [
        'plugin' => 'non-existent-plugin',
    ]);

    expect($response)
        ->assertOk()
        ->assertSee('not found');
});

test('plugin-structure tool works with valid plugin', function () {
    $response = LaraviltPluginsServer::tool(PluginStructureTool::class, [
        'plugin' => 'plugins',
    ]);

    expect($response)
        ->assertOk()
        ->assertSee('Plugin Structure: plugins');
});

test('generate-component tool validates component type', function () {
    $response = LaraviltPluginsServer::tool(GenerateComponentTool::class, [
        'plugin' => 'plugins',
        'type' => 'invalid-type',
        'name' => 'TestComponent',
    ]);

    expect($response)
        ->assertOk()
        ->assertSee('Invalid component type');
});

test('mcp server extends laravel mcp server', function () {
    expect(is_subclass_of(LaraviltPluginsServer::class, \Laravel\Mcp\Server::class))
        ->toBeTrue();
});

test('all tool classes exist and extend tool', function () {
    $tools = [
        ListPluginsTool::class,
        ListComponentTypesTool::class,
        PluginInfoTool::class,
        PluginStructureTool::class,
        GenerateComponentTool::class,
    ];

    foreach ($tools as $tool) {
        expect(class_exists($tool))->toBeTrue();
        expect(is_subclass_of($tool, \Laravel\Mcp\Server\Tool::class))->toBeTrue();
    }
});

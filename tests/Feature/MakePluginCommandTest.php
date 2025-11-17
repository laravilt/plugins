<?php

use Illuminate\Support\Facades\File;

it('can generate a plugin with default options', function () {
    $pluginPath = base_path('packages/laravilt/test-plugin');

    // Clean up if exists
    if (File::exists($pluginPath)) {
        File::deleteDirectory($pluginPath);
    }

    $this->artisan('laravilt:plugin', [
        'name' => 'TestPlugin',
        '--vendor' => 'laravilt',
        '--no-interaction' => true,
    ])
        ->assertSuccessful();

    expect($pluginPath)->toBeDirectory();
    expect($pluginPath.'/composer.json')->toBeFile();
    expect($pluginPath.'/src/TestPluginPlugin.php')->toBeFile();
    expect($pluginPath.'/.github/workflows/tests.yml')->toBeFile();
    expect($pluginPath.'/phpunit.xml')->toBeFile();
    expect($pluginPath.'/pint.json')->toBeFile();

    // Clean up
    File::deleteDirectory($pluginPath);
});

it('creates config file with laravilt prefix', function () {
    $pluginPath = base_path('packages/laravilt/test-plugin');

    // Clean up if exists
    if (File::exists($pluginPath)) {
        File::deleteDirectory($pluginPath);
    }

    $this->artisan('laravilt:plugin', [
        'name' => 'TestPlugin',
        '--vendor' => 'laravilt',
        '--no-interaction' => true,
    ]);

    expect($pluginPath.'/config/laravilt-test-plugin.php')->toBeFile();

    $config = file_get_contents($pluginPath.'/config/laravilt-test-plugin.php');
    expect($config)->toContain('LARAVILT_TEST_PLUGIN_ENABLED');

    // Clean up
    File::deleteDirectory($pluginPath);
});

it('can skip components generation', function () {
    $pluginPath = base_path('packages/laravilt/test-plugin');

    // Clean up if exists
    if (File::exists($pluginPath)) {
        File::deleteDirectory($pluginPath);
    }

    $this->artisan('laravilt:plugin', [
        'name' => 'TestPlugin',
        '--vendor' => 'laravilt',
        '--no-components' => true,
        '--no-interaction' => true,
    ]);

    expect($pluginPath.'/src/Components')->toBeDirectory();
    expect($pluginPath.'/src/Components/CustomInput.php')->not->toBeFile();

    // Clean up
    File::deleteDirectory($pluginPath);
});

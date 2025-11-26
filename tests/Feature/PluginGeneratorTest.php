<?php

use Illuminate\Filesystem\Filesystem;
use Laravilt\Plugins\Services\PluginFeatureFactory;
use Laravilt\Plugins\Services\PluginGenerator;

beforeEach(function () {
    $this->files = Mockery::mock(Filesystem::class);
    $this->factory = Mockery::mock(PluginFeatureFactory::class);
    $this->generator = new PluginGenerator($this->files, $this->factory);
});

afterEach(function () {
    Mockery::close();
});

test('can create directory structure', function () {
    $basePath = '/path/to/plugin';
    $config = ['test' => true];
    $directories = ['src', 'tests', 'config'];

    $this->factory->shouldReceive('getDirectories')
        ->once()
        ->with($config)
        ->andReturn($directories);

    $this->files->shouldReceive('makeDirectory')
        ->once()
        ->with("{$basePath}/src", 0755, true, true);

    $this->files->shouldReceive('makeDirectory')
        ->once()
        ->with("{$basePath}/tests", 0755, true, true);

    $this->files->shouldReceive('makeDirectory')
        ->once()
        ->with("{$basePath}/config", 0755, true, true);

    $this->generator->createDirectoryStructure($basePath, $config);
});

test('can generate all files', function () {
    $config = ['test' => true];

    $this->factory->shouldReceive('generateAll')
        ->once()
        ->with($config);

    $this->generator->generateAllFiles($config);
});

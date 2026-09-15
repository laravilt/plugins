<?php

use Illuminate\Filesystem\Filesystem;
use Laravilt\Plugins\Features\JsFeature;
use Laravilt\Plugins\Services\Generation\StubProcessor;

beforeEach(function () {
    $this->files = new Filesystem;
    $this->basePath = sys_get_temp_dir().'/laravilt-js-feature-'.uniqid();
    $this->feature = new JsFeature(new StubProcessor($this->files));
    $this->config = [
        'base_path' => $this->basePath,
        'studly_name' => 'Acme',
        'kebab_name' => 'acme',
        'generate_js' => true,
    ];
});

afterEach(function () {
    $this->files->deleteDirectory($this->basePath);
});

it('generates the React entry, package.json and vite plugin', function () {
    $config = [...$this->config, 'frontend' => 'react'];

    $this->feature->generate($config);

    $package = json_decode($this->files->get($this->basePath.'/package.json'), true);
    $vite = $this->files->get($this->basePath.'/vite.plugin.js');

    expect($this->feature->getDirectories($config))->toBe(['resources/react', 'dist'])
        ->and($this->files->exists($this->basePath.'/resources/react/app.ts'))->toBeTrue()
        ->and($this->files->exists($this->basePath.'/resources/js/app.js'))->toBeFalse()
        ->and($package['name'])->toBe('acme')
        ->and($package['dependencies'])->toHaveKey('react')
        ->and($vite)->toContain('AcmePlugin')
        ->and($vite)->toContain("resolve(pluginPath, 'resources/react/app.ts')")
        ->and($vite)->toContain('fileURLToPath(import.meta.url)')
        ->and($vite)->not->toContain('resolve(__dirname)');
});

it('generates the Vue entry when the frontend is vue', function () {
    $config = [...$this->config, 'frontend' => 'vue'];

    $this->feature->generate($config);

    $package = json_decode($this->files->get($this->basePath.'/package.json'), true);

    expect($this->feature->getDirectories($config))->toBe(['resources/js', 'dist'])
        ->and($this->files->exists($this->basePath.'/resources/js/app.js'))->toBeTrue()
        ->and($package['name'])->toBe('acme')
        ->and($package['dependencies'])->toHaveKey('vue');
});

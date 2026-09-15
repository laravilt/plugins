<?php

use Illuminate\Filesystem\Filesystem;
use Laravilt\Plugins\Features\InstallCommandFeature;
use Laravilt\Plugins\Features\PluginClassFeature;
use Laravilt\Plugins\Services\Generation\StubProcessor;

beforeEach(function () {
    $this->files = new Filesystem;
    $this->basePath = sys_get_temp_dir().'/laravilt-stubs-'.uniqid();
    $this->processor = new StubProcessor($this->files);
    $this->config = [
        'base_path' => $this->basePath,
        'namespace' => 'Acme\\Blog',
        'studly_name' => 'Blog',
        'kebab_name' => 'blog',
        'vendor_lower' => 'acme',
        'config_name' => 'laravilt-blog',
        'author' => 'Jane',
    ];
});

afterEach(function () {
    $this->files->deleteDirectory($this->basePath);
});

it('generates an install command with every placeholder replaced and Process imported', function () {
    (new InstallCommandFeature($this->processor))->generate([...$this->config, 'generate_js' => true]);

    $command = $this->files->get($this->basePath.'/src/Commands/InstallBlogCommand.php');

    expect($command)->not->toContain('{{')
        ->and($command)->toContain("'--tag' => 'laravilt-blog-config'")
        ->and($command)->toContain('Installing Blog plugin...')
        ->and($command)->toContain('Process::path(')
        ->and($command)->toContain('use Illuminate\\Support\\Facades\\Process;');
});

it('generates a plugin class compatible with the Laravilt PluginProvider', function () {
    (new PluginClassFeature($this->processor))->generate($this->config);

    $plugin = $this->files->get($this->basePath.'/src/BlogPlugin.php');

    expect($plugin)->not->toContain('{{')
        ->and($plugin)->not->toContain('Filament')
        ->and($plugin)->toContain('use Laravilt\\Panel\\Panel;')
        ->and($plugin)->toContain('class BlogPlugin extends PluginProvider');
});

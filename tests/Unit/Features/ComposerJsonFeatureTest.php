<?php

use Laravilt\Plugins\Features\ComposerJsonFeature;
use Laravilt\Plugins\Services\Generation\StubProcessor;

beforeEach(function () {
    $this->processor = Mockery::mock(StubProcessor::class);
    $this->feature = new ComposerJsonFeature($this->processor);
});

afterEach(function () {
    Mockery::close();
});

test('has correct name', function () {
    expect($this->feature->getName())->toBe('composer-json');
});

test('should always generate', function () {
    expect($this->feature->shouldGenerate([]))->toBeTrue()
        ->and($this->feature->shouldGenerate(['any' => 'config']))->toBeTrue();
});

test('has high priority', function () {
    expect($this->feature->getPriority())->toBe(1);
});

test('generates composer json file with correct replacements', function () {
    $config = [
        'base_path' => '/path/to/plugin',
        'vendor_lower' => 'acme',
        'kebab_name' => 'blog-plugin',
        'studly_name' => 'BlogPlugin',
        'author' => 'John Doe',
        'email' => 'john@example.com',
        'license' => 'MIT',
        'namespace' => 'Acme\\BlogPlugin',
    ];

    $this->processor->shouldReceive('generateFile')
        ->once()
        ->with(
            '/path/to/plugin/composer.json',
            'composer.json',
            Mockery::on(function ($replacements) {
                return $replacements['vendor'] === 'acme'
                    && $replacements['package'] === 'blog-plugin'
                    && $replacements['author'] === 'John Doe'
                    && $replacements['email'] === 'john@example.com'
                    && $replacements['license'] === 'MIT'
                    && $replacements['namespace'] === 'Acme\\\\BlogPlugin'
                    && $replacements['class'] === 'BlogPluginPlugin'
                    && $replacements['service_provider'] === 'BlogPluginServiceProvider';
            })
        );

    $this->feature->generate($config);
});

test('uses plugin description from config if provided', function () {
    $config = [
        'base_path' => '/path/to/plugin',
        'vendor_lower' => 'acme',
        'kebab_name' => 'blog-plugin',
        'studly_name' => 'BlogPlugin',
        'author' => 'John Doe',
        'email' => 'john@example.com',
        'license' => 'MIT',
        'namespace' => 'Acme\\BlogPlugin',
        'plugin_description' => 'A custom description',
    ];

    $this->processor->shouldReceive('generateFile')
        ->once()
        ->with(
            Mockery::any(),
            Mockery::any(),
            Mockery::on(fn ($replacements) => $replacements['description'] === 'A custom description')
        );

    $this->feature->generate($config);
});

test('uses default description if not provided', function () {
    $config = [
        'base_path' => '/path/to/plugin',
        'vendor_lower' => 'acme',
        'kebab_name' => 'blog-plugin',
        'studly_name' => 'BlogPlugin',
        'author' => 'John Doe',
        'email' => 'john@example.com',
        'license' => 'MIT',
        'namespace' => 'Acme\\BlogPlugin',
    ];

    $this->processor->shouldReceive('generateFile')
        ->once()
        ->with(
            Mockery::any(),
            Mockery::any(),
            Mockery::on(fn ($replacements) => $replacements['description'] === 'BlogPlugin plugin for Laravilt')
        );

    $this->feature->generate($config);
});

test('uses author email from author_email config if provided', function () {
    $config = [
        'base_path' => '/path/to/plugin',
        'vendor_lower' => 'acme',
        'kebab_name' => 'blog-plugin',
        'studly_name' => 'BlogPlugin',
        'author' => 'John Doe',
        'email' => 'default@example.com',
        'author_email' => 'custom@example.com',
        'license' => 'MIT',
        'namespace' => 'Acme\\BlogPlugin',
    ];

    $this->processor->shouldReceive('generateFile')
        ->once()
        ->with(
            Mockery::any(),
            Mockery::any(),
            Mockery::on(fn ($replacements) => $replacements['email'] === 'custom@example.com')
        );

    $this->feature->generate($config);
});

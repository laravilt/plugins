<?php

use Illuminate\Filesystem\Filesystem;
use Laravilt\Plugins\Services\Generation\StubProcessor;

beforeEach(function () {
    $this->files = Mockery::mock(Filesystem::class);
    $this->processor = new StubProcessor($this->files);
});

afterEach(function () {
    Mockery::close();
});

test('can process stub with replacements', function () {
    $stubContent = 'Hello {{ name }}, your email is {{ email }}';

    $this->files->shouldReceive('exists')
        ->once()
        ->with(Mockery::pattern('/Stubs\/test\.stub$/'))
        ->andReturn(true);

    $this->files->shouldReceive('get')
        ->once()
        ->andReturn($stubContent);

    $result = $this->processor->process('test', [
        'name' => 'John',
        'email' => 'john@example.com',
    ]);

    expect($result)->toBe('Hello John, your email is john@example.com');
});

test('throws exception when stub file not found', function () {
    $this->files->shouldReceive('exists')
        ->once()
        ->andReturn(false);

    $this->processor->process('nonexistent', []);
})->throws(RuntimeException::class, 'Stub file not found');

test('can generate file from stub', function () {
    $stubContent = 'class {{ class }} {}';
    $expectedContent = 'class TestClass {}';

    $this->files->shouldReceive('exists')
        ->once()
        ->andReturn(true);

    $this->files->shouldReceive('get')
        ->once()
        ->andReturn($stubContent);

    $this->files->shouldReceive('ensureDirectoryExists')
        ->once()
        ->with('/path/to');

    $this->files->shouldReceive('put')
        ->once()
        ->with('/path/to/TestClass.php', $expectedContent);

    $this->processor->generateFile('/path/to/TestClass.php', 'test', [
        'class' => 'TestClass',
    ]);
});

test('handles multiple replacements in single stub', function () {
    $stubContent = '{{ namespace }}\\{{ class }} extends {{ parent }}';

    $this->files->shouldReceive('exists')->andReturn(true);
    $this->files->shouldReceive('get')->andReturn($stubContent);

    $result = $this->processor->process('test', [
        'namespace' => 'App\\Models',
        'class' => 'User',
        'parent' => 'Model',
    ]);

    expect($result)->toBe('App\\Models\\User extends Model');
});

test('handles stub with no replacements', function () {
    $stubContent = 'No placeholders here';

    $this->files->shouldReceive('exists')->andReturn(true);
    $this->files->shouldReceive('get')->andReturn($stubContent);

    $result = $this->processor->process('test', []);

    expect($result)->toBe('No placeholders here');
});

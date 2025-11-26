<?php

use Laravilt\Plugins\Contracts\PluginFeatureInterface;
use Laravilt\Plugins\Services\PluginFeatureFactory;

beforeEach(function () {
    $this->factory = new PluginFeatureFactory;
});

test('can register a feature', function () {
    $feature = Mockery::mock(PluginFeatureInterface::class);
    $feature->shouldReceive('getName')->andReturn('test-feature');

    $this->factory->register($feature);

    expect($this->factory->getFeatures())->toHaveCount(1);
});

test('can register multiple features', function () {
    $feature1 = Mockery::mock(PluginFeatureInterface::class);
    $feature1->shouldReceive('getName')->andReturn('feature-1');
    $feature1->shouldReceive('getPriority')->andReturn(10);

    $feature2 = Mockery::mock(PluginFeatureInterface::class);
    $feature2->shouldReceive('getName')->andReturn('feature-2');
    $feature2->shouldReceive('getPriority')->andReturn(20);

    $this->factory->registerMany([$feature1, $feature2]);

    expect($this->factory->getFeatures())->toHaveCount(2);
});

test('sorts features by priority', function () {
    $lowPriority = Mockery::mock(PluginFeatureInterface::class);
    $lowPriority->shouldReceive('getName')->andReturn('low');
    $lowPriority->shouldReceive('getPriority')->andReturn(50);

    $highPriority = Mockery::mock(PluginFeatureInterface::class);
    $highPriority->shouldReceive('getName')->andReturn('high');
    $highPriority->shouldReceive('getPriority')->andReturn(10);

    $midPriority = Mockery::mock(PluginFeatureInterface::class);
    $midPriority->shouldReceive('getName')->andReturn('mid');
    $midPriority->shouldReceive('getPriority')->andReturn(30);

    $this->factory->register($lowPriority);
    $this->factory->register($highPriority);
    $this->factory->register($midPriority);

    $features = $this->factory->getFeatures();

    expect($features[0]->getName())->toBe('high')
        ->and($features[1]->getName())->toBe('mid')
        ->and($features[2]->getName())->toBe('low');
});

test('can get directories from all features', function () {
    $feature1 = Mockery::mock(PluginFeatureInterface::class);
    $feature1->shouldReceive('getName')->andReturn('feature-1');
    $feature1->shouldReceive('getPriority')->andReturn(10);
    $feature1->shouldReceive('shouldGenerate')->with(['test' => true])->andReturn(true);
    $feature1->shouldReceive('getDirectories')->with(['test' => true])->andReturn(['src', 'tests']);

    $feature2 = Mockery::mock(PluginFeatureInterface::class);
    $feature2->shouldReceive('getName')->andReturn('feature-2');
    $feature2->shouldReceive('getPriority')->andReturn(20);
    $feature2->shouldReceive('shouldGenerate')->with(['test' => true])->andReturn(true);
    $feature2->shouldReceive('getDirectories')->with(['test' => true])->andReturn(['config', 'tests']);

    $this->factory->registerMany([$feature1, $feature2]);

    $directories = $this->factory->getDirectories(['test' => true]);

    expect($directories)->toContain('src')
        ->and($directories)->toContain('tests')
        ->and($directories)->toContain('config')
        ->and($directories)->toHaveCount(3); // 'tests' should be unique
});

test('skips features that should not be generated', function () {
    $feature1 = Mockery::mock(PluginFeatureInterface::class);
    $feature1->shouldReceive('getName')->andReturn('feature-1');
    $feature1->shouldReceive('getPriority')->andReturn(10);
    $feature1->shouldReceive('shouldGenerate')->with(['enabled' => false])->andReturn(false);

    $feature2 = Mockery::mock(PluginFeatureInterface::class);
    $feature2->shouldReceive('getName')->andReturn('feature-2');
    $feature2->shouldReceive('getPriority')->andReturn(20);
    $feature2->shouldReceive('shouldGenerate')->with(['enabled' => false])->andReturn(true);
    $feature2->shouldReceive('getDirectories')->with(['enabled' => false])->andReturn(['config']);

    $this->factory->registerMany([$feature1, $feature2]);

    $directories = $this->factory->getDirectories(['enabled' => false]);

    expect($directories)->toHaveCount(1)
        ->and($directories)->toContain('config');
});

test('can generate all features', function () {
    $feature1 = Mockery::mock(PluginFeatureInterface::class);
    $feature1->shouldReceive('getName')->andReturn('feature-1');
    $feature1->shouldReceive('getPriority')->andReturn(10);
    $feature1->shouldReceive('shouldGenerate')->with(['test' => true])->andReturn(true);
    $feature1->shouldReceive('generate')->once()->with(['test' => true]);

    $feature2 = Mockery::mock(PluginFeatureInterface::class);
    $feature2->shouldReceive('getName')->andReturn('feature-2');
    $feature2->shouldReceive('getPriority')->andReturn(20);
    $feature2->shouldReceive('shouldGenerate')->with(['test' => true])->andReturn(false);

    $this->factory->registerMany([$feature1, $feature2]);

    $this->factory->generateAll(['test' => true]);

    // Expectations are verified by Mockery
});

test('can generate specific feature by name', function () {
    $feature = Mockery::mock(PluginFeatureInterface::class);
    $feature->shouldReceive('getName')->andReturn('test-feature');
    $feature->shouldReceive('shouldGenerate')->with(['config' => 'value'])->andReturn(true);
    $feature->shouldReceive('generate')->once()->with(['config' => 'value']);

    $this->factory->register($feature);

    $this->factory->generate('test-feature', ['config' => 'value']);
});

test('throws exception when generating non-existent feature', function () {
    $this->factory->generate('non-existent', []);
})->throws(InvalidArgumentException::class, "Feature 'non-existent' not registered");

test('does not generate feature when shouldGenerate returns false', function () {
    $feature = Mockery::mock(PluginFeatureInterface::class);
    $feature->shouldReceive('getName')->andReturn('test-feature');
    $feature->shouldReceive('shouldGenerate')->with(['enabled' => false])->andReturn(false);
    $feature->shouldReceive('generate')->never();

    $this->factory->register($feature);

    $this->factory->generate('test-feature', ['enabled' => false]);
});

afterEach(function () {
    Mockery::close();
});

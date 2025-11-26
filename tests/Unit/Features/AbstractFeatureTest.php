<?php

use Laravilt\Plugins\Features\AbstractFeature;
use Laravilt\Plugins\Services\Generation\StubProcessor;

beforeEach(function () {
    $this->processor = Mockery::mock(StubProcessor::class);
    $this->feature = new class($this->processor) extends AbstractFeature
    {
        public function getName(): string
        {
            return 'test-feature';
        }

        public function shouldGenerate(array $config): bool
        {
            return true;
        }

        public function generate(array $config): void
        {
            //
        }
    };
});

afterEach(function () {
    Mockery::close();
});

test('has default priority of 50', function () {
    expect($this->feature->getPriority())->toBe(50);
});

test('returns empty directories by default', function () {
    expect($this->feature->getDirectories([]))->toBe([]);
});

test('can be extended with custom priority', function () {
    $customFeature = new class($this->processor) extends AbstractFeature
    {
        public function getName(): string
        {
            return 'custom';
        }

        public function shouldGenerate(array $config): bool
        {
            return true;
        }

        public function generate(array $config): void
        {
            //
        }

        public function getPriority(): int
        {
            return 100;
        }
    };

    expect($customFeature->getPriority())->toBe(100);
});

test('can be extended with custom directories', function () {
    $customFeature = new class($this->processor) extends AbstractFeature
    {
        public function getName(): string
        {
            return 'custom';
        }

        public function shouldGenerate(array $config): bool
        {
            return true;
        }

        public function generate(array $config): void
        {
            //
        }

        public function getDirectories(array $config): array
        {
            return ['src', 'tests'];
        }
    };

    expect($customFeature->getDirectories([]))->toBe(['src', 'tests']);
});

<?php

use Illuminate\Contracts\Foundation\Application;
use Laravilt\Panel\Panel;
use Laravilt\Plugins\Contracts\Plugin;
use Laravilt\Plugins\PluginProvider;
use Laravilt\Plugins\Support\PluginManager;

beforeEach(function () {
    $this->manager = new PluginManager(Mockery::mock(Application::class));
});

afterEach(function () {
    Mockery::close();
});

it('registers, boots and describes a PluginProvider plugin', function () {
    $plugin = new class extends PluginProvider
    {
        protected static string $id = 'blog';

        protected static string $name = 'Blog';

        public function register(Panel $panel): void {}
    };

    $this->manager->register($plugin);
    $this->manager->bootAll();

    expect($this->manager->has('blog'))->toBeTrue()
        ->and($this->manager->getManifest()->toArray()['blog'])->toMatchArray([
            'id' => 'blog',
            'name' => 'Blog',
            'dependencies' => [],
        ]);
});

it('handles plugins that only implement the Plugin contract', function () {
    $plugin = new class implements Plugin
    {
        public function getId(): string
        {
            return 'bare';
        }

        public function register(Panel $panel): void {}

        public function boot(Panel $panel): void {}

        public function isEnabled(): bool
        {
            return true;
        }

        public static function make(): static
        {
            return new self;
        }
    };

    $this->manager->register($plugin);
    $this->manager->bootAll();

    expect($this->manager->getManifest()->toArray()['bare'])->toMatchArray([
        'id' => 'bare',
        'enabled' => true,
        'dependencies' => [],
    ]);
});

it('rejects a plugin whose dependencies are not satisfied', function () {
    $plugin = new class extends PluginProvider
    {
        protected static string $id = 'needy';

        public function getDependencies(): array
        {
            return ['missing'];
        }

        public function dependenciesSatisfied(): bool
        {
            return false;
        }

        public function register(Panel $panel): void {}
    };

    expect(fn () => $this->manager->register($plugin))
        ->toThrow(RuntimeException::class, "Plugin 'needy' dependencies not satisfied: missing");
});

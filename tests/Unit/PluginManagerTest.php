<?php

use Illuminate\Support\Collection;
use Laravilt\Plugins\Contracts\PluginManager as PluginManagerContract;
use Laravilt\Plugins\Support\PluginManager;

beforeEach(function () {
    $this->manager = app(PluginManagerContract::class);
});

it('can instantiate plugin manager', function () {
    expect($this->manager)->toBeInstanceOf(PluginManager::class);
});

it('returns empty collection initially', function () {
    expect($this->manager->all())->toBeInstanceOf(Collection::class);
    expect($this->manager->all())->toBeEmpty();
});

it('can check if plugin exists', function () {
    expect($this->manager->has('non-existent-plugin'))->toBeFalse();
});

it('returns enabled plugins collection', function () {
    $enabled = $this->manager->enabled();

    expect($enabled)->toBeInstanceOf(Collection::class);
});

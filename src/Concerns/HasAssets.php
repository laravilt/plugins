<?php

namespace Laravilt\Plugins\Concerns;

trait HasAssets
{
    protected array $assets = [];

    protected string $assetsPath = 'dist';

    public function assets(array $assets): static
    {
        $this->assets = $assets;

        return $this;
    }

    public function assetsPath(string $path): static
    {
        $this->assetsPath = $path;

        return $this;
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function getAssetsPath(): string
    {
        return $this->assetsPath;
    }

    public function publishAssets(): void
    {
        $pluginId = $this->getId();

        $this->publishes([
            __DIR__."/../../{$this->assetsPath}" => public_path("vendor/{$pluginId}"),
        ], "{$pluginId}-assets");
    }
}

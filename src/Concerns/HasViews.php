<?php

namespace Laravilt\Plugins\Concerns;

trait HasViews
{
    protected string $viewNamespace = '';

    public function viewNamespace(string $namespace): static
    {
        $this->viewNamespace = $namespace;

        return $this;
    }

    public function getViewNamespace(): string
    {
        return $this->viewNamespace ?: $this->getId();
    }

    public function loadViews(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            $this->getViewNamespace()
        );
    }

    public function publishViews(): void
    {
        $namespace = $this->getViewNamespace();

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path("views/vendor/{$namespace}"),
        ], "{$namespace}-views");
    }
}

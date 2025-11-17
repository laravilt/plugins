<?php

namespace Laravilt\Plugins\Concerns;

use Illuminate\Support\Str;

trait HasComponents
{
    protected array $components = [];

    public function components(array $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function registerComponents(): void
    {
        foreach ($this->components as $component) {
            $alias = Str::kebab(class_basename($component));

            $this->app->singleton("laravilt.component.{$alias}", $component);
        }
    }
}

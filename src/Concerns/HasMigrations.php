<?php

namespace Laravilt\Plugins\Concerns;

trait HasMigrations
{
    protected array $migrations = [];

    public function migrations(array $migrations): static
    {
        $this->migrations = $migrations;

        return $this;
    }

    public function getMigrations(): array
    {
        return $this->migrations;
    }

    public function loadMigrations(): void
    {
        if (empty($this->migrations)) {
            return;
        }

        $this->loadMigrationsFrom(
            __DIR__.'/../../database/migrations'
        );
    }
}

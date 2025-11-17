<?php

namespace Laravilt\Plugins\Concerns;

trait HasTranslations
{
    protected array $translationNamespaces = [];

    public function translations(array $namespaces): static
    {
        $this->translationNamespaces = $namespaces;

        return $this;
    }

    public function getTranslationNamespaces(): array
    {
        return $this->translationNamespaces;
    }

    public function loadTranslations(): void
    {
        $pluginId = $this->getId();

        $this->loadTranslationsFrom(
            __DIR__.'/../../resources/lang',
            $pluginId
        );
    }
}

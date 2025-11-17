<?php

namespace Laravilt\Plugins\Services\Generation;

use Illuminate\Filesystem\Filesystem;

/**
 * Processes stub files by replacing placeholders with actual values.
 *
 * This class is responsible for loading stub templates and performing
 * placeholder replacement to generate final file content.
 */
class StubProcessor
{
    public function __construct(public Filesystem $files) {}

    /**
     * Get stub path from stub name.
     */
    protected function getStubPath(string $name): string
    {
        return __DIR__.'/../../Stubs/'.$name.'.stub';
    }

    /**
     * Get processed stub content with replacements.
     *
     * @param  string  $stubName  Name of the stub file (without .stub extension)
     * @param  array  $replacements  Key-value pairs for placeholder replacement
     * @return string Processed stub content
     *
     * @throws \RuntimeException if stub file not found
     */
    public function process(string $stubName, array $replacements): string
    {
        $stubPath = $this->getStubPath($stubName);

        if (! $this->files->exists($stubPath)) {
            throw new \RuntimeException("Stub file not found: {$stubPath}");
        }

        $stub = $this->files->get($stubPath);

        foreach ($replacements as $key => $value) {
            $stub = str_replace("{{ {$key} }}", $value, $stub);
        }

        return $stub;
    }

    /**
     * Generate file from stub with replacements.
     *
     * @param  string  $path  Full path where file should be created
     * @param  string  $stubName  Name of the stub file
     * @param  array  $replacements  Placeholder replacements
     */
    public function generateFile(string $path, string $stubName, array $replacements): void
    {
        $content = $this->process($stubName, $replacements);
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }
}

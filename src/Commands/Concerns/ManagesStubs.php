<?php

namespace Laravilt\Plugins\Commands\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ManagesStubs
{
    /**
     * Get stub content.
     */
    protected function getStub(string $name): string
    {
        $stubPath = __DIR__.'/../../Stubs/'.$name.'.stub';

        if (! file_exists($stubPath)) {
            throw new \RuntimeException("Stub file not found: {$stubPath}");
        }

        return file_get_contents($stubPath);
    }

    /**
     * Replace placeholders in stub content.
     */
    protected function replaceInStub(string $stub, array $replacements): string
    {
        foreach ($replacements as $key => $value) {
            $stub = str_replace("{{ {$key} }}", $value, $stub);
        }

        return $stub;
    }

    /**
     * Generate file from stub.
     */
    protected function generateFromStub(string $stubName, string $outputPath, array $replacements): void
    {
        $stub = $this->getStub($stubName);
        $content = $this->replaceInStub($stub, $replacements);

        $this->files->ensureDirectoryExists(dirname($outputPath));
        $this->files->put($outputPath, $content);
    }

    /**
     * Generate file from stub with more options.
     *
     * @param  string  $from  Stub file path
     * @param  string  $to  Output file path
     * @param  array  $replacements  Key-value pairs for replacements
     * @param  array  $directory  Directories to create
     * @param  bool  $append  Whether to append instead of overwrite
     */
    protected function generateStubs(string $from, string $to, array $replacements, array $directory = [], bool $append = false): void
    {
        if (File::exists($from)) {
            $stubValue = File::get($from);

            $convertStubToText = Str::of($stubValue);

            foreach ($replacements as $key => $replacement) {
                $convertStubToText = $convertStubToText->replace('{{ '.$key.' }}', $replacement);
            }

            foreach ($directory as $dir) {
                if (! File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
            }

            if (File::exists($to) && ! $append) {
                File::delete($to);
            }

            if ($append) {
                $content = File::get($to);
                if (! str_contains($content, $convertStubToText)) {
                    File::append($to, $convertStubToText);
                }
            } else {
                File::put($to, $convertStubToText);
            }
        }
    }
}

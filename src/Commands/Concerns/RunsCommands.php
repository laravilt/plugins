<?php

namespace Laravilt\Plugins\Commands\Concerns;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait RunsCommands
{
    /**
     * Get the path to the appropriate PHP binary.
     */
    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    /**
     * Run a PHP command.
     */
    public function phpCommand(array $commands, ?bool $useOutput = false): void
    {
        (new Process(array_merge([$this->phpBinary()], $commands), base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($useOutput) {
                if ($useOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run a Yarn command.
     */
    public function yarnCommand(array $commands, ?bool $withOutput = false): void
    {
        (new Process(array_merge(['yarn'], $commands), base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run an npm command.
     */
    public function npmCommand(array $commands, ?bool $withOutput = false): void
    {
        (new Process(array_merge(['npm'], $commands), base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run an artisan command.
     */
    public function artisanCommand(array $command, ?bool $withOutput = false): void
    {
        $this->phpCommand(array_merge(['artisan'], $command), $withOutput);
    }

    /**
     * Installs the given Composer Packages into the application.
     */
    protected function requireComposerPackages(mixed $packages, ?bool $withOutput = false): void
    {
        $command = array_merge(
            ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Install the given Composer Packages as "dev" dependencies.
     */
    protected function requireComposerDevPackages(mixed $packages, ?bool $withOutput = false): void
    {
        $command = array_merge(
            ['composer', 'require', '--dev'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run composer install in a specific directory.
     */
    protected function composerInstall(string $directory, ?bool $withOutput = false): void
    {
        (new Process(['composer', 'install'], $directory, ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run npm install in a specific directory.
     */
    protected function npmInstall(string $directory, ?bool $withOutput = false): void
    {
        (new Process(['npm', 'install'], $directory))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }

    /**
     * Run npm build in a specific directory.
     */
    protected function npmBuild(string $directory, ?bool $withOutput = false): void
    {
        (new Process(['npm', 'run', 'build'], $directory))
            ->setTimeout(null)
            ->run(function ($type, $output) use ($withOutput) {
                if ($withOutput) {
                    $this->output->write($output);
                }
            });
    }
}

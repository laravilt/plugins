<?php

namespace Laravilt\Plugins\Services\Generation;

/**
 * Generates GitHub-specific files including workflows and templates.
 *
 * Creates GitHub Actions workflows for CI/CD and issue/PR templates
 * for better community collaboration.
 */
class GitHubFilesGenerator
{
    public function __construct(protected StubProcessor $processor) {}

    /**
     * Generate GitHub Actions workflow files.
     *
     * Creates workflows for automated testing and code style fixing.
     */
    public function generateWorkflows(array $config): void
    {
        $this->generateTestsWorkflow($config['base_path']);
        $this->generateCodeStyleWorkflow($config['base_path']);
    }

    /**
     * Generate GitHub templates and configuration files.
     *
     * Creates FUNDING.yml and CONTRIBUTING.md for community engagement.
     */
    public function generateTemplates(array $config): void
    {
        $this->processor->files->put(
            $config['base_path'].'/.github/FUNDING.yml',
            "github: fadymondy\n"
        );

        $this->processor->files->put(
            $config['base_path'].'/.github/CONTRIBUTING.md',
            "# Contributing\n\nThank you for considering contributing!\n"
        );
    }

    /**
     * Generate tests workflow for GitHub Actions.
     */
    protected function generateTestsWorkflow(string $basePath): void
    {
        $content = <<<'YAML'
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: [8.3, 8.4]

    name: PHP ${{ matrix.php }} - Laravel 12.x

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite
          coverage: none

      - name: Install dependencies
        run: |
          composer require "laravel/framework:^12.0" "orchestra/testbench:^10.0" --no-interaction --no-update
          composer update --prefer-stable --prefer-dist --no-interaction

      - name: Execute tests
        run: vendor/bin/pest
YAML;
        $this->processor->files->put($basePath.'/.github/workflows/tests.yml', $content);
    }

    /**
     * Generate code style workflow for GitHub Actions.
     */
    protected function generateCodeStyleWorkflow(string $basePath): void
    {
        $content = <<<'YAML'
name: Fix Code Style
on: [push]
jobs:
  style:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: aglipanci/laravel-pint-action@2.4
YAML;
        $this->processor->files->put($basePath.'/.github/workflows/fix-php-code-styling.yml', $content);
    }
}

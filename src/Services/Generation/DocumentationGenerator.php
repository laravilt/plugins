<?php

namespace Laravilt\Plugins\Services\Generation;

/**
 * Generates documentation files for the plugin.
 *
 * Creates CHANGELOG, LICENSE, SECURITY, and CODE_OF_CONDUCT files
 * following open source best practices.
 */
class DocumentationGenerator
{
    public function __construct(protected StubProcessor $processor) {}

    /**
     * Generate CHANGELOG.md file.
     *
     * Creates a changelog file following Keep a Changelog format.
     */
    public function generateChangelog(string $basePath): void
    {
        $content = "# Changelog\n\nAll notable changes will be documented in this file.\n\n## 1.0.0 - TBD\n\n- Initial release\n";
        $this->processor->files->put($basePath.'/CHANGELOG.md', $content);
    }

    /**
     * Generate LICENSE.md file.
     *
     * Creates an MIT License file with copyright information.
     */
    public function generateLicense(array $config): void
    {
        $year = date('Y');
        $content = "# The MIT License (MIT)\n\nCopyright (c) {$year} {$config['author']}\n\nPermission is hereby granted, free of charge...\n";
        $this->processor->files->put($config['base_path'].'/LICENSE.md', $content);
    }

    /**
     * Generate SECURITY.md file.
     *
     * Creates a security policy file with vulnerability reporting instructions.
     */
    public function generateSecurity(array $config): void
    {
        $content = "# Security Policy\n\nIf you discover a security vulnerability, please email {$config['email']}.\n";
        $this->processor->files->put($config['base_path'].'/SECURITY.md', $content);
    }

    /**
     * Generate CODE_OF_CONDUCT.md file.
     *
     * Creates a code of conduct for community participation.
     */
    public function generateCodeOfConduct(string $basePath): void
    {
        $content = "# Code of Conduct\n\nWe pledge to make participation in our community a harassment-free experience for everyone.\n";
        $this->processor->files->put($basePath.'/CODE_OF_CONDUCT.md', $content);
    }
}

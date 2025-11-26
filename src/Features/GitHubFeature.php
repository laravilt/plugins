<?php

namespace Laravilt\Plugins\Features;

/**
 * Generates GitHub files for the plugin.
 *
 * Creates workflows, issue templates, and dependabot configuration.
 */
class GitHubFeature extends AbstractFeature
{
    public function getName(): string
    {
        return 'github';
    }

    public function shouldGenerate(array $config): bool
    {
        return $config['generate_github_files'] ?? false;
    }

    public function getPriority(): int
    {
        return 90; // Documentation file
    }

    public function getDirectories(array $config): array
    {
        return $this->shouldGenerate($config)
            ? [
                '.github',
                '.github/workflows',
                '.github/ISSUE_TEMPLATE',
            ]
            : [];
    }

    public function generate(array $config): void
    {
        // Generate GitHub meta files
        $this->generateContributing($config);
        $this->generateFunding($config);
        $this->generateSecurity($config);

        // Generate workflows
        $this->generateWorkflows($config);

        // Generate issue templates
        $this->generateIssueTemplates($config);

        // Generate dependabot configuration
        $this->generateDependabot($config);
    }

    protected function generateContributing(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/.github/CONTRIBUTING.md',
            'github/CONTRIBUTING.md',
            [
                'plugin_name' => $config['studly_name'],
            ]
        );
    }

    protected function generateFunding(array $config): void
    {
        if (! empty($config['github_sponsor'])) {
            $this->processor->generateFile(
                $config['base_path'].'/.github/FUNDING.yml',
                'github/FUNDING.yml',
                [
                    'github_username' => $config['github_sponsor'],
                ]
            );
        }
    }

    protected function generateSecurity(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/.github/SECURITY.md',
            'github/SECURITY.md',
            [
                'plugin_name' => $config['studly_name'],
                'author_email' => $config['author_email'],
            ]
        );
    }

    protected function generateWorkflows(array $config): void
    {
        // Generate tests workflow
        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/tests.yml',
            'github/workflows/tests.yml',
            [
                'plugin_name' => $config['studly_name'],
            ]
        );

        // Generate PHP code styling workflow
        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/fix-php-code-styling.yml',
            'github/workflows/fix-php-code-styling.yml',
            [
                'plugin_name' => $config['studly_name'],
            ]
        );

        // Generate dependabot auto-merge workflow
        $this->processor->generateFile(
            $config['base_path'].'/.github/workflows/dependabot-auto-merge.yml',
            'github/workflows/dependabot-auto-merge.yml',
            []
        );
    }

    protected function generateIssueTemplates(array $config): void
    {
        // Generate bug report template (GitHub issue form)
        $this->processor->generateFile(
            $config['base_path'].'/.github/ISSUE_TEMPLATE/bug.yml',
            'github/ISSUE_TEMPLATE/bug.yml',
            []
        );

        // Generate issue template config
        $this->processor->generateFile(
            $config['base_path'].'/.github/ISSUE_TEMPLATE/config.yml',
            'github/ISSUE_TEMPLATE/config.yml',
            [
                'repository_url' => $config['repository_url'] ?? 'https://github.com/'.$config['vendor'].'/'.$config['kebab_name'],
            ]
        );
    }

    protected function generateDependabot(array $config): void
    {
        $this->processor->generateFile(
            $config['base_path'].'/.github/dependabot.yml',
            'github/dependabot.yml',
            []
        );
    }
}

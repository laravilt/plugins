<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMcpServerCommand extends Command
{
    protected $signature = 'laravilt:install-mcp';

    protected $description = 'Install the Laravilt Plugins MCP server configuration';

    public function handle(): int
    {
        $this->info('Installing Laravilt Plugins MCP server...');
        $this->newLine();

        // Step 1: Publish routes/ai.php if it doesn't exist
        $this->publishAiRoutes();

        // Step 2: Register MCP server in routes/ai.php
        $this->registerMcpServer();

        // Step 3: Create .mcp/config.json for MCP clients
        $this->createMcpConfig();

        $this->newLine();
        $this->components->info('✅ Laravilt Plugins MCP server installed successfully!');
        $this->newLine();

        $this->components->bulletList([
            'Server name: laravilt-plugins',
            'Server type: Local (Artisan command)',
            'Tools available: list-plugins, plugin-info, generate-plugin, generate-component, list-component-types, plugin-structure',
        ]);

        $this->newLine();
        $this->components->info('AI agents can now access plugin management features through the MCP server!');
        $this->newLine();

        $this->components->warn('Next steps:');
        $this->components->bulletList([
            'Restart your AI agent application (Claude Desktop, etc.)',
            'The laravilt-plugins server should now be available',
        ]);

        return self::SUCCESS;
    }

    protected function publishAiRoutes(): void
    {
        $aiRoutesPath = base_path('routes/ai.php');

        if (! File::exists($aiRoutesPath)) {
            $this->info('Publishing routes/ai.php...');
            $this->call('vendor:publish', ['--tag' => 'ai-routes']);
            $this->info('✓ Published routes/ai.php');
        } else {
            $this->info('✓ routes/ai.php already exists');
        }
    }

    protected function registerMcpServer(): void
    {
        $aiRoutesPath = base_path('routes/ai.php');

        if (! File::exists($aiRoutesPath)) {
            $this->error('✗ routes/ai.php not found. Cannot register MCP server.');

            return;
        }

        $content = File::get($aiRoutesPath);

        // Check if already registered
        if (str_contains($content, 'laravilt-plugins') || str_contains($content, 'LaraviltPluginsServer')) {
            $this->info('✓ MCP server already registered in routes/ai.php');

            return;
        }

        // Add the necessary use statements
        $useStatements = "use Laravilt\Plugins\Mcp\LaraviltPluginsServer;\n";

        // Check if Laravel\Mcp\Facades\Mcp is already imported
        if (! str_contains($content, 'use Laravel\Mcp\Facades\Mcp;')) {
            $useStatements = "use Laravel\Mcp\Facades\Mcp;\n".$useStatements;
        }

        // Find the position to insert use statements (after <?php and namespace if present)
        $lines = explode("\n", $content);
        $insertPosition = 0;

        foreach ($lines as $index => $line) {
            if (str_starts_with(trim($line), 'use ')) {
                $insertPosition = $index;
                break;
            }
            if (str_starts_with(trim($line), '<?php')) {
                $insertPosition = $index + 1;
            }
        }

        // Insert use statements
        if ($insertPosition > 0) {
            array_splice($lines, $insertPosition, 0, rtrim($useStatements));
            $content = implode("\n", $lines);
        } else {
            // If no use statements found, add after <?php
            $content = str_replace("<?php\n", "<?php\n\n".$useStatements, $content);
        }

        // Add the MCP server registration at the end of the file
        $serverRegistration = "\n// Laravilt Plugins MCP Server\nMcp::local('laravilt-plugins', LaraviltPluginsServer::class);\n";

        $content = rtrim($content).$serverRegistration;

        File::put($aiRoutesPath, $content);

        $this->info('✓ Registered MCP server in routes/ai.php');
    }

    protected function createMcpConfig(): void
    {
        $mcpConfigPath = base_path('.mcp.json');

        // Read existing config or create new one
        $config = [];
        if (File::exists($mcpConfigPath)) {
            $config = json_decode(File::get($mcpConfigPath), true) ?? [];
        }

        // Add laravilt-plugins server
        if (! isset($config['mcpServers'])) {
            $config['mcpServers'] = [];
        }

        $config['mcpServers']['laravilt-plugins'] = [
            'command' => 'php',
            'args' => [
                'artisan',
                'mcp:serve',
                'laravilt-plugins',
            ],
        ];

        // Write config
        File::put(
            $mcpConfigPath,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('✓ Updated .mcp.json');
    }
}

<?php

namespace Laravilt\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Generate Laravel components within a plugin.
 *
 * This command allows generating various Laravel components
 * (migrations, models, controllers, etc.) within a plugin context.
 */
class MakeComponentCommand extends Command
{
    protected $signature = 'laravilt:make
                            {plugin? : The plugin name}
                            {type? : The component type}
                            {name? : The component name}';

    protected $description = 'Generate Laravel components within a Laravilt plugin';

    protected array $componentTypes = [
        'resource' => 'Panel Resource',
        'migration' => 'Database Migration',
        'model' => 'Eloquent Model',
        'controller' => 'Controller',
        'command' => 'Artisan Command',
        'job' => 'Job',
        'event' => 'Event',
        'listener' => 'Event Listener',
        'notification' => 'Notification',
        'seeder' => 'Database Seeder',
        'factory' => 'Model Factory',
        'test' => 'Test',
        'lang' => 'Language File',
        'route' => 'Route',
    ];

    public function handle(): int
    {
        // Get plugin name
        $plugin = $this->argument('plugin') ?: text(
            label: 'Plugin name',
            placeholder: 'e.g., BlogExtensions',
            required: true
        );

        // Get component type
        $type = $this->argument('type') ?: select(
            label: 'What component do you want to generate?',
            options: $this->componentTypes,
            hint: 'Use arrow keys to select'
        );

        // Get component name
        $name = $this->argument('name') ?: text(
            label: ucfirst($this->componentTypes[$type]).' name',
            placeholder: $this->getPlaceholder($type),
            required: true
        );

        // Generate the component
        return $this->generateComponent($plugin, $type, $name);
    }

    protected function generateComponent(string $plugin, string $type, string $name): int
    {
        $pluginPath = $this->getPluginPath($plugin);

        if (! is_dir($pluginPath)) {
            $this->error("Plugin '{$plugin}' not found at {$pluginPath}");

            return self::FAILURE;
        }

        // Get plugin namespace from composer.json
        $namespace = $this->getPluginNamespace($pluginPath);
        if (! $namespace) {
            $this->error('Could not determine plugin namespace from composer.json');

            return self::FAILURE;
        }

        $this->info("Generating {$this->componentTypes[$type]}: {$name}");
        $this->info("Plugin: {$plugin}");
        $this->info("Path: {$pluginPath}");
        $this->info("Namespace: {$namespace}");

        // Call the appropriate generator method
        $method = 'generate'.Str::studly($type);

        if (! method_exists($this, $method)) {
            $this->error("Component type '{$type}' is not yet supported");

            return self::FAILURE;
        }

        return $this->{$method}($pluginPath, $namespace, $name);
    }

    protected function generateMigration(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating migration...');

        $table = Str::snake(Str::pluralStudly($name));
        $migrationName = 'create_'.$table.'_table';
        $className = Str::studly($migrationName);
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp.'_'.$migrationName.'.php';

        $migrationPath = $pluginPath.'/database/migrations';
        if (! is_dir($migrationPath)) {
            mkdir($migrationPath, 0755, true);
        }

        $stub = $this->getMigrationStub();
        $content = str_replace(
            ['{{ class }}', '{{ table }}'],
            [$className, $table],
            $stub
        );

        file_put_contents($migrationPath.'/'.$fileName, $content);

        $this->info('✅ Migration created successfully!');
        $this->line("Created: database/migrations/{$fileName}");

        return self::SUCCESS;
    }

    protected function generateModel(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating model...');

        $modelPath = $pluginPath.'/src/Models';
        if (! is_dir($modelPath)) {
            mkdir($modelPath, 0755, true);
        }

        $stub = $this->getModelStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Models', $name],
            $stub
        );

        file_put_contents($modelPath.'/'.$name.'.php', $content);

        $this->info('✅ Model created successfully!');
        $this->line("Created: src/Models/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateController(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating controller...');

        $controllerPath = $pluginPath.'/src/Http/Controllers';
        if (! is_dir($controllerPath)) {
            mkdir($controllerPath, 0755, true);
        }

        $stub = $this->getControllerStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Http\\Controllers', $name],
            $stub
        );

        file_put_contents($controllerPath.'/'.$name.'.php', $content);

        $this->info('✅ Controller created successfully!');
        $this->line("Created: src/Http/Controllers/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateCommand(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating command...');

        $commandPath = $pluginPath.'/src/Commands';
        if (! is_dir($commandPath)) {
            mkdir($commandPath, 0755, true);
        }

        $signature = Str::kebab(str_replace('Command', '', $name));

        $stub = $this->getCommandStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ signature }}'],
            [$namespace.'\\Commands', $name, $signature],
            $stub
        );

        file_put_contents($commandPath.'/'.$name.'.php', $content);

        $this->info('✅ Command created successfully!');
        $this->line("Created: src/Commands/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateJob(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating job...');

        $jobPath = $pluginPath.'/src/Jobs';
        if (! is_dir($jobPath)) {
            mkdir($jobPath, 0755, true);
        }

        $stub = $this->getJobStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Jobs', $name],
            $stub
        );

        file_put_contents($jobPath.'/'.$name.'.php', $content);

        $this->info('✅ Job created successfully!');
        $this->line("Created: src/Jobs/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateEvent(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating event...');

        $eventPath = $pluginPath.'/src/Events';
        if (! is_dir($eventPath)) {
            mkdir($eventPath, 0755, true);
        }

        $stub = $this->getEventStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Events', $name],
            $stub
        );

        file_put_contents($eventPath.'/'.$name.'.php', $content);

        $this->info('✅ Event created successfully!');
        $this->line("Created: src/Events/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateListener(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating listener...');

        $listenerPath = $pluginPath.'/src/Listeners';
        if (! is_dir($listenerPath)) {
            mkdir($listenerPath, 0755, true);
        }

        $stub = $this->getListenerStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Listeners', $name],
            $stub
        );

        file_put_contents($listenerPath.'/'.$name.'.php', $content);

        $this->info('✅ Listener created successfully!');
        $this->line("Created: src/Listeners/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateNotification(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating notification...');

        $notificationPath = $pluginPath.'/src/Notifications';
        if (! is_dir($notificationPath)) {
            mkdir($notificationPath, 0755, true);
        }

        $stub = $this->getNotificationStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Notifications', $name],
            $stub
        );

        file_put_contents($notificationPath.'/'.$name.'.php', $content);

        $this->info('✅ Notification created successfully!');
        $this->line("Created: src/Notifications/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateSeeder(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating seeder...');

        $seederPath = $pluginPath.'/database/seeders';
        if (! is_dir($seederPath)) {
            mkdir($seederPath, 0755, true);
        }

        $stub = $this->getSeederStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Database\\Seeders', $name],
            $stub
        );

        file_put_contents($seederPath.'/'.$name.'.php', $content);

        $this->info('✅ Seeder created successfully!');
        $this->line("Created: database/seeders/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateFactory(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating factory...');

        $factoryPath = $pluginPath.'/database/factories';
        if (! is_dir($factoryPath)) {
            mkdir($factoryPath, 0755, true);
        }

        $modelName = str_replace('Factory', '', $name);

        $stub = $this->getFactoryStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelNamespace }}'],
            [$namespace.'\\Database\\Factories', $name, $modelName, $namespace.'\\Models\\'.$modelName],
            $stub
        );

        file_put_contents($factoryPath.'/'.$name.'.php', $content);

        $this->info('✅ Factory created successfully!');
        $this->line("Created: database/factories/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateTest(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating test...');

        $testPath = $pluginPath.'/tests/Feature';
        if (! is_dir($testPath)) {
            mkdir($testPath, 0755, true);
        }

        $stub = $this->getTestStub();
        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace.'\\Tests\\Feature', $name],
            $stub
        );

        file_put_contents($testPath.'/'.$name.'.php', $content);

        $this->info('✅ Test created successfully!');
        $this->line("Created: tests/Feature/{$name}.php");

        return self::SUCCESS;
    }

    protected function generateLang(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating language file...');

        $langPath = $pluginPath.'/resources/lang/'.$name;
        if (! is_dir($langPath)) {
            mkdir($langPath, 0755, true);
        }

        $messagesFile = $langPath.'/messages.php';
        $content = <<<'PHP'
<?php

return [
    // Add your translations here
];

PHP;

        file_put_contents($messagesFile, $content);

        $this->info('✅ Language file created successfully!');
        $this->line("Created: resources/lang/{$name}/messages.php");

        return self::SUCCESS;
    }

    protected function generateRoute(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating route file...');

        $routePath = $pluginPath.'/routes';
        if (! is_dir($routePath)) {
            mkdir($routePath, 0755, true);
        }

        $routeFile = $routePath.'/'.$name.'.php';
        $content = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

// Add your routes here

PHP;

        file_put_contents($routeFile, $content);

        $this->info('✅ Route file created successfully!');
        $this->line("Created: routes/{$name}.php");

        return self::SUCCESS;
    }

    protected function getPluginPath(string $plugin): string
    {
        $kebabName = Str::kebab($plugin);

        return base_path("packages/laravilt/{$kebabName}");
    }

    protected function getPluginNamespace(string $pluginPath): ?string
    {
        $composerJsonPath = $pluginPath.'/composer.json';

        if (! file_exists($composerJsonPath)) {
            return null;
        }

        $composerData = json_decode(file_get_contents($composerJsonPath), true);

        if (! isset($composerData['autoload']['psr-4'])) {
            return null;
        }

        // Get the first PSR-4 namespace
        $namespaces = array_keys($composerData['autoload']['psr-4']);

        return rtrim($namespaces[0], '\\');
    }

    protected function getPlaceholder(string $type): string
    {
        return match ($type) {
            'resource' => 'e.g., User (singular)',
            'migration' => 'e.g., CreatePostsTable',
            'model' => 'e.g., Post',
            'controller' => 'e.g., PostController',
            'command' => 'e.g., ProcessPostsCommand',
            'job' => 'e.g., ProcessPost',
            'event' => 'e.g., PostCreated',
            'listener' => 'e.g., SendPostNotification',
            'notification' => 'e.g., PostPublished',
            'seeder' => 'e.g., PostSeeder',
            'factory' => 'e.g., PostFactory',
            'test' => 'e.g., PostTest',
            'lang' => 'e.g., en',
            'route' => 'e.g., posts',
            default => 'e.g., Example',
        };
    }

    protected function generateResource(string $pluginPath, string $namespace, string $name): int
    {
        $this->info('Creating resource...');

        $singular = Str::singular($name);
        $plural = Str::plural($name);
        $studlySingular = Str::studly($singular);
        $studlyPlural = Str::studly($plural);

        // Create directory structure
        $resourcePath = $pluginPath.'/src/Resources/'.$studlyPlural;
        $schemasPath = $resourcePath.'/Schemas';
        $tablesPath = $resourcePath.'/Tables';
        $pagesPath = $resourcePath.'/Pages';

        foreach ([$resourcePath, $schemasPath, $tablesPath, $pagesPath] as $path) {
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Generate resource files
        $this->generateResourceFile($resourcePath, $namespace, $studlySingular, $studlyPlural);
        $this->generateFormSchema($schemasPath, $namespace, $studlySingular, $studlyPlural);
        $this->generateInfolistSchema($schemasPath, $namespace, $studlySingular, $studlyPlural);
        $this->generateTableFile($tablesPath, $namespace, $studlySingular, $studlyPlural);
        $this->generatePages($pagesPath, $namespace, $studlySingular, $studlyPlural);

        $this->info('✅ Resource created successfully!');
        $this->line("Created: src/Resources/{$studlyPlural}/");
        $this->line("  - {$studlySingular}Resource.php");
        $this->line("  - Schemas/{$studlySingular}Form.php");
        $this->line("  - Schemas/{$studlySingular}Infolist.php");
        $this->line("  - Tables/{$studlyPlural}Table.php");
        $this->line("  - Pages/List{$studlyPlural}.php");
        $this->line("  - Pages/Create{$studlySingular}.php");
        $this->line("  - Pages/View{$studlySingular}.php");
        $this->line("  - Pages/Edit{$studlySingular}.php");

        return self::SUCCESS;
    }

    protected function generateResourceFile(string $resourcePath, string $namespace, string $singular, string $plural): void
    {
        $content = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural};

use Laravilt\Panel\Resources\Resource;
use Laravilt\Schemas\Schema;
use Laravilt\Tables\Table;
use {$namespace}\\Resources\\{$plural}\\Pages\\Create{$singular};
use {$namespace}\\Resources\\{$plural}\\Pages\\Edit{$singular};
use {$namespace}\\Resources\\{$plural}\\Pages\\List{$plural};
use {$namespace}\\Resources\\{$plural}\\Pages\\View{$singular};
use {$namespace}\\Resources\\{$plural}\\Schemas\\{$singular}Form;
use {$namespace}\\Resources\\{$plural}\\Schemas\\{$singular}Infolist;
use {$namespace}\\Resources\\{$plural}\\Tables\\{$plural}Table;

class {$singular}Resource extends Resource
{
    protected static ?string \$recordTitleAttribute = 'name';

    protected static ?string \$navigationIcon = 'layers';

    public static function getModel(): string
    {
        return \\App\\Models\\{$singular}::class;
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationBadge(): ?string
    {
        \$count = static::getModel()::count();

        return \$count > 0 ? (string) \$count : null;
    }

    public static function form(Schema \$schema): Schema
    {
        return {$singular}Form::make(\$schema);
    }

    public static function infolist(Schema \$schema): Schema
    {
        return {$singular}Infolist::make(\$schema);
    }

    public static function table(Table \$table): Table
    {
        return {$plural}Table::make(\$table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => List{$plural}::route('/'),
            'create' => Create{$singular}::route('/create'),
            'view' => View{$singular}::route('/{record}'),
            'edit' => Edit{$singular}::route('/{record}/edit'),
        ];
    }
}

PHP;
        file_put_contents($resourcePath."/{$singular}Resource.php", $content);
    }

    protected function generateFormSchema(string $schemasPath, string $namespace, string $singular, string $plural): void
    {
        $content = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Schemas;

use Laravilt\Forms\Components\TextInput;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class {$singular}Form
{
    public static function make(Schema \$schema): Schema
    {
        return \$schema
            ->schema([
                Section::make('{$singular} Information')
                    ->icon('information-circle')
                    ->description('Fill in the {$singular} details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ])->columns(1);
    }
}

PHP;
        file_put_contents($schemasPath."/{$singular}Form.php", $content);
    }

    protected function generateInfolistSchema(string $schemasPath, string $namespace, string $singular, string $plural): void
    {
        $content = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Schemas;

use Laravilt\Infolists\Entries\TextEntry;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class {$singular}Infolist
{
    public static function make(Schema \$schema): Schema
    {
        return \$schema
            ->schema([
                Section::make('{$singular} Information')
                    ->icon('information-circle')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}

PHP;
        file_put_contents($schemasPath."/{$singular}Infolist.php", $content);
    }

    protected function generateTableFile(string $tablesPath, string $namespace, string $singular, string $plural): void
    {
        $content = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Tables;

use Laravilt\Actions\DeleteAction;
use Laravilt\Actions\DeleteBulkAction;
use Laravilt\Actions\EditAction;
use Laravilt\Actions\ViewAction;
use Laravilt\Tables\Columns\TextColumn;
use Laravilt\Tables\Enums\PaginationMode;
use Laravilt\Tables\Table;

class {$plural}Table
{
    public static function make(Table \$table): Table
    {
        return \$table
            ->extremePaginationLinks()
            ->paginationPageOptions([10, 25, 50, 100])
            ->deferLoading()
            ->paginationMode(PaginationMode::Simple)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}

PHP;
        file_put_contents($tablesPath."/{$plural}Table.php", $content);
    }

    protected function generatePages(string $pagesPath, string $namespace, string $singular, string $plural): void
    {
        // ListRecords page
        $listContent = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Pages;

use Laravilt\Panel\Pages\ListRecords;
use {$namespace}\\Resources\\{$plural}\\{$singular}Resource;

class List{$plural} extends ListRecords
{
    protected static ?string \$resource = {$singular}Resource::class;
}

PHP;
        file_put_contents($pagesPath."/List{$plural}.php", $listContent);

        // CreateRecord page
        $createContent = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Pages;

use Laravilt\Panel\Pages\CreateRecord;
use {$namespace}\\Resources\\{$plural}\\{$singular}Resource;

class Create{$singular} extends CreateRecord
{
    protected static ?string \$resource = {$singular}Resource::class;
}

PHP;
        file_put_contents($pagesPath."/Create{$singular}.php", $createContent);

        // ViewRecord page
        $viewContent = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Pages;

use Laravilt\Panel\Pages\ViewRecord;
use {$namespace}\\Resources\\{$plural}\\{$singular}Resource;

class View{$singular} extends ViewRecord
{
    protected static ?string \$resource = {$singular}Resource::class;
}

PHP;
        file_put_contents($pagesPath."/View{$singular}.php", $viewContent);

        // EditRecord page
        $editContent = <<<PHP
<?php

namespace {$namespace}\\Resources\\{$plural}\\Pages;

use Laravilt\Panel\Pages\EditRecord;
use {$namespace}\\Resources\\{$plural}\\{$singular}Resource;

class Edit{$singular} extends EditRecord
{
    protected static ?string \$resource = {$singular}Resource::class;
}

PHP;
        file_put_contents($pagesPath."/Edit{$singular}.php", $editContent);
    }

    protected function getMigrationStub(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{{ table }}', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{{ table }}');
    }
};

PHP;
    }

    protected function getModelStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Database\Eloquent\Model;

class {{ class }} extends Model
{
    protected $fillable = [];
}

PHP;
    }

    protected function getControllerStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class {{ class }} extends Controller
{
    public function index()
    {
        //
    }
}

PHP;
    }

    protected function getCommandStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Console\Command;

class {{ class }} extends Command
{
    protected $signature = '{{ signature }}';

    protected $description = 'Command description';

    public function handle(): int
    {
        $this->info('Command executed successfully!');

        return self::SUCCESS;
    }
}

PHP;
    }

    protected function getJobStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {{ class }} implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        //
    }
}

PHP;
    }

    protected function getEventStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class {{ class }}
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}

PHP;
    }

    protected function getListenerStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class {{ class }} implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(object $event): void
    {
        //
    }
}

PHP;
    }

    protected function getNotificationStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class {{ class }} extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

PHP;
    }

    protected function getSeederStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Database\Seeder;

class {{ class }} extends Seeder
{
    public function run(): void
    {
        //
    }
}

PHP;
    }

    protected function getFactoryStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Database\Eloquent\Factories\Factory;
use {{ modelNamespace }};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\{{ modelNamespace }}>
 */
class {{ class }} extends Factory
{
    protected $model = {{ model }}::class;

    public function definition(): array
    {
        return [
            //
        ];
    }
}

PHP;
    }

    protected function getTestStub(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Tests\TestCase;

class {{ class }} extends TestCase
{
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
}

PHP;
    }
}

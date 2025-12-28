<?php

namespace ShreyaSarker\LaraCrud\Commands;

use Illuminate\Console\Command;
use ShreyaSarker\LaraCrud\Services\ControllerGeneratorService;
use ShreyaSarker\LaraCrud\Services\MigrationGeneratorService;
use ShreyaSarker\LaraCrud\Services\ModelGeneratorService;
use ShreyaSarker\LaraCrud\Services\RequestGeneratorService;
use ShreyaSarker\LaraCrud\Services\RouteGeneratorService;
use ShreyaSarker\LaraCrud\Services\ViewsGeneratorService;
use ShreyaSarker\LaraCrud\Utils\TypeUtil;

class MakeCrudCommand extends Command
{
    /**
     * Examples:
     * php artisan make:crud Post --interactive
     * php artisan make:crud Post --interactive --only=model,migration
     * php artisan make:crud Post --interactive --skip=views,routes
     * php artisan make:crud Post --interactive --dry-run
     */
    protected $signature = 'make:crud
        {name : The resource name (e.g., Post)}
        {--interactive : Create fields using a guided wizard}
        {--only= : Generate only: model,migration,request,controller,views,routes}
        {--skip= : Skip: model,migration,request,controller,views,routes}
        {--api : API-style generation (skips views by default)}
        {--stack=bootstrap : View stack: bootstrap|tailwind}
        {--force : Overwrite existing files}
        {--dry-run : Show plan only, don’t write files}';

    protected $description = 'Create CRUD operations with a single command';

    protected ModelGeneratorService $modelGeneratorService;
    protected RequestGeneratorService $requestGeneratorService;
    protected ControllerGeneratorService $controllerGeneratorService;
    protected RouteGeneratorService $routeGeneratorService;
    protected ViewsGeneratorService $viewsGeneratorService;
    protected MigrationGeneratorService $migrationGeneratorService;

    private const PARTS = ['migration', 'model', 'request', 'controller', 'views', 'routes'];

    public function __construct(
        ModelGeneratorService $modelGeneratorService,
        RequestGeneratorService $requestGeneratorService,
        ControllerGeneratorService $controllerGeneratorService,
        RouteGeneratorService $routeGeneratorService,
        ViewsGeneratorService $viewsGeneratorService,
        MigrationGeneratorService $migrationGeneratorService
    ) {
        parent::__construct();

        $this->modelGeneratorService = $modelGeneratorService;
        $this->requestGeneratorService = $requestGeneratorService;
        $this->controllerGeneratorService = $controllerGeneratorService;
        $this->routeGeneratorService = $routeGeneratorService;
        $this->viewsGeneratorService = $viewsGeneratorService;
        $this->migrationGeneratorService = $migrationGeneratorService;
    }

    public function handle(): int
    {
        $name = (string) $this->argument('name');

        $parts = $this->resolvePartsToGenerate();

        $options = [
            'force' => (bool) $this->option('force'),
            'api' => (bool) $this->option('api'),
            'stack' => (string) ($this->option('stack') ?? 'bootstrap'),
            'dry_run' => (bool) $this->option('dry-run'),
        ];

        // Fields: interactive only (legacy removed)
        if (! (bool) $this->option('interactive')) {
            $this->error('Please use --interactive to define fields.');
            return Command::FAILURE;
        }

        $fields = $this->interactiveFields();

        if (count($fields) === 0) {
            $this->warn('No fields added. Migration/Request/Views may be minimal.');
        }

        // If API mode, skip views by default
        if ($options['api']) {
            $parts = array_values(array_diff($parts, ['views']));
        }

        // --- Summary ---
        $this->info('Plan:');
        $this->line('- Resource: ' . $name);
        $this->line('- Parts: ' . implode(', ', $parts));
        $this->line('- Mode: ' . ($options['api'] ? 'api' : 'web'));
        $this->line('- Stack: ' . $options['stack']);
        $this->line('- Force overwrite: ' . ($options['force'] ? 'yes' : 'no'));
        $this->line('- Dry run: ' . ($options['dry_run'] ? 'yes' : 'no'));

        if (count($fields)) {
            $this->line('- Fields:');
            foreach ($fields as $f) {
                $rules = (string) ($f['validations'] ?? '');
                $nullable = str_contains($rules, 'nullable') ? 'nullable' : 'not-null';
                $this->line("  - {$f['name']} ({$f['type']}, {$nullable}) | rules: {$rules}");
            }
        } else {
            $this->line('- Fields: (none)');
        }

        $this->newLine();

        // Confirm before writing anything (skip confirm for dry-run)
        if (! $options['dry_run']) {
            $proceed = $this->confirm('Proceed to generate files?', true);
            if (! $proceed) {
                $this->info('No changes made. Generation canceled.');
                return Command::SUCCESS;
            }
        }

        // Generate (services will output "Dry run: ..." when dry_run=true)
        $this->callHandlers($name, $fields, $parts, $options);

        if ($options['dry_run']) {
            $this->comment('Dry run complete ✅ (no files were written)');
        } else {
            $this->info('Done ✅');
        }

        return Command::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function resolvePartsToGenerate(): array
    {
        $only = trim((string) ($this->option('only') ?? ''));
        $skip = trim((string) ($this->option('skip') ?? ''));

        $parts = self::PARTS;

        if ($only !== '') {
            $requested = $this->csvToList($only);
            $invalid = array_values(array_diff($requested, self::PARTS));
            if ($invalid) {
                $this->error('Invalid --only value(s): ' . implode(', ', $invalid));
                $this->line('Allowed: ' . implode(', ', self::PARTS));
                exit(Command::FAILURE);
            }
            $parts = $requested;
        }

        if ($skip !== '') {
            $toSkip = $this->csvToList($skip);
            $invalid = array_values(array_diff($toSkip, self::PARTS));
            if ($invalid) {
                $this->error('Invalid --skip value(s): ' . implode(', ', $invalid));
                $this->line('Allowed: ' . implode(', ', self::PARTS));
                exit(Command::FAILURE);
            }
            $parts = array_values(array_diff($parts, $toSkip));
        }

        if (count($parts) === 0) {
            $this->error('Nothing to generate. Your --only/--skip removed all parts.');
            exit(Command::FAILURE);
        }

        return $parts;
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<int, string>  $parts
     * @param  array<string, mixed>  $options
     */
    private function callHandlers(string $name, array $fields, array $parts, array $options): void
    {
        foreach ($parts as $part) {
            match ($part) {
                'migration' => $this->info($this->migrationGeneratorService->generate($name, $fields, $options)),
                'model' => $this->info($this->modelGeneratorService->generate($name, $options)),
                'request' => $this->info($this->requestGeneratorService->generate($name, $fields, $options)),
                'controller' => $this->info($this->controllerGeneratorService->generate($name, $options)),
                'views' => $this->info($this->viewsGeneratorService->generate($name, $fields, $options)),
                'routes' => $this->info($this->routeGeneratorService->generate($name, $options)),
                default => null,
            };
        }
    }

    /**
     * Interactive wizard (beginner-friendly)
     * Flow:
     * Field name -> Type -> Nullable -> Validation rules -> (Select options) -> Add another?
     *
     * @return array<int, array<string, mixed>>
     */
    private function interactiveFields(): array
    {
        $this->info('Interactive mode: let’s build your fields step-by-step.');

        $validationMap = TypeUtil::getValidationType();
        $sqlMap = TypeUtil::getSqlColumnType();

        $types = array_keys($validationMap);
        sort($types);

        $fields = [];

        while (true) {
            $this->newLine();

            $fieldName = trim((string) $this->ask('Field name (leave empty to finish)', ''));
            if ($fieldName === '') {
                break;
            }

            $type = $this->choice('Type', $types, 0);

            $nullable = $this->confirm('Nullable?', false);

            // default rules from TypeUtil
            $rules = (string) ($validationMap[$type] ?? '');

            // If nullable, replace "required" with "nullable" (cleaner than "nullable|required")
            if ($nullable) {
                if (str_contains($rules, 'required')) {
                    $rules = str_replace('required', 'nullable', $rules);
                } elseif (! str_contains($rules, 'nullable')) {
                    $rules = $rules ? 'nullable|' . $rules : 'nullable';
                }
            }

            $finalRules = trim((string) $this->ask('Validation rules?', $rules));

            $field = [
                'name' => $fieldName,
                'type' => $type,
                'validations' => $finalRules,
                'sqlColumn' => $sqlMap[$type] ?? 'string',
            ];

            if ($type === 'select') {
                $this->line('Add select options (key => label).');

                $options = [];
                while (true) {
                    $key = trim((string) $this->ask('Option key (blank to finish)', ''));
                    if ($key === '') {
                        break;
                    }
                    $label = trim((string) $this->ask('Option label', $key));
                    $options[$key] = $label;
                }

                $field['options'] = $options;
            }

            $fields[] = $field;

            $addMore = $this->confirm('Add another field?', true);
            if (! $addMore) {
                break;
            }
        }

        return $fields;
    }

    /**
     * @return array<int, string>
     */
    private function csvToList(string $csv): array
    {
        return array_values(array_filter(array_map(
            fn ($p) => trim($p),
            explode(',', $csv)
        )));
    }
}

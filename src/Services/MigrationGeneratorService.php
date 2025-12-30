<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class MigrationGeneratorService
{
    public function __construct(private Filesystem $files)
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $options  Supported: force(bool), dry_run(bool)
     */
    public function generate(string $name, array $fields, array $options = []): string
    {
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $sqlColumns = $this->getSqlColumns($fields);

        $naming = NameUtil::getNamingConvention($name);
        $tableName = $naming['table_name'];

        $migrationBaseName = 'create_' . $tableName . '_table';

        $migrationDir = PathUtil::getMigrationBasePath();

        $existing = $this->findExistingMigrationFiles($migrationDir, $migrationBaseName);

        if (! empty($existing) && ! $force) {
            $fileList = implode(', ', array_map('basename', $existing));
            return "Skipped migration: a migration for '{$tableName}' already exists ({$fileList}). Use --force to overwrite.";
        }

        $targetPath = (! empty($existing) && $force)
            ? end($existing)
            : $migrationDir . DIRECTORY_SEPARATOR . FileUtil::getFileName(now()->format('Y_m_d_His') . '_' . $migrationBaseName);

        $contents = $this->getStubContents(
            $this->getStubVariables($tableName, $sqlColumns)
        );

        if ($contents === null) {
            return "Error: Migration stub not found/readable at " . PathUtil::getMigrationStubPath();
        }

        if ($dryRun) {
            return "Dry run: would write migration to " . $targetPath;
        }

        if (! $this->files->exists($migrationDir)) {
            $this->files->makeDirectory($migrationDir, 0755, true);
        }

        $this->files->put($targetPath, $contents);

        return (! empty($existing) && $force)
            ? "Migration overwritten successfully: " . basename($targetPath)
            : "Migration created successfully: " . basename($targetPath);
    }

    private function getSqlColumns(array $fields): string
    {
        $columns = '';

        foreach ($fields as $field) {
            $sqlColumn = trim((string) ($field['sqlColumn'] ?? ''));
            $name = trim((string) ($field['name'] ?? ''));
            $validations = trim((string) ($field['validations'] ?? ''));

            if ($sqlColumn === '' || $name === '') {
                continue;
            }

            // Add ->nullable() if field has nullable validation
            $nullable = str_contains($validations, 'nullable') ? '->nullable()' : '';

            $columns .= str_repeat("\t", 3) . "\$table->{$sqlColumn}('{$name}'){$nullable};\n";
        }

        return FileUtil::cleanLastLineBreak($columns);
    }

    private function getStubContents(array $stubVariables = []): ?string
    {
        $stub = PathUtil::getMigrationStubPath();
        $contents = @file_get_contents($stub);

        if ($contents === false) {
            return null;
        }

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{' . $search . '}}', $replace, $contents);
        }

        return $contents;
    }

    private function getStubVariables(string $tableName, string $sqlColumns): array
    {
        return [
            'TABLE_NAME' => $tableName,
            'SQL_COLUMNS' => $sqlColumns,
        ];
    }

    private function findExistingMigrationFiles(string $migrationDir, string $migrationBaseName): array
    {
        if (! $this->files->exists($migrationDir)) {
            return [];
        }

        $all = $this->files->files($migrationDir);

        $matches = [];
        foreach ($all as $file) {
            $filename = $file->getFilename();
            if (Str::endsWith($filename, $migrationBaseName . '.php')) {
                $matches[] = $file->getPathname();
            }
        }

        sort($matches);
        return $matches;
    }
}
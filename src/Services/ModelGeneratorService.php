<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class ModelGeneratorService
{
    public function __construct(private Filesystem $files)
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $options Supported: force(bool), dry_run(bool)
     */
    public function generate(string $name, array $fields, array $options = []): string
    {
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $fillable = $this->getFillableFields($fields);
        $casts = $this->getCasts($fields);
        $hidden = $this->getHiddenFields($fields);

        $naming = NameUtil::getNamingConvention($name);
        $namespace = PathUtil::getModelNamespace();
        $className = $naming['singular_upper'];

        $dir  = PathUtil::getModelBasePath();
        $path = $dir . DIRECTORY_SEPARATOR . FileUtil::getFileName($className);

        $existedBefore = $this->files->exists($path);

        if ($existedBefore && ! $force) {
            return "Skipped model: " . basename($path) . " already exists. Use --force to overwrite.";
        }

        $contents = $this->getStubContents(
            $this->getStubVariables($namespace, $className, $fillable, $casts, $hidden)
        );

        if ($contents === null) {
            return "Error: Model stub not found/readable at " . PathUtil::getModelStubPath();
        }

        if ($dryRun) {
            return "Dry run: would write model to " . $path;
        }

        if (! $this->files->exists($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($path, $contents);

        return $existedBefore
            ? "Model overwritten successfully: " . basename($path)
            : "Model created successfully: " . basename($path);
    }

    /**
     * Generate fillable fields array
     */
    private function getFillableFields(array $fields): string
    {
        if (empty($fields)) {
            return str_repeat("\t", 2) . '// Add fillable fields here';
        }

        $fillable = '';
        
        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $type = (string) ($field['type'] ?? '');

            if ($name === '') {
                continue;
            }

            // Skip password fields from fillable (security best practice)
            // They should use separate methods with hashing
            if ($type === 'password') {
                continue;
            }

            $fillable .= str_repeat("\t", 2) . "'{$name}',\n";
        }

        if ($fillable === '') {
            return str_repeat("\t", 2) . '// Add fillable fields here';
        }

        return FileUtil::cleanLastLineBreak($fillable);
    }

    /**
     * Generate casts array based on field types
     */
    private function getCasts(array $fields): string
    {
        if (empty($fields)) {
            return str_repeat("\t", 2) . '// Add casts here';
        }

        $casts = '';
        $hasCasts = false;

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $type = (string) ($field['type'] ?? '');

            if ($name === '') {
                continue;
            }

            // Map field types to Laravel casts
            $cast = $this->getCastType($type);

            if ($cast !== null) {
                $casts .= str_repeat("\t", 2) . "'{$name}' => '{$cast}',\n";
                $hasCasts = true;
            }
        }

        if (!$hasCasts) {
            return str_repeat("\t", 2) . '// Add casts here';
        }

        return FileUtil::cleanLastLineBreak($casts);
    }

    /**
     * Get the appropriate cast type for a field type
     */
    private function getCastType(string $fieldType): ?string
    {
        return match ($fieldType) {
            'integer', 'bigint', 'mediumint', 'smallint', 'tinyint' => 'integer',
            'decimal', 'double', 'float', 'number' => 'decimal:2',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'datetime',
            'text', 'mediumtext', 'longtext' => 'string',
            default => null, // string, email, password, select don't need explicit casts
        };
    }

    /**
     * Generate hidden fields array (for sensitive data)
     */
    private function getHiddenFields(array $fields): string
    {
        if (empty($fields)) {
            return str_repeat("\t", 2) . '// Add hidden fields here (e.g., password, tokens)';
        }

        $hidden = '';
        $hasHidden = false;

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $type = (string) ($field['type'] ?? '');

            if ($name === '') {
                continue;
            }

            // Automatically hide password fields and common sensitive fields
            if ($type === 'password' || 
                in_array($name, ['password', 'remember_token', 'api_token', 'secret'])) {
                $hidden .= str_repeat("\t", 2) . "'{$name}',\n";
                $hasHidden = true;
            }
        }

        if (!$hasHidden) {
            return str_repeat("\t", 2) . '// Add hidden fields here (e.g., password, tokens)';
        }

        return FileUtil::cleanLastLineBreak($hidden);
    }

    private function getStubContents(array $stubVariables = []): ?string
    {
        $stub = PathUtil::getModelStubPath();
        $contents = @file_get_contents($stub);

        if ($contents === false) {
            return null;
        }

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{' . $search . '}}', $replace, $contents);
        }

        return $contents;
    }

    private function getStubVariables(
        string $namespace,
        string $className,
        string $fillable,
        string $casts,
        string $hidden
    ): array {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'FILLABLE_FIELDS' => $fillable,
            'CASTS' => $casts,
            'HIDDEN_FIELDS' => $hidden,
        ];
    }
}
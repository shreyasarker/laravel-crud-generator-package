<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class RequestGeneratorService
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

        $rules = $this->getRequestRules($fields);

        $naming = NameUtil::getNamingConvention($name);
        $namespace = PathUtil::getRequestNamespace();
        $className = $naming['singular_upper'];

        $dir  = PathUtil::getRequestBasePath();
        $path = $dir . DIRECTORY_SEPARATOR . FileUtil::getFileName($className . 'Request');

        $existedBefore = $this->files->exists($path);

        if ($existedBefore && ! $force) {
            return "Skipped request: " . basename($path) . " already exists. Use --force to overwrite.";
        }

        $contents = $this->getStubContents(
            $this->getStubVariables($namespace, $className, $rules)
        );

        if ($contents === null) {
            return "Error: Request stub not found/readable at " . PathUtil::getRequestStubPath();
        }

        if ($dryRun) {
            return "Dry run: would write request to " . $path;
        }

        if (! $this->files->exists($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($path, $contents);

        return $existedBefore
            ? "Request overwritten successfully: " . basename($path)
            : "Request created successfully: " . basename($path);
    }

    private function getRequestRules(array $fields): string
    {
        if (empty($fields)) {
            return '';
        }

        $rules = '';
        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $validations = (string) ($field['validations'] ?? '');

            if ($name === '') {
                continue;
            }

            $rules .= str_repeat("\t", 3) . "'" . $name . "' => '" . $validations . "',\n";
        }

        return FileUtil::cleanLastLineBreak($rules);
    }

    private function getStubContents(array $stubVariables = []): ?string
    {
        $stub = PathUtil::getRequestStubPath();
        $contents = @file_get_contents($stub);

        if ($contents === false) {
            return null;
        }

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{' . $search . '}}', $replace, $contents);
        }

        return $contents;
    }

    private function getStubVariables(string $namespace, string $className, string $rules): array
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'REQUEST_RULES' => $rules,
        ];
    }
}

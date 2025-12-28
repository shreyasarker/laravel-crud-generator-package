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
     * @param  array<string, mixed>  $options Supported: force(bool), dry_run(bool)
     */
    public function generate(string $name, array $options = []): string
    {
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);

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
            $this->getStubVariables($namespace, $className)
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
     * @param  array<string, string>  $stubVariables
     */
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

    /**
     * @return array<string, string>
     */
    private function getStubVariables(string $namespace, string $className): array
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
        ];
    }
}

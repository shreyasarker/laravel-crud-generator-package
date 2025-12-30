<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class ControllerGeneratorService
{
    public function __construct(private Filesystem $files)
    {
    }

    /**
     * @param  array<string, mixed>  $options Supported: force(bool), dry_run(bool), api(bool)
     */
    public function generate(string $name, array $options = []): string
    {
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);
        $api    = (bool) ($options['api'] ?? false);

        $naming = NameUtil::getNamingConvention($name);

        $namespace          = PathUtil::getControllerNamespace();
        $className          = $naming['singular_upper'];
        $fieldVariable      = $naming['singular_lower'];
        $viewsDirectoryName = $naming['plural_lower'];
        $route              = $naming['table_name'];

        $dir  = PathUtil::getControllerBasePath();
        $path = $dir . DIRECTORY_SEPARATOR . FileUtil::getFileName($className . 'Controller');

        $existedBefore = $this->files->exists($path);

        if ($existedBefore && ! $force) {
            return "Skipped controller: " . basename($path) . " already exists. Use --force to overwrite.";
        }

        $contents = $this->getStubContents(
            $this->getStubVariables($namespace, $className, $fieldVariable, $viewsDirectoryName, $route),
            $api
        );

        if ($contents === null) {
            $stubType = $api ? 'API' : 'web';
            return "Error: Controller stub not found/readable at " . PathUtil::getControllerStubPath($api);
        }

        if ($dryRun) {
            $controllerType = $api ? 'API controller' : 'web controller';
            return "Dry run: would write {$controllerType} to " . $path;
        }

        if (! $this->files->exists($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($path, $contents);

        $controllerType = $api ? 'API controller' : 'Controller';
        return $existedBefore
            ? "{$controllerType} overwritten successfully: " . basename($path)
            : "{$controllerType} created successfully: " . basename($path);
    }

    /**
     * @param  array<string, string>  $stubVariables
     */
    private function getStubContents(array $stubVariables = [], bool $api = false): ?string
    {
        $stub = PathUtil::getControllerStubPath($api);
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
    private function getStubVariables(
        string $namespace,
        string $className,
        string $fieldVariable,
        string $viewsDirectoryName,
        string $route
    ): array {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'MODEL_NAMESPACE' => PathUtil::getModelNamespace(),
            'REQUEST_NAMESPACE' => PathUtil::getRequestNamespace(),
            'FIELD_VARIABLE' => $fieldVariable,
            'VIEWS_DIRECTORY' => $viewsDirectoryName,
            'ROUTE' => $route,
        ];
    }
}
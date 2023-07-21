<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class ControllerGeneratorService
{
    private $files;

    public function __construct(FileSystem $files)
    {
        $this->files = $files;
    }

    public function generate($name)
    {
        $namingConvention = NameUtil::getNamingConvention($name);
        $namespace =  PathUtil::getControllerNamespace();
        $className = $namingConvention['singular_upper'];
        $fieldVariable = $namingConvention['singular_lower'];
        $viewsDirectoryName = $namingConvention['plural_lower'];
        $route = $namingConvention['table_name'];

        $path = PathUtil::getControllerBasePath() . DIRECTORY_SEPARATOR . FileUtil::getFileName($className . 'Controller');
        $contents = $this->getStubContents($this->getStubVariables($namespace, $className, $fieldVariable, $viewsDirectoryName, $route));

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Controller created successfully';
        }

        return 'Controller file already exists';
    }


    private function getStubContents($stubVariables = [])
    {
        $stub = PathUtil::getControllerStubPath();
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{'.$search.'}}', $replace, $contents);
        }

        return $contents;

    }

    private function getStubVariables($namespace, $className, $fieldVariable, $viewsDirectoryName, $route)
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'Model_CLASS_NAMESPACE' => PathUtil::getModelNamespace(),
            'REQUEST_CLASS_NAMESPACE' => PathUtil::getRequestNamespace(),
            'FIELD_VARIABLE' => $fieldVariable,
            'VIEWS_DIRECTORY' => $viewsDirectoryName,
            'ROUTE' => $route
        ];
    }
}

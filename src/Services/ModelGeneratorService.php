<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class ModelGeneratorService
{
    private Filesystem $files;

    public function __construct(FileSystem $files)
    {
        $this->files = $files;
    }

    public function generate($name)
    {
        $namingConvention = NameUtil::getNamingConvention($name);
        $namespace =  PathUtil::getModelNamespace();
        $className = $namingConvention['singular_upper'];

        $path = PathUtil::getModelBasePath() . DIRECTORY_SEPARATOR . FileUtil::getFileName($className);
        $contents = $this->getStubContents($this->getStubVariables($namespace, $className));

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Model created successfully';
        }

        return 'Model file already exists';
    }


    private function getStubContents($stubVariables = [])
    {
        $stub = PathUtil::getModelStubPath();
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{'.$search.'}}', $replace, $contents);
        }

        return $contents;

    }

    private function getStubVariables($namespace, $className)
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className
        ];
    }

}

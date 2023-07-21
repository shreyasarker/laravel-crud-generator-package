<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class RequestGeneratorService
{
    private Filesystem $files;

    public function __construct(FileSystem $files)
    {
        $this->files = $files;
    }

    public function generate($name, $fields)
    {
        $rules = $this->getRequestRules($fields);
        $namingConvention = NameUtil::getNamingConvention($name);
        $namespace =  PathUtil::getRequestNamespace();
        $className = $namingConvention['singular_upper'];

        $path = PathUtil::getRequestBasePath() . DIRECTORY_SEPARATOR . FileUtil::getFileName($className . 'Request');
        $this->makeDirectory(dirname($path));
        $contents = $this->getStubContents($this->getStubVariables($namespace, $className, $rules));

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Request created successfully';
        }

        return 'Request file already exists';
    }

    private function getRequestRules($fields)
    {
        $rules = '';
        foreach($fields as $field) {

            $rules .= str_repeat("\t", 3) . "'" . $field['name'] . "' => '" . $field['validations'] ."',\n"  ;
        }

        $rules = FileUtil::cleanLastLineBreak($rules);

        return $rules;
    }

    private function getStubContents($stubVariables = [])
    {
        $stub = PathUtil::getRequestStubPath();
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{'.$search.'}}', $replace, $contents);
        }

        return $contents;

    }

    private function getStubVariables($namespace, $className, $rules)
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'REQUEST_RULES' => $rules
        ];
    }


    private function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

        return $path;
    }
}

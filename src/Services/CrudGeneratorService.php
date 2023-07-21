<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Support\Pluralizer;

class CrudGeneratorService
{
    protected function getStubPath($type)
    {
        return __DIR__."../../template/stubs/$type.stub";
    }

    protected function getSingularUpperCaseClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    protected function getSingularLowerClassName($name)
    {
        return strtolower(Pluralizer::singular($name));
    }

    protected function getPluralLowerClassName($name)
    {
        return strtolower(Pluralizer::plural($name));
    }

    protected function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace("$$search$", $replace, $contents);
        }

        return $contents;

    }
}

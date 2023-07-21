<?php

namespace ShreyaSarker\LaraCrud\Utils;

use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class NameUtil
{
    public static function getNamingConvention($name)
    {
        return [
            'plural_upper' => ucfirst(Str::camel((Pluralizer::plural($name)))),
            'singular_upper' => ucwords(Str::camel(Pluralizer::singular($name))),
            'plural_lower' => lcfirst(Str::camel(Pluralizer::plural($name))),
            'singular_lower' => lcfirst(Str::camel(Pluralizer::singular($name))),
            'table_name' => Str::snake(Pluralizer::plural($name)),
            'label_upper' => ucwords(Pluralizer::singular($name)),
            'label_lower' => strtolower(Pluralizer::singular($name)),
        ];
    }
}

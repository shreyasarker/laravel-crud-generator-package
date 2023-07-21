<?php

namespace ShreyaSarker\LaraCrud\Utils;

class TypeUtil
{
    public static function getFieldType()
    {
        return [
            'string' => 'text',
            'text' => 'textarea',
            'mediumtext' => 'textarea',
            'longtext' => 'textarea',
            'password' => 'password',
            'email' => 'email',
            'number' => 'number',
            'integer' => 'number',
            'bigint' => 'number',
            'mediumint' => 'number',
            'tinyint' => 'number',
            'smallint' => 'number',
            'decimal' => 'number',
            'double' => 'number',
            'float' => 'number',
            'select' => 'select'
        ];
    }

    public static function getValidationType()
    {
        return [
            'string' => 'required|string|max:255',
            'text' => 'required|string|max:255',
            'mediumtext' => 'required|string|max:255',
            'longtext' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
            'email' => 'required|email|max:255',
            'number' => 'required|numeric',
            'integer' => 'required|numeric',
            'bigint' => 'required|numeric',
            'mediumint' => 'required|numeric',
            'tinyint' => 'required|numeric',
            'smallint' => 'required|numeric',
            'decimal' => 'required|numeric',
            'double' =>  'required|numeric',
            'float' =>  'required|numeric',
            'select' => 'required|string'
        ];
    }

    public static function getSqlColumnType()
    {
        return [
            'string' => 'string',
            'text' => 'text',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'password' => 'string',
            'email' => 'string',
            'number' => 'decimal',
            'integer' => 'integer',
            'bigint' => 'bigInteger',
            'mediumint' => 'mediumInteger',
            'tinyint' => 'tinyInteger',
            'smallint' => 'smallInteger',
            'decimal' => 'decimal',
            'double' =>  'double',
            'float' =>  'float',
            'select' => 'string'
        ];
    }
}

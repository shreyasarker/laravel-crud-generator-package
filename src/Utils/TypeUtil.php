<?php

namespace ShreyaSarker\LaraCrud\Utils;

class TypeUtil
{
    public static function getFieldType(): array
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

            'boolean' => 'checkbox',
            'date' => 'date',
            'datetime' => 'datetime-local',

            'select' => 'select',
        ];
    }

    public static function getValidationType(): array
    {
        return [
            // strings
            'string' => 'required|string|max:255',

            // text-like (no 255 cap by default)
            'text' => 'nullable|string',
            'mediumtext' => 'nullable|string',
            'longtext' => 'nullable|string',

            // auth-ish
            'password' => 'required|string|min:6|max:255',
            'email' => 'required|email|max:255',

            // numbers
            'number' => 'required|numeric',
            'integer' => 'required|integer',
            'bigint' => 'required|integer',
            'mediumint' => 'required|integer',
            'tinyint' => 'required|integer',
            'smallint' => 'required|integer',
            'decimal' => 'required|numeric',
            'double' => 'required|numeric',
            'float' => 'required|numeric',

            // booleans/dates
            'boolean' => 'required|boolean',
            'date' => 'required|date',
            'datetime' => 'required|date',

            // selections
            'select' => 'required|string',
        ];
    }

    public static function getSqlColumnType(): array
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
            'double' => 'double',
            'float' => 'float',

            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'dateTime',

            'select' => 'string',
        ];
    }
}

<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

class GlobalService
{
    use InteractsWithIO;

    public function __construct(ConsoleOutput $consoleOutput)
    {
        $this->output = $consoleOutput;
    }

    protected $validationTypeLookup = [
        'string' => 'required|string|max:255',
        'char' => 'required|string|max:255',
        'varchar' => 'required|string|max:255',
        'text' => 'required|string|max:255',
        'mediumtext' => 'required|string|max:255',
        'longtext' => 'required|string|max:255',
        'json' => 'required|string|max:255',
        'jsonb' => 'required|string|max:255',
        'binary' => 'required|string|max:255',
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
        'date' =>  'required|date',
        'datetime' => 'required|date',
        'timestamp' => 'required|date',
        'time' => 'required|timezone',
        'radio' => 'required|string',
        'boolean' => 'required|boolean',
        'enum' => 'required|string',
        'select' => 'required|string'
    ];

    protected $sqlTypeLookup = [
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

    public function getNamingConvention($name): array
    {
        return [
            'plural_camel' => Str::camel(Pluralizer::plural($name)),
            'plural_camel_upper' => ucwords(Str::camel(Pluralizer::plural($name))),
            'singular_camel' => Str::camel(Pluralizer::singular($name)),
            'singular_camel_lower' => ucfirst(Str::camel(Pluralizer::singular($name))),
            'plural_name' => ucwords(Pluralizer::plural($name)),
            'singular_name' => ucwords(Pluralizer::singular($name)),
            'plural_lower_name' => strtolower(Pluralizer::plural($name)),
            'singular_lower_name' => strtolower(Pluralizer::singular($name)),
            'table_name' => Str::snake(Pluralizer::plural($name))
        ];
    }

    public function parseFields($fields)
    {
        $fields= rtrim($fields, ';');
        $formFields = [];
        $fieldsArray = explode(';', $fields);
        if($fields){
            foreach($fieldsArray as $key => $item){
                $itemArray = explode('#', $item);
                $formFields[$key]['name'] = isset($itemArray[0]) ? trim($itemArray[0]) : null;
                $formFields[$key]['type'] = isset($itemArray[1]) ? trim($itemArray[1]) : null;
                $formFields[$key]['validations'] = isset($itemArray[1]) ? $this->validationTypeLookup[trim($itemArray[1])] : null;
                $formFields[$key]['sqlColumn'] = isset($itemArray[1]) ? $this->sqlTypeLookup[trim($itemArray[1])] : null;

                if ($formFields[$key]['type'] === 'select' || $formFields[$key]['type'] === 'select' && isset($itemArray[2])){
                    $options = isset($itemArray[2]) ? trim($itemArray[2]) : null;

                    if ($options){
                        $options = str_replace('options=', '', $options);
                        $options= preg_replace('/(?<!")(?<!\w)(\w+)(?!")(?!\w)/', '"$1"', $options);
                        $decodedOptions = json_decode($options, true);
                        if (!$decodedOptions) {
                            $this->error('Please provide a proper format for options.');
                        }

                        $formFields[$key]['options'] = $decodedOptions;
                    }
                }
            }
        }
        return $formFields;
    }

    public function getFileName($name): string
    {
        return $name . '.php';
    }

    public function cleanLastLineBreak($string): string
    {
        return rtrim($string, "\n");
    }
}

<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class MigrationGeneratorService
{
    private $files;

    public function __construct(FileSystem $files)
    {
        $this->files = $files;
    }

    public function generate($name, $fields)
    {
        $sqlColumns = $this->getSqlColumns($fields);

        $date = now()->format('Y_m_d_His');
        $namingConvention = NameUtil::getNamingConvention($name);
        $className = 'Create' . $namingConvention['plural_upper'] . 'Table';
        $migrationFileNameConvention = NameUtil::getNamingConvention($name);
        $migrationFileName = $date .'_create_'. $migrationFileNameConvention['table_name'] .'_table';
        $tableName = $namingConvention['table_name'];

        $path = PathUtil::getMigrationBasePath() . DIRECTORY_SEPARATOR . FileUtil::getFileName($migrationFileName);
        $contents = $this->getStubContents($this->getStubVariables($className, $tableName, $sqlColumns));

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            Artisan::call('migrate');
            return 'Migration created successfully';
        }

        return 'Migration file already exists';
    }

    private function getSqlColumns($fields)
    {
        $columns = '';
        foreach($fields as $field) {
            $columns .= str_repeat("\t", 3) . "$" . "table->" . trim($field['sqlColumn']) . "('"  . trim($field['name']) . "');\n";
        }

        $columns = FileUtil::cleanLastLineBreak($columns);

        return $columns;
    }

    private function getStubContents($stubVariables = [])
    {
        $stub = PathUtil::getMigrationStubPath();
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{'.$search.'}}', $replace, $contents);
        }

        return $contents;

    }

    private function getStubVariables($className, $tableName, $sqlColumns)
    {
        return [
            'CLASS_NAME' => $className,
            'TABLE_NAME' => $tableName,
            'SQL_COLUMNS' => $sqlColumns
        ];
    }
}

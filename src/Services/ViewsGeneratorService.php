<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;
use ShreyaSarker\LaraCrud\Utils\TypeUtil;

class ViewsGeneratorService
{
    use InteractsWithIO;

    private $files;

    private $formFieldType;
    private $heading;
    private $route;
    private $viewsDirectoryName;
    private $fieldVariable;

    public function __construct(FileSystem $files, ConsoleOutput $consoleOutput)
    {
        $this->files = $files;
        $this->formFieldType = TypeUtil::getFieldType();
        $this->output = $consoleOutput;
    }

    public function generate($name, $fields)
    {

        $namingConvention = NameUtil::getNamingConvention($name);
        $this->viewsDirectoryName = $namingConvention['plural_lower'];

        $this->heading = $namingConvention['label_upper'];
        $this->fieldVariable = $namingConvention['singular_lower'];
        $this->route = $namingConvention['table_name'];

        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName;
        $this->makeDirectory(dirname($path));

        if ($this->files->exists($path)){
            return 'Form views files already exist';
        }
        /* APP LAYOUT VIEW */
        $this->makeAppLayoutView();

        /* INDEX VIEW */
        $this->makeIndexView($fields);

        /* _FORM VIEW */
        $this->makeFormView($fields);

        /* CREATE VIEW */
        $this->makeCreateFormView();

        /* EDIT VIEW */
        $this->makeEditFormView();

        /* SHOW VIEW */
        $this->makeShowView($fields);

        return 'Form views created successfully';
    }

    private function makeAppLayoutView()
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'layouts'. DIRECTORY_SEPARATOR . 'app.blade.stub';

        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . 'layouts'. DIRECTORY_SEPARATOR . FileUtil::getFileName('app.blade');
        $this->makeDirectory(dirname($path));

        $contents = $this->getStubContents($stub, []);

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'App layout view created successfully';
        }

        return 'App layout view file already exists';
    }

    private function makeIndexView($formFields)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'index.blade.stub';
        $tableHeader=$tableBody='';
        foreach ($formFields as $value) {
            $field = $value['name'];

            $fieldName = NameUtil::getNamingConvention($field);
            $label = $fieldName['label_upper'];

            $tableHeader .= str_repeat("\t", 4)."<th>".trim($label)."</th>\n";
            $tableBody .= str_repeat("\t", 5)."<td>{{ $"."value->".trim($field)." }}</td>\n";
        }
        $stubVariables = [
            'HEADING' => $this->heading,
            'ROUTE' => $this->route,
            'DIRECTORY_NAME' => $this->viewsDirectoryName,
            'TABLE_HEADER' => $tableHeader,
            'TABLE_BODY' => $tableBody
        ];

        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . FileUtil::getFileName('index.blade');
        $this->makeDirectory(dirname($path));

        $contents = $this->getStubContents($stub, $stubVariables);

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Index view created successfully';
        }

        return 'Index view file already exists';
    }

    private function makeShowView($formFields)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'show.blade.stub';

        $showHtml = '';
        foreach($formFields as $item){
            $showHtml .= $this->makeFieldsWithDataView($item);
        }

        $stubVariables = [
            'HEADING' => $this->heading,
            'ROUTE' => $this->route,
            'DIRECTORY_NAME' => $this->viewsDirectoryName,
            'FIELDS_WITH_DATA' => $showHtml
        ];

        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . FileUtil::getFileName('show.blade');
        $this->makeDirectory(dirname($path));

        $contents = $this->getStubContents($stub, $stubVariables);

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Show view created successfully';
        }

        return 'Show view file already exists';
    }

    private function makeFieldsWithDataView($item){
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'fieldWithData.blade.stub';
        $namingConvention = NameUtil::getNamingConvention($item['name']);
        $stubVariables = [
            'FIELD_NAME' => $namingConvention['singular_lower'],
            'FIELD_LABEL' => $namingConvention['label_upper']
        ];

        return $this->getStubContents($stub, $stubVariables);
    }

    private function makeFormView($formFields)
    {
        $formFieldsHtml = '';
        foreach($formFields as $item){
            $formFieldsHtml .= $this->createField($item);
        }

        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . FileUtil::getFileName('_form.blade');
        $this->makeDirectory(dirname($path));

        if (!$this->files->exists($path)){
            $this->files->put($path, $formFieldsHtml);
            return 'Form view created successfully';
        }

        return 'Form view file already exists';
    }

    private function makeCreateFormView()
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'create.blade.stub';
        $stubVariables = [
            'HEADING' => $this->heading,
            'ROUTE' => $this->route,
            'DIRECTORY_NAME' => $this->viewsDirectoryName
        ];
        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . FileUtil::getFileName('create.blade');
        $this->makeDirectory(dirname($path));

        $contents = $this->getStubContents($stub, $stubVariables);

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Create view created successfully';
        }

        return 'Create view file already exists';
    }

    private function makeEditFormView()
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'edit.blade.stub';
        $stubVariables = [
            'HEADING' => $this->heading,
            'ROUTE' => $this->route,
            'FIELD_VARIABLE' => $this->fieldVariable,
            'DIRECTORY_NAME' => $this->viewsDirectoryName
        ];
        $path = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR .  $this->viewsDirectoryName . DIRECTORY_SEPARATOR . FileUtil::getFileName('edit.blade');
        $this->makeDirectory(dirname($path));

        $contents = $this->getStubContents($stub, $stubVariables);

        if (!$this->files->exists($path)){
            $this->files->put($path, $contents);
            return 'Edit view created successfully';
        }

        return 'Edit view file already exists';
    }

    private function createField($item)
    {
        switch ($this->formFieldType[$item['type']]) {
            case 'password':
                return $this->createPasswordField($item);
            case 'textarea':
                return $this->createTextareaField($item);
            case 'select':
                return $this->createSelectOptionsField($item);
            default:
                return $this->createInputField($item);
        }
    }

    private function createPasswordField($item)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'formFields' . DIRECTORY_SEPARATOR . 'passwordField.blade.stub';
        $namingConvention = NameUtil::getNamingConvention($item['name']);
        $stubVariables = [
            'FIELD_NAME' => $namingConvention['singular_lower'],
            'FIELD_LABEL' => $namingConvention['label_upper'],
            'FIELD_TYPE' => $this->formFieldType[$item['type']],
            'FIELD_VARIABLE' => $this->fieldVariable
        ];

        return $this->getStubContents($stub, $stubVariables);
    }

    private function createInputField($item)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'formFields' . DIRECTORY_SEPARATOR . 'inputField.blade.stub';
        $namingConvention = NameUtil::getNamingConvention($item['name']);
        $stubVariables = [
            'FIELD_NAME' => $namingConvention['singular_lower'],
            'FIELD_LABEL' => $namingConvention['label_upper'],
            'FIELD_TYPE' => $this->formFieldType[$item['type']],
            'FIELD_VARIABLE' => $this->fieldVariable
        ];

        return $this->getStubContents($stub, $stubVariables);
    }

    private function createTextareaField($item)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'formFields' . DIRECTORY_SEPARATOR . 'textareaField.blade.stub';
        $namingConvention = NameUtil::getNamingConvention($item['name']);
        $stubVariables = [
            'FIELD_NAME' => $namingConvention['singular_lower'],
            'FIELD_LABEL' => $namingConvention['label_upper'],
            'FIELD_VARIABLE' => $this->fieldVariable
        ];

        return $this->getStubContents($stub, $stubVariables);
    }

    private function createSelectOptionsField($item)
    {
        $stub = PathUtil::getViewsStubPath() . DIRECTORY_SEPARATOR . 'formFields' . DIRECTORY_SEPARATOR . 'selectOptionsFields.blade.stub';
        $namingConvention = NameUtil::getNamingConvention($item['name']);
        $stubVariables = [
            'FIELD_NAME' => $namingConvention['singular_lower'],
            'FIELD_LABEL' => $namingConvention['label_upper'],
            'FIELD_VARIABLE' => $this->fieldVariable,
            'OPTIONS' => $this->getOptions($item['options'],)
        ];
        return $this->getStubContents($stub, $stubVariables);
    }

    private function getOptions($options)
    {
        $optionsMarkup = '';
        foreach($options as $key => $option){
            $optionsMarkup .= str_repeat("\t", 3) . "<option value=\"" . $key ."\">" . $option . "</option>\n";
        }
        $optionsMarkup = FileUtil::cleanLastLineBreak($optionsMarkup);

        return $optionsMarkup;
    }

    private function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{'.$search.'}}', $replace, $contents);
        }

        return $contents;

    }

    private function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

       return $path;
   }
}

<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;
use ShreyaSarker\LaraCrud\Utils\TypeUtil;

class ViewsGeneratorService
{
    private string $stubBasePath = '';
    private array $formFieldType = [];

    private string $viewsDirectoryName = '';
    private string $heading = '';
    private string $fieldVariable = '';
    private string $route = '';

    // FIXED: Added missing properties
    private bool $force = false;
    private bool $dryRun = false;

    public function __construct(private Filesystem $files)
    {
        $this->formFieldType = TypeUtil::getFieldType();
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $options Supported: force(bool), dry_run(bool), api(bool), stack(string)
     */
    public function generate(string $name, array $fields, array $options = []): string
    {
        if (!empty($options['api'])) {
            return 'Skipped views (API mode enabled)';
        }

        // FIXED: Store force and dryRun as instance properties
        $this->force  = (bool) ($options['force'] ?? false);
        $this->dryRun = (bool) ($options['dry_run'] ?? false);
        $stack  = (string) ($options['stack'] ?? 'bootstrap');

        $allowedStacks = ['bootstrap', 'tailwind'];
        if (!in_array($stack, $allowedStacks, true)) {
            return "View stack '{$stack}' not supported. Supported: bootstrap, tailwind.";
        }

        $this->stubBasePath = PathUtil::getViewsStubPathByStack($stack);

        $naming = NameUtil::getNamingConvention($name);

        $this->viewsDirectoryName = $naming['plural_lower'];
        $this->heading = $naming['label_upper'];
        $this->fieldVariable = $naming['singular_lower'];
        $this->route = $naming['table_name'];

        $baseDir = PathUtil::getViewsBasePath() . DIRECTORY_SEPARATOR . $this->viewsDirectoryName;

        if ($this->files->exists($baseDir) && !$this->force) {
            return "Skipped views: directory '{$this->viewsDirectoryName}' already exists. Use --force to overwrite.";
        }

        if ($this->dryRun) {
            return "Dry run: would generate views in {$baseDir} using stack '{$stack}'.";
        }

        if ($this->files->exists($baseDir) && $this->force) {
            $this->files->deleteDirectory($baseDir);
        }

        $this->files->makeDirectory($baseDir, 0755, true);

        $this->makeAppLayoutView();
        $this->makeIndexView($fields);
        $this->makeFormView($fields);
        $this->makeCreateFormView();
        $this->makeEditFormView();
        $this->makeShowView($fields);

        return "Views generated successfully ({$this->viewsDirectoryName}) using stack '{$stack}'.";
    }

    /* ----------------------- Views ----------------------- */

    private function makeAppLayoutView(): void
    {
        $this->writeStub(
            'layouts/app.blade.stub',
            'layouts/app.blade.php',
            []
        );
    }

    private function makeIndexView(array $fields): void
    {
        $header = $body = '';

        foreach ($fields as $field) {
            $name = NameUtil::getNamingConvention($field['name']);
            $header .= str_repeat("\t", 4) . "<th>{$name['label_upper']}</th>\n";
            $body   .= str_repeat("\t", 5) . "<td>{{ \$value->{$field['name']} }}</td>\n";
        }

        $this->writeStub(
            'index.blade.stub',
            'index.blade.php',
            [
                'HEADING' => $this->heading,
                'ROUTE' => $this->route,
                'DIRECTORY_NAME' => $this->viewsDirectoryName,
                'TABLE_HEADER' => FileUtil::cleanLastLineBreak($header),
                'TABLE_BODY' => FileUtil::cleanLastLineBreak($body),
            ]
        );
    }

    private function makeShowView(array $fields): void
    {
        $html = '';
        foreach ($fields as $item) {
            $html .= $this->makeFieldsWithDataView($item);
        }

        $this->writeStub(
            'show.blade.stub',
            'show.blade.php',
            [
                'HEADING' => $this->heading,
                'ROUTE' => $this->route,
                'DIRECTORY_NAME' => $this->viewsDirectoryName,
                'FIELDS_WITH_DATA' => $html,
            ]
        );
    }

    private function makeFormView(array $fields): void
    {
        $html = '';
        foreach ($fields as $item) {
            $html .= $this->createField($item);
        }

        $path = PathUtil::getViewsBasePath()
            . DIRECTORY_SEPARATOR
            . $this->viewsDirectoryName
            . DIRECTORY_SEPARATOR
            . '_form.blade.php';

        if ($this->dryRun) {
            return;
        }

        $this->files->put($path, $html);
    }

    private function makeCreateFormView(): void
    {
        $this->writeStub(
            'create.blade.stub',
            'create.blade.php',
            [
                'HEADING' => $this->heading,
                'ROUTE' => $this->route,
                'DIRECTORY_NAME' => $this->viewsDirectoryName,
            ]
        );
    }

    private function makeEditFormView(): void
    {
        $this->writeStub(
            'edit.blade.stub',
            'edit.blade.php',
            [
                'HEADING' => $this->heading,
                'ROUTE' => $this->route,
                'FIELD_VARIABLE' => $this->fieldVariable,
                'DIRECTORY_NAME' => $this->viewsDirectoryName,
            ]
        );
    }

    /* ----------------------- Helpers ----------------------- */

    // FIXED: Corrected method to write stub files
    private function writeStub(string $stubName, string $fileName, array $replacements): void
    {
        $contents = $this->loadStub($stubName, $replacements);

        if ($contents === null) {
            return;
        }

        $path = PathUtil::getViewsBasePath()
            . DIRECTORY_SEPARATOR
            . $this->viewsDirectoryName
            . DIRECTORY_SEPARATOR
            . $fileName;

        if ($this->files->exists($path) && !$this->force) {
            return;
        }

        if ($this->dryRun) {
            return;
        }

        $dir = dirname($path);
        if (!$this->files->exists($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($path, $contents);
    }

    // FIXED: Separated stub loading logic
    private function loadStub(string $stubName, array $replacements = []): ?string
    {
        $path = $this->stubBasePath . DIRECTORY_SEPARATOR . $stubName;

        if (!file_exists($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        foreach ($replacements as $key => $value) {
            $contents = str_replace('{{ ' . $key . ' }}', $value, $contents);
        }

        return $contents;
    }

    private function makeFieldsWithDataView(array $item): string
    {
        $name = NameUtil::getNamingConvention($item['name']);

        $content = $this->loadStub('fieldWithData.blade.stub', [
            'FIELD_NAME' => $name['singular_lower'],
            'FIELD_LABEL' => $name['label_upper'],
            'FIELD_VARIABLE' => $this->fieldVariable,
        ]);

        return $content ?? '';
    }

    private function createField(array $item): string
    {
        return match ($this->formFieldType[$item['type']] ?? 'input') {
            'password' => $this->getFormField('passwordField', $item),
            'textarea' => $this->getFormField('textareaField', $item),
            'select' => $this->getFormField('selectOptionsFields', $item),
            default => $this->getFormField('inputField', $item),
        };
    }

    private function getFormField(string $stubName, array $item): string
    {
        $name = NameUtil::getNamingConvention($item['name']);

        // If select options are array, convert to <option> markup
        $optionsMarkup = '';
        if (($item['type'] ?? '') === 'select' && !empty($item['options']) && is_array($item['options'])) {
            foreach ($item['options'] as $key => $label) {
                $key = (string) $key;
                $label = (string) $label;

                $optionsMarkup .= str_repeat("\t", 3)
                    . '<option value="' . $key . '" {{ old("' . $name['singular_lower'] . '", $' . $this->fieldVariable . '->' . $name['singular_lower'] . ' ?? "") == "' . $key . '" ? "selected" : "" }}>'
                    . e($label)
                    . "</option>\n";
            }
        }

        $content = $this->loadStub("formFields/{$stubName}.blade.stub", [
            'FIELD_NAME' => $name['singular_lower'],
            'FIELD_LABEL' => $name['label_upper'],
            'FIELD_TYPE' => $this->formFieldType[$item['type']] ?? 'text',
            'FIELD_VARIABLE' => $this->fieldVariable,
            'OPTIONS' => $optionsMarkup,
        ]);

        return $content ?? '';
    }
}
<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\FileUtil;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class RequestGeneratorService
{
    public function __construct(private Filesystem $files)
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @param  array<string, mixed>  $options Supported: force(bool), dry_run(bool)
     */
    public function generate(string $name, array $fields, array $options = []): string
    {
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $rules = $this->getRequestRules($fields);
        $messages = $this->getCustomMessages($fields);
        $attributes = $this->getCustomAttributes($fields);

        $naming = NameUtil::getNamingConvention($name);
        $namespace = PathUtil::getRequestNamespace();
        $className = $naming['singular_upper'];

        $dir  = PathUtil::getRequestBasePath();
        $path = $dir . DIRECTORY_SEPARATOR . FileUtil::getFileName($className . 'Request');

        $existedBefore = $this->files->exists($path);

        if ($existedBefore && ! $force) {
            return "Skipped request: " . basename($path) . " already exists. Use --force to overwrite.";
        }

        $contents = $this->getStubContents(
            $this->getStubVariables($namespace, $className, $rules, $messages, $attributes)
        );

        if ($contents === null) {
            return "Error: Request stub not found/readable at " . PathUtil::getRequestStubPath();
        }

        if ($dryRun) {
            return "Dry run: would write request to " . $path;
        }

        if (! $this->files->exists($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        $this->files->put($path, $contents);

        return $existedBefore
            ? "Request overwritten successfully: " . basename($path)
            : "Request created successfully: " . basename($path);
    }

    /**
     * Generate validation rules from fields
     */
    private function getRequestRules(array $fields): string
    {
        if (empty($fields)) {
            return '';
        }

        $rules = '';
        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $validations = (string) ($field['validations'] ?? '');

            if ($name === '') {
                continue;
            }

            $rules .= str_repeat("\t", 3) . "'{$name}' => '{$validations}',\n";
        }

        return FileUtil::cleanLastLineBreak($rules);
    }

    /**
     * Generate custom validation messages for common rules
     */
    private function getCustomMessages(array $fields): string
    {
        if (empty($fields)) {
            return str_repeat("\t", 3) . '// Add custom messages here';
        }

        $messages = '';
        $hasMessages = false;

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            $validations = (string) ($field['validations'] ?? '');
            $fieldNaming = NameUtil::getNamingConvention($name);
            $label = $fieldNaming['label_lower'];

            if ($name === '') {
                continue;
            }

            // Generate messages for common validation rules
            if (str_contains($validations, 'required')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.required' => 'The {$label} field is required.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'email')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.email' => 'The {$label} must be a valid email address.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'unique')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.unique' => 'This {$label} has already been taken.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'min:')) {
                preg_match('/min:(\d+)/', $validations, $matches);
                $min = $matches[1] ?? '0';
                
                if (str_contains($validations, 'string')) {
                    $messages .= str_repeat("\t", 3) . "'{$name}.min' => 'The {$label} must be at least {$min} characters.',\n";
                } else {
                    $messages .= str_repeat("\t", 3) . "'{$name}.min' => 'The {$label} must be at least {$min}.',\n";
                }
                $hasMessages = true;
            }

            if (str_contains($validations, 'max:')) {
                preg_match('/max:(\d+)/', $validations, $matches);
                $max = $matches[1] ?? '0';
                
                if (str_contains($validations, 'string')) {
                    $messages .= str_repeat("\t", 3) . "'{$name}.max' => 'The {$label} may not be greater than {$max} characters.',\n";
                } else {
                    $messages .= str_repeat("\t", 3) . "'{$name}.max' => 'The {$label} may not be greater than {$max}.',\n";
                }
                $hasMessages = true;
            }

            if (str_contains($validations, 'numeric')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.numeric' => 'The {$label} must be a number.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'integer')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.integer' => 'The {$label} must be an integer.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'date')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.date' => 'The {$label} is not a valid date.',\n";
                $hasMessages = true;
            }

            if (str_contains($validations, 'boolean')) {
                $messages .= str_repeat("\t", 3) . "'{$name}.boolean' => 'The {$label} field must be true or false.',\n";
                $hasMessages = true;
            }
        }

        if (!$hasMessages) {
            return str_repeat("\t", 3) . '// Add custom messages here';
        }

        return FileUtil::cleanLastLineBreak($messages);
    }

    /**
     * Generate custom attribute names for better error messages
     */
    private function getCustomAttributes(array $fields): string
    {
        if (empty($fields)) {
            return str_repeat("\t", 3) . '// Add custom attribute names here';
        }

        $attributes = '';

        foreach ($fields as $field) {
            $name = (string) ($field['name'] ?? '');
            
            if ($name === '') {
                continue;
            }

            $fieldNaming = NameUtil::getNamingConvention($name);
            $label = $fieldNaming['label_lower'];

            $attributes .= str_repeat("\t", 3) . "'{$name}' => '{$label}',\n";
        }

        return FileUtil::cleanLastLineBreak($attributes);
    }

    private function getStubContents(array $stubVariables = []): ?string
    {
        $stub = PathUtil::getRequestStubPath();
        $contents = @file_get_contents($stub);

        if ($contents === false) {
            return null;
        }

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{' . $search . '}}', $replace, $contents);
        }

        return $contents;
    }

    private function getStubVariables(
        string $namespace,
        string $className,
        string $rules,
        string $messages,
        string $attributes
    ): array {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $className,
            'REQUEST_RULES' => $rules,
            'CUSTOM_MESSAGES' => $messages,
            'CUSTOM_ATTRIBUTES' => $attributes,
        ];
    }
}
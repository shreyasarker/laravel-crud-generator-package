<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class RouteGeneratorService
{
    public function __construct(private Filesystem $files)
    {
    }

    /**
     * @param array<string, mixed> $options Supported: api(bool), force(bool), dry_run(bool)
     */
    public function generate(string $name, array $options = []): string
    {
        $api    = (bool) ($options['api'] ?? false);
        $force  = (bool) ($options['force'] ?? false);
        $dryRun = (bool) ($options['dry_run'] ?? false);

        $naming = NameUtil::getNamingConvention($name);

        $routePath       = $naming['table_name'];
        $controllerClass = $naming['singular_upper'] . 'Controller';

        // Validate route path
        if (empty($routePath)) {
            return "Error: Invalid route path generated from name '{$name}'";
        }

        $routesDir           = base_path('routes');
        $generatedRoutesFile = $routesDir . DIRECTORY_SEPARATOR . 'lara-crud.php';

        $targetRoutesFile = $api
            ? $routesDir . DIRECTORY_SEPARATOR . 'api.php'
            : $routesDir . DIRECTORY_SEPARATOR . 'web.php';

        $includeLine = "require_once __DIR__ . '/lara-crud.php';";

        // Use PathUtil for consistent namespace
        $namespace = PathUtil::getControllerNamespace();
        $routeLine = $api
            ? "Route::apiResource('{$routePath}', \\{$namespace}\\{$controllerClass}::class);"
            : "Route::resource('{$routePath}', \\{$namespace}\\{$controllerClass}::class);";

        $header = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n";

        if ($dryRun) {
            return "Dry run: would add route for '{$routePath}' into routes/lara-crud.php and include it from " . basename($targetRoutesFile);
        }

        if (! $this->files->exists($routesDir)) {
            $this->files->makeDirectory($routesDir, 0755, true);
        }

        // 1) Ensure routes/lara-crud.php exists and has header
        if (! $this->files->exists($generatedRoutesFile)) {
            $this->files->put($generatedRoutesFile, $header);
        }

        $existing = $this->files->get($generatedRoutesFile);

        // Ensure header/import exists (if user edited file)
        if (! str_contains($existing, 'use Illuminate\\Support\\Facades\\Route;')) {
            $trimmed = ltrim($existing);

            // If file already has <?php, inject "use Route" after it (avoid duplicate PHP tags)
            if (str_starts_with($trimmed, '<?php')) {
                $existing = preg_replace(
                    '/^<\\?php\\s*/',
                    "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n",
                    $trimmed,
                    1
                ) ?? $existing;
            } else {
                // Otherwise, prepend the full header
                $existing = $header . $trimmed;
            }

            $this->files->put($generatedRoutesFile, $existing);
        }

        // Already exists?
        if (str_contains($existing, $routeLine) && ! $force) {
            $this->ensureIncludeLine($targetRoutesFile, $includeLine);
            return "Skipped routes: route already exists in routes/lara-crud.php. Use --force to rewrite.";
        }

        if ($force) {
            $updated = $this->removeExistingCrudRoute($existing, $routePath);
            $updated = rtrim($updated) . "\n" . $routeLine . "\n";
            $this->files->put($generatedRoutesFile, $updated);
        } else {
            $this->files->append($generatedRoutesFile, $routeLine . "\n");
        }

        // 2) Ensure include exists in web.php/api.php
        $this->ensureIncludeLine($targetRoutesFile, $includeLine);

        return "Routes updated successfully (" . ($api ? 'api' : 'web') . ")";
    }

    private function ensureIncludeLine(string $targetRoutesFile, string $includeLine): void
    {
        // Check if target route file exists (web.php or api.php)
        if (! $this->files->exists($targetRoutesFile)) {
            // Don't create main route files - they should exist in Laravel
            // Silently skip - lara-crud.php will still work if manually included
            return;
        }

        $contents = $this->files->get($targetRoutesFile);

        // Check if include line already exists
        if (str_contains($contents, $includeLine)) {
            return;
        }

        // Add include line at the end
        $this->files->append($targetRoutesFile, "\n" . $includeLine . "\n");
    }

    /**
     * Remove any existing Route::resource or Route::apiResource for this route path.
     * Supports both single and double quotes.
     */
    private function removeExistingCrudRoute(string $contents, string $routePath): string
    {
        $routePath = preg_quote($routePath, '/');

        $patterns = [
            // matches Route::resource('posts', ...) OR Route::resource("posts", ...)
            "/^\\s*Route::resource\\((['\"])\\s*{$routePath}\\s*\\1.*?\\)\\;\\s*\\n?/m",
            "/^\\s*Route::apiResource\\((['\"])\\s*{$routePath}\\s*\\1.*?\\)\\;\\s*\\n?/m",
        ];

        return preg_replace($patterns, '', $contents) ?? $contents;
    }
}
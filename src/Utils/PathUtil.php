<?php

namespace ShreyaSarker\LaraCrud\Utils;

class PathUtil
{
    /* =========================
     |  Base paths
     ========================= */

    public static function appPath(): string
    {
        return app_path();
    }

    public static function routesPath(): string
    {
        return base_path('routes');
    }

    public static function viewsPath(): string
    {
        return resource_path('views');
    }

    public static function migrationsPath(): string
    {
        return database_path('migrations');
    }

    /* =========================
     |  Generator base paths (FIXED)
     ========================= */

    public static function getMigrationBasePath(): string
    {
        return database_path('migrations');
    }

    public static function getModelBasePath(): string
    {
        return app_path();
    }

    public static function getRequestBasePath(): string
    {
        return app_path('Http/Requests');
    }

    public static function getControllerBasePath(): string
    {
        return app_path('Http/Controllers');
    }

    public static function getViewsBasePath(): string
    {
        return resource_path('views');
    }

    /* =========================
     |  Namespaces
     ========================= */

    public static function getModelNamespace(): string
    {
        return 'App';
    }

    public static function getControllerNamespace(): string
    {
        return 'App\Http\Controllers';
    }

    public static function getRequestNamespace(): string
    {
        return 'App\Http\Requests';
    }

    /* =========================
     |  Stub paths
     ========================= */

    public static function stubsBasePath(): string
    {
        return __DIR__ . '/../stubs';
    }

    public static function getMigrationStubPath(): string
    {
        return self::stubsBasePath() . '/migration.stub';
    }

    public static function getModelStubPath(): string
    {
        return self::stubsBasePath() . '/model.stub';
    }

    public static function getRequestStubPath(): string
    {
        return self::stubsBasePath() . '/request.stub';
    }

    public static function getControllerStubPath(bool $api = false): string
    {
        return $api
            ? self::stubsBasePath() . '/controller.api.stub'
            : self::stubsBasePath() . '/controller.stub';
    }

    /**
     * Return the views stub directory based on stack
     *
     * @param string $stack bootstrap|tailwind
     */
    public static function getViewsStubPathByStack(string $stack = 'bootstrap'): string
    {
        $stack = strtolower($stack);

        return match ($stack) {
            'tailwind' => self::stubsBasePath() . '/views_tailwind',
            default => self::stubsBasePath() . '/views_bootstrap',
        };
    }
}
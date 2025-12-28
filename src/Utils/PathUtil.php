<?php

namespace ShreyaSarker\LaraCrud\Utils;

class PathUtil
{
    public static function getStubPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'stubs';
    }

    public static function getBasePath(string $directory): string
    {
        return base_path($directory);
    }

    /* MODEL PATH */
    public static function getModelNamespace(): string
    {
        return 'App\Models';
    }

    public static function getModelBasePath(): string
    {
        return self::getBasePath('app') . DIRECTORY_SEPARATOR . 'Models';
    }

    public static function getModelStubPath(): string
    {
        return self::getStubPath() . DIRECTORY_SEPARATOR . 'Model.stub';
    }

    /* REQUEST PATH */
    public static function getRequestNamespace(): string
    {
        return 'App\Http\Requests';
    }

    public static function getRequestBasePath(): string
    {
        return self::getBasePath('app\\Http') . DIRECTORY_SEPARATOR . 'Requests';
    }

    public static function getRequestStubPath(): string
    {
        return self::getStubPath() . DIRECTORY_SEPARATOR . 'Request.stub';
    }

    /* CONTROLLER PATH */
    public static function getControllerNamespace(): string
    {
        return 'App\Http\Controllers';
    }

    public static function getControllerBasePath(): string
    {
        return self::getBasePath('app\\Http') . DIRECTORY_SEPARATOR . 'Controllers';
    }

    public static function getControllerStubPath(): string
    {
        return self::getStubPath() . DIRECTORY_SEPARATOR . 'Controller.stub';
    }

    /* VIEW PATH */
    public static function getViewsBasePath(): string
    {
        return self::getBasePath('') . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
    }

    public static function getViewsStubPathByStack(string $stack = 'bootstrap'): string
    {
        $base = self::getStubPath() . DIRECTORY_SEPARATOR;

        return match ($stack) {
            'tailwind' => $base . 'views_tailwind',
            'bootstrap' => $base . 'views_bootstrap',
            default => $base . 'views_bootstrap',
        };
    }

    /* MIGRATION PATH */
    public static function getMigrationBasePath(): string
    {
        return self::getBasePath('') . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    }

    public static function getMigrationStubPath(): string
    {
        return self::getStubPath() . DIRECTORY_SEPARATOR . 'Migration.stub';
    }
}

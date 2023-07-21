<?php

namespace ShreyaSarker\LaraCrud\Utils;

class PathUtil
{
    public static function getStubPath(): string
    {
        return __DIR__ . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs';
    }

    public static function getBasePath($directory): string
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
        return PathUtil::getBasePath('app').DIRECTORY_SEPARATOR.'Models';
    }

    public static function getModelStubPath(): string
    {
        return PathUtil::getStubPath().DIRECTORY_SEPARATOR.'Model.stub';
    }

    /* REQUEST PATH */
    public static function getRequestNamespace(): string
    {
        return 'App\Http\Requests';
    }

    public static function getRequestBasePath(): string
    {
        return PathUtil::getBasePath('app\Http').DIRECTORY_SEPARATOR.'Requests';
    }

    public static function getRequestStubPath(): string
    {
        return PathUtil::getStubPath().DIRECTORY_SEPARATOR.'Request.stub';
    }

    /* CONTROLLER PATH */
    public static function getControllerNamespace(): string
    {
        return 'App\Http\Controllers';
    }

    public static function getControllerBasePath(): string
    {
        return PathUtil::getBasePath('app\Http').DIRECTORY_SEPARATOR.'Controllers';
    }

    public static function getControllerStubPath(): string
    {
        return PathUtil::getStubPath().DIRECTORY_SEPARATOR.'Controller.stub';
    }

    /* VIEW PATH */
    public static function getViewsBasePath(): string
    {
        return PathUtil::getBasePath('').DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views';
    }

    public static function getViewsStubPath(): string
    {
        return PathUtil::getStubPath().DIRECTORY_SEPARATOR.'views';
    }

    /* MIGRATION PATH */
    public static function getMigrationBasePath(): string
    {
    return PathUtil::getBasePath('').DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
    }

    public static function getMigrationStubPath(): string
    {
    return PathUtil::getStubPath().DIRECTORY_SEPARATOR.'Migration.stub';
    }


    /* ROUTES PATH */
    public static function getRoutesBasePath(): string
    {
    return PathUtil::getBasePath('').DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR.'web.php';
    }
}

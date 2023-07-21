<?php

namespace ShreyaSarker\LaraCrud\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ShreyaSarker\LaraCrud\Utils\NameUtil;
use ShreyaSarker\LaraCrud\Utils\PathUtil;

class RouteGeneratorService extends CrudGeneratorService
{
    public function generate($name)
    {
        $path = PathUtil::getRoutesBasePath();

        $namingConvention = NameUtil::getNamingConvention($name);
        $routePath = $namingConvention['table_name'];
        $controllerName = $namingConvention['singular_upper'];

        $route = "Route::resource('" . $routePath . "', " . DIRECTORY_SEPARATOR . PathUtil::getControllerNamespace() . DIRECTORY_SEPARATOR . $controllerName . "Controller::class);\n";

        File::append($path, $route);
        Artisan::call('optimize');
    }
}

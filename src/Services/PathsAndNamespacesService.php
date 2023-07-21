<?php

namespace ShreyaSarker\LaraCrud\Services;

class PathsAndNamespacesService
{
    public function getStubPath(): string
    {
        return __DIR__ . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs';
    }

    public function getBasePath($directory): string
    {
        return base_path($directory);
    }

    /* MODEL PATH */
    public function getModelNamespace(): string
    {
        return 'App\Models';
    }

    public function getModelBasePath(): string
    {
        return $this->getBasePath('app').DIRECTORY_SEPARATOR.'Models';
    }

    public function getModelStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Model.stub';
    }

    /* REQUEST PATH */
    public function getRequestNamespace(): string
    {
        return 'App\Http\Requests';
    }

    public function getRequestBasePath(): string
    {
        return $this->getBasePath('app\Http').DIRECTORY_SEPARATOR.'Requests';
    }

    public function getRequestStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Request.stub';
    }

    /* CONTROLLER PATH */
    public function getControllerNamespace(): string
    {
        return 'App\Http\Controllers';
    }

    public function getControllerBasePath(): string
    {
        return $this->getBasePath('app\Http').DIRECTORY_SEPARATOR.'Controllers';
    }

    public function getControllerStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Controller.stub';
    }

    /* VIEW PATH */
    public function getViewsBasePath(): string
    {
        return $this->getBasePath('').DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views';
    }

    public function getViewsStubPath(): string
    {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'views';
    }

     /* MIGRATION PATH */
     public function getMigrationBasePath(): string
     {
        return $this->getBasePath('').DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
     }

     public function getMigrationStubPath(): string
     {
        return $this->getStubPath().DIRECTORY_SEPARATOR.'Migration.stub';
     }
}

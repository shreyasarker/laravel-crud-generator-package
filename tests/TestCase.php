<?php

namespace ShreyaSarker\LaraCrud\Tests;

use \Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected $consoleOutput;

    protected function getPackageProviders($app)
    {
        return [\ShreyaSarker\LaraCrud\CrudServiceProvider::class];
    }


    public function setUp(): void
    {
        parent::setUp();

    }

    public function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('artisan', function ($app) {
            return new \Illuminate\Console\Application($app, $app['events'], $app->version());
        });

        $app->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);
    }

    public function consoleOutput()
    {
        return $this->consoleOutput ?: $this->consoleOutput = $this->app[Kernel::class]->output();
    }

}

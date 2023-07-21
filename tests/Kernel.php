<?php

namespace ShreyaSarker\LaraCrud\Tests;

use \Illuminate\Foundation\Console\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @return void
     */
    protected $bootstrappers = [];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    public function getArtisan()
    {
        return $this->app['artisan'];
    }
}

<?php

namespace ShreyaSarker\LaraCrud;

use Illuminate\Support\ServiceProvider;
use ShreyaSarker\LaraCrud\Commands\MakeCrudCommand;

class CrudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCrudCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        // Later we can add: publishing stubs/config files etc.
    }
}

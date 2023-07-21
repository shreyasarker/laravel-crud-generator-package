<?php

namespace ShreyaSarker\LaraCrud;

use Illuminate\Support\ServiceProvider;
use ShreyaSarker\LaraCrud\Commands\MakeCrudCommand;

class CrudServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->commands([MakeCrudCommand::class]);
    }

    public function register() {}
}

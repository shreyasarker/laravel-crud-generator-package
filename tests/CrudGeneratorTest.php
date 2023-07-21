<?php

namespace ShreyaSarker\LaraCrud\Tests;


class CrudGeneratorTest extends TestCase
{
    public function testCRUDCommands()
    {
        $this->artisan('make:crud', [
            'name' => 'Product'
        ])->expectsConfirmation('There is no field specify. Do you want to continue?', 'no')
        ->expectsOutput('Crud generation canceled.')->assertExitCode(1);

    }
}

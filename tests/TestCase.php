<?php
/*
namespace ShreyaSarker\LaraCrud\Tests;

use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase as Orchestra;
use ShreyaSarker\LaraCrud\CrudServiceProvider;

class TestCase extends Orchestra
{
    protected string $tempBasePath;

    protected function getPackageProviders($app): array
    {
        return [CrudServiceProvider::class];
    }

    
    protected function resolveApplicationBasePath($app): string
    {
        return $this->tempBasePath;
    }

    protected function setUp(): void
    {
        $this->tempBasePath = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . 'lara-crud-test-'
            . uniqid('', true);

        $this->makeSkeleton($this->tempBasePath);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        (new Filesystem())->deleteDirectory($this->tempBasePath);

        parent::tearDown();
    }

    private function makeSkeleton(string $base): void
    {
        $fs = new Filesystem();

        // Required folders for Laravel/Testbench boot
        foreach ([
            'bootstrap/cache',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',

            // Your package needs these
            'app/Models',
            'app/Http/Controllers',
            'app/Http/Requests',
            'database/migrations',
            'resources/views',
            'routes',
        ] as $dir) {
            $fs->makeDirectory(
                $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dir),
                0777,
                true,
                true
            );
        }

        // Minimal route files
        $fs->put($base . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php', "<?php\n\n");
        $fs->put($base . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'api.php', "<?php\n\n");
    }
}
*/
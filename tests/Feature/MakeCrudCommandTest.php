<?php
/*
namespace ShreyaSarker\LaraCrud\Tests\Feature;

use ShreyaSarker\LaraCrud\Tests\TestCase;

class MakeCrudCommandTest extends TestCase
{
    public function test_it_requires_interactive_flag(): void
    {
        $this->artisan('make:crud', ['name' => 'Post'])
            ->expectsOutputToContain('Please use --interactive')
            ->assertExitCode(1);
    }

    public function test_routes_dry_run_does_not_write_anything(): void
    {
        $routesFile = base_path('routes/lara-crud.php');

        $this->assertFileDoesNotExist($routesFile);

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'routes',
            '--dry-run' => true,
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsOutputToContain('Plan:')
            ->expectsOutputToContain('Dry run: yes')
            ->expectsOutputToContain('Dry run: would add route for')
            ->assertExitCode(0);

        $this->assertFileDoesNotExist($routesFile);

        $web = file_get_contents(base_path('routes/web.php'));
        $this->assertStringNotContainsString('lara-crud.php', $web);
    }

    public function test_it_generates_web_routes_and_includes_file(): void
    {
        $this->assertRoutesDirWritable();

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'routes',
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsConfirmation('Proceed to generate files?', true)
            ->assertExitCode(0);

        $this->assertFileExistsWithDebug(base_path('routes/lara-crud.php'));
        $this->assertFileExists(base_path('routes/web.php'));

        $generated = file_get_contents(base_path('routes/lara-crud.php'));
        $this->assertStringContainsString("use Illuminate\\Support\\Facades\\Route;", $generated);
        $this->assertStringContainsString("Route::resource('posts'", $generated);

        $web = file_get_contents(base_path('routes/web.php'));
        $this->assertStringContainsString("require_once __DIR__ . '/lara-crud.php';", $web);
    }

    public function test_it_generates_api_routes_when_api_flag_is_used(): void
    {
        $this->assertRoutesDirWritable();

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'routes',
            '--api' => true,
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsConfirmation('Proceed to generate files?', true)
            ->assertExitCode(0);

        $this->assertFileExistsWithDebug(base_path('routes/lara-crud.php'));
        $this->assertFileExists(base_path('routes/api.php'));

        $generated = file_get_contents(base_path('routes/lara-crud.php'));
        $this->assertStringContainsString("Route::apiResource('posts'", $generated);

        $api = file_get_contents(base_path('routes/api.php'));
        $this->assertStringContainsString("require_once __DIR__ . '/lara-crud.php';", $api);
    }

    public function test_routes_second_run_does_not_duplicate_route_line(): void
    {
        $this->assertRoutesDirWritable();

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'routes',
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsConfirmation('Proceed to generate files?', true)
            ->assertExitCode(0);

        $this->assertFileExistsWithDebug(base_path('routes/lara-crud.php'));

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'routes',
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsConfirmation('Proceed to generate files?', true)
            ->assertExitCode(0);

        $generated = file_get_contents(base_path('routes/lara-crud.php'));
        $count = substr_count($generated, "Route::resource('posts'");
        $this->assertSame(1, $count);
    }

    public function test_views_dry_run_does_not_write_views_directory(): void
    {
        $viewsDir = base_path('resources/views/posts');

        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'views',
            '--dry-run' => true,
            '--stack' => 'bootstrap',
        ])
            ->expectsQuestion('Field name (leave empty to finish)', '')
            ->expectsOutputToContain('Dry run: yes')
            ->assertExitCode(0);

        $this->assertDirectoryDoesNotExist($viewsDir);
    }

    private function assertRoutesDirWritable(): void
    {
        $routesDir = base_path('routes');

        $this->assertDirectoryExists($routesDir, 'routes/ directory missing at: ' . $routesDir);
        $this->assertTrue(is_writable($routesDir), 'routes/ directory is not writable: ' . $routesDir);
    }

    private function assertFileExistsWithDebug(string $path): void
    {
        if (file_exists($path)) {
            $this->assertTrue(true);
            return;
        }

        $routesDir = base_path('routes');
        $files = is_dir($routesDir) ? implode(', ', array_diff(scandir($routesDir) ?: [], ['.', '..'])) : '(routes dir missing)';
        $this->fail("Expected file not found: {$path}\nbase_path(): " . base_path() . "\nroutes dir: {$routesDir}\nroutes contents: {$files}");
    }
}
*/
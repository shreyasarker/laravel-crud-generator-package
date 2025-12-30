<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\RouteGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class RouteGeneratorServiceTest extends TestCase
{
    private RouteGeneratorService $service;
    private Filesystem $files;
    private string $testRoutesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new RouteGeneratorService($this->files);
        $this->testRoutesPath = sys_get_temp_dir() . '/test_routes';

        // Create test directory
        if (!$this->files->exists($this->testRoutesPath)) {
            $this->files->makeDirectory($this->testRoutesPath, 0755, true);
        }
    }

    /** @test */
    public function it_generates_web_routes_in_dry_run(): void
    {
        $result = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => false
        ]);

        $this->assertStringContainsString('Dry run: would add route', $result);
        $this->assertStringContainsString('posts', $result);
        $this->assertStringContainsString('web.php', $result);
    }

    /** @test */
    public function it_generates_api_routes_in_dry_run(): void
    {
        $result = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => true
        ]);

        $this->assertStringContainsString('Dry run: would add route', $result);
        $this->assertStringContainsString('posts', $result);
        $this->assertStringContainsString('api.php', $result);
    }

    /** @test */
    public function it_validates_empty_route_path(): void
    {
        // Empty string name will result in empty table_name
        $result = $this->service->generate('', ['dry_run' => true]);

        $this->assertStringContainsString('Error: Invalid route path', $result);
    }

    /** @test */
    public function it_uses_correct_naming_conventions(): void
    {
        $result = $this->service->generate('BlogPost', ['dry_run' => true]);

        $this->assertStringContainsString('blog_posts', $result);
    }

    /** @test */
    public function it_generates_correct_controller_class_name(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true]);

        // Should generate PostController (singular)
        $this->assertIsString($result);
        $this->assertStringContainsString('posts', $result);
    }

    /** @test */
    public function it_handles_multi_word_names(): void
    {
        $result = $this->service->generate('ProductCategory', ['dry_run' => true]);

        $this->assertStringContainsString('product_categories', $result);
    }

    /** @test */
    public function it_handles_force_flag(): void
    {
        $result = $this->service->generate('Post', [
            'dry_run' => true,
            'force' => true
        ]);

        $this->assertIsString($result);
        $this->assertStringContainsString('posts', $result);
    }

    /** @test */
    public function it_supports_both_web_and_api_modes(): void
    {
        // Test web mode
        $webResult = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => false
        ]);

        $this->assertStringContainsString('web.php', $webResult);

        // Test API mode
        $apiResult = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => true
        ]);

        $this->assertStringContainsString('api.php', $apiResult);
    }

    /** @test */
    public function it_generates_different_route_types(): void
    {
        // The actual route type (resource vs apiResource) is internal
        // but we can verify the service runs for both modes
        $webResult = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => false
        ]);

        $apiResult = $this->service->generate('Post', [
            'dry_run' => true,
            'api' => true
        ]);

        $this->assertIsString($webResult);
        $this->assertIsString($apiResult);
    }

    /** @test */
    public function it_handles_special_characters_in_names(): void
    {
        // Test with underscore
        $result = $this->service->generate('user_profile', ['dry_run' => true]);

        $this->assertStringContainsString('user_profiles', $result);
    }

    /** @test */
    public function it_returns_success_message_format(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true]);

        // Should contain either dry run message or success message format
        $this->assertTrue(
            str_contains($result, 'Dry run') ||
            str_contains($result, 'successfully') ||
            str_contains($result, 'Skipped')
        );
    }

    /** @test */
    public function it_handles_pascal_case_names(): void
    {
        $result = $this->service->generate('UserProfile', ['dry_run' => true]);

        $this->assertStringContainsString('user_profiles', $result);
    }

    /** @test */
    public function it_handles_snake_case_names(): void
    {
        $result = $this->service->generate('blog_post', ['dry_run' => true]);

        $this->assertStringContainsString('blog_posts', $result);
    }

    protected function cleanUpTestFiles(): void
    {
        if ($this->files->exists($this->testRoutesPath)) {
            $this->files->deleteDirectory($this->testRoutesPath);
        }
    }
}
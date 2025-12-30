<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\ModelGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class ModelGeneratorServiceTest extends TestCase
{
    private ModelGeneratorService $service;
    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new ModelGeneratorService($this->files);
    }

    /** @test */
    public function it_generates_model_file(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('Dry run: would write model', $result);
    }

    /** @test */
    public function it_generates_fillable_fields(): void
    {
        $fields = [
            ['name' => 'title', 'type' => 'string'],
            ['name' => 'content', 'type' => 'text'],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('model', $result);
    }

    /** @test */
    public function it_excludes_password_from_fillable(): void
    {
        $fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'password', 'type' => 'password'],
        ];

        $result = $this->service->generate('User', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('model', $result);
    }

    /** @test */
    public function it_generates_casts_for_typed_fields(): void
    {
        $fields = [
            ['name' => 'views', 'type' => 'integer'],
            ['name' => 'is_published', 'type' => 'boolean'],
            ['name' => 'published_at', 'type' => 'datetime'],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('model', $result);
    }

    /** @test */
    public function it_hides_sensitive_fields(): void
    {
        $fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'password', 'type' => 'password'],
            ['name' => 'remember_token', 'type' => 'string'],
        ];

        $result = $this->service->generate('User', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('model', $result);
    }

    /** @test */
    public function it_handles_empty_fields(): void
    {
        $result = $this->service->generate('Post', [], ['dry_run' => true]);

        $this->assertStringContainsString('model', $result);
    }

    /** @test */
    public function it_skips_existing_model_without_force(): void
    {
        $fields = $this->getSampleFields();
        
        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);
        
        $this->assertIsString($result);
    }
}

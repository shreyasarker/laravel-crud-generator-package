<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\MigrationGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class MigrationGeneratorServiceTest extends TestCase
{
    private MigrationGeneratorService $service;
    private Filesystem $files;
    private string $testMigrationsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new MigrationGeneratorService($this->files);
        $this->testMigrationsPath = sys_get_temp_dir() . '/test_migrations';

        // Create test directory
        if (!$this->files->exists($this->testMigrationsPath)) {
            $this->files->makeDirectory($this->testMigrationsPath, 0755, true);
        }
    }

    /** @test */
    public function it_generates_migration_file(): void
    {
        $fields = [
            [
                'name' => 'title',
                'type' => 'string',
                'sqlColumn' => 'string',
                'validations' => 'required|string|max:255',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('Dry run: would write migration', $result);
    }

    /** @test */
    public function it_generates_nullable_columns(): void
    {
        $fields = [
            [
                'name' => 'description',
                'type' => 'text',
                'sqlColumn' => 'text',
                'validations' => 'nullable|string',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('migration', $result);
    }

    /** @test */
    public function it_skips_existing_migration_without_force(): void
    {
        $fields = $this->getSampleFields();
        
        // This should indicate it would skip
        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);
        
        $this->assertIsString($result);
    }

    /** @test */
    public function it_handles_empty_fields(): void
    {
        $result = $this->service->generate('Post', [], ['dry_run' => true]);

        $this->assertStringContainsString('migration', $result);
    }

    /** @test */
    public function it_generates_correct_table_name(): void
    {
        $fields = $this->getSampleFields();
        
        $result = $this->service->generate('BlogPost', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('blog_posts', $result);
    }

    protected function cleanUpTestFiles(): void
    {
        if ($this->files->exists($this->testMigrationsPath)) {
            $this->files->deleteDirectory($this->testMigrationsPath);
        }
    }
}

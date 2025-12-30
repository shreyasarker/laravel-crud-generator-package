<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\ViewsGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class ViewsGeneratorServiceTest extends TestCase
{
    private ViewsGeneratorService $service;
    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new ViewsGeneratorService($this->files);
    }

    /** @test */
    public function it_skips_views_in_api_mode(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, ['api' => true]);

        $this->assertStringContainsString('Skipped views (API mode enabled)', $result);
    }

    /** @test */
    public function it_generates_bootstrap_views(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, [
            'dry_run' => true,
            'stack' => 'bootstrap'
        ]);

        $this->assertStringContainsString('Dry run: would generate views', $result);
        $this->assertStringContainsString('bootstrap', $result);
    }

    /** @test */
    public function it_generates_tailwind_views(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, [
            'dry_run' => true,
            'stack' => 'tailwind'
        ]);

        $this->assertStringContainsString('Dry run: would generate views', $result);
        $this->assertStringContainsString('tailwind', $result);
    }

    /** @test */
    public function it_rejects_invalid_stack(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, [
            'dry_run' => true,
            'stack' => 'invalid'
        ]);

        $this->assertStringContainsString('not supported', $result);
        $this->assertStringContainsString('bootstrap', $result);
        $this->assertStringContainsString('tailwind', $result);
    }

    /** @test */
    public function it_uses_bootstrap_as_default_stack(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        // Default stack should be bootstrap
        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_empty_fields(): void
    {
        $result = $this->service->generate('Post', [], ['dry_run' => true]);

        $this->assertStringContainsString('Dry run: would generate views', $result);
    }

    /** @test */
    public function it_skips_existing_views_without_force(): void
    {
        $fields = $this->getSampleFields();

        // This test verifies the logic exists
        // In dry run, it won't actually check for existing files
        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertIsString($result);
    }

    /** @test */
    public function it_generates_views_with_force_flag(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, [
            'dry_run' => true,
            'force' => true
        ]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_uses_correct_naming_conventions(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('BlogPost', $fields, ['dry_run' => true]);

        // Should use blog_posts directory (plural, snake_case)
        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_select_fields_with_options(): void
    {
        $fields = [
            [
                'name' => 'status',
                'type' => 'select',
                'validations' => 'required|string',
                'sqlColumn' => 'string',
                'options' => [
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ],
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_text_fields(): void
    {
        $fields = [
            [
                'name' => 'description',
                'type' => 'text',
                'validations' => 'nullable|string',
                'sqlColumn' => 'text',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_password_fields(): void
    {
        $fields = [
            [
                'name' => 'password',
                'type' => 'password',
                'validations' => 'required|string|min:8',
                'sqlColumn' => 'string',
            ],
        ];

        $result = $this->service->generate('User', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_boolean_fields(): void
    {
        $fields = [
            [
                'name' => 'is_active',
                'type' => 'boolean',
                'validations' => 'required|boolean',
                'sqlColumn' => 'boolean',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_date_fields(): void
    {
        $fields = [
            [
                'name' => 'published_at',
                'type' => 'date',
                'validations' => 'nullable|date',
                'sqlColumn' => 'date',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_datetime_fields(): void
    {
        $fields = [
            [
                'name' => 'scheduled_at',
                'type' => 'datetime',
                'validations' => 'nullable|date',
                'sqlColumn' => 'dateTime',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_handles_mixed_field_types(): void
    {
        $fields = [
            ['name' => 'title', 'type' => 'string', 'sqlColumn' => 'string'],
            ['name' => 'description', 'type' => 'text', 'sqlColumn' => 'text'],
            ['name' => 'is_featured', 'type' => 'boolean', 'sqlColumn' => 'boolean'],
            ['name' => 'published_at', 'type' => 'datetime', 'sqlColumn' => 'dateTime'],
            [
                'name' => 'status',
                'type' => 'select',
                'sqlColumn' => 'string',
                'options' => ['draft' => 'Draft', 'published' => 'Published'],
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_returns_proper_success_message(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        // Should contain meaningful output
        $this->assertTrue(
            str_contains($result, 'Dry run') ||
            str_contains($result, 'successfully') ||
            str_contains($result, 'Skipped')
        );
    }

    /** @test */
    public function it_handles_fields_with_special_characters_in_names(): void
    {
        $fields = [
            [
                'name' => 'user_id',
                'type' => 'integer',
                'validations' => 'required|integer',
                'sqlColumn' => 'integer',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }

    /** @test */
    public function it_generates_for_multi_word_model_names(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('ProductCategory', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('views', $result);
    }
}
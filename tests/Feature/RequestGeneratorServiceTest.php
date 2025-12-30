<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\RequestGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class RequestGeneratorServiceTest extends TestCase
{
    private RequestGeneratorService $service;
    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new RequestGeneratorService($this->files);
    }

    /** @test */
    public function it_generates_request_file(): void
    {
        $fields = $this->getSampleFields();

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('Dry run: would write request', $result);
    }

    /** @test */
    public function it_generates_validation_rules(): void
    {
        $fields = [
            [
                'name' => 'title',
                'validations' => 'required|string|max:255',
            ],
            [
                'name' => 'email',
                'validations' => 'required|email|unique:posts',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('request', $result);
    }

    /** @test */
    public function it_generates_custom_messages(): void
    {
        $fields = [
            [
                'name' => 'title',
                'validations' => 'required|string|max:255',
            ],
        ];

        $result = $this->service->generate('Post', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('request', $result);
    }

    /** @test */
    public function it_generates_custom_attributes(): void
    {
        $fields = [
            [
                'name' => 'first_name',
                'validations' => 'required|string',
            ],
        ];

        $result = $this->service->generate('User', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('request', $result);
    }

    /** @test */
    public function it_handles_empty_fields(): void
    {
        $result = $this->service->generate('Post', [], ['dry_run' => true]);

        $this->assertStringContainsString('request', $result);
    }

    /** @test */
    public function it_extracts_min_max_values(): void
    {
        $fields = [
            [
                'name' => 'password',
                'validations' => 'required|string|min:8|max:20',
            ],
        ];

        $result = $this->service->generate('User', $fields, ['dry_run' => true]);

        $this->assertStringContainsString('request', $result);
    }
}

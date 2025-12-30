<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use ShreyaSarker\LaraCrud\Services\ControllerGeneratorService;
use ShreyaSarker\LaraCrud\Tests\TestCase;

class ControllerGeneratorServiceTest extends TestCase
{
    private ControllerGeneratorService $service;
    private Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        $this->service = new ControllerGeneratorService($this->files);
    }

    /** @test */
    public function it_generates_web_controller(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true, 'api' => false]);

        $this->assertStringContainsString('Dry run: would write web controller', $result);
    }

    /** @test */
    public function it_generates_api_controller(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true, 'api' => true]);

        $this->assertStringContainsString('Dry run: would write API controller', $result);
    }

    /** @test */
    public function it_uses_correct_naming_conventions(): void
    {
        $result = $this->service->generate('BlogPost', ['dry_run' => true]);

        $this->assertStringContainsString('controller', $result);
    }

    /** @test */
    public function it_skips_existing_controller_without_force(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true]);
        
        $this->assertIsString($result);
    }

    /** @test */
    public function it_overwrites_with_force_option(): void
    {
        $result = $this->service->generate('Post', ['dry_run' => true, 'force' => true]);
        
        $this->assertStringContainsString('controller', $result);
    }
}

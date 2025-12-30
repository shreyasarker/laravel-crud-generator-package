<?php

namespace ShreyaSarker\LaraCrud\Tests\Feature;

use ShreyaSarker\LaraCrud\Tests\TestCase;

class MakeCrudCommandTest extends TestCase
{
    /** @test */
    public function it_requires_interactive_flag(): void
    {
        $this->artisan('make:crud', ['name' => 'Post'])
            ->expectsOutput('Please use --interactive to define fields.')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_shows_error_for_both_only_and_skip(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'model',
            '--skip' => 'views',
        ])->expectsOutput('You cannot use both --only and --skip options.')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_validates_only_option(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--only' => 'invalid,another',
        ])->expectsOutput('Invalid --only option(s): invalid, another')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_validates_skip_option(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--skip' => 'invalid',
        ])->expectsOutput('Invalid --skip option(s): invalid')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_handles_dry_run_with_empty_fields(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--dry-run' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '') // Empty = no fields
        ->expectsOutput('Dry run complete (no files were written)')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_supports_api_mode(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--api' => true,
            '--dry-run' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '')
        ->expectsOutput('Dry run complete (no files were written)')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_supports_force_flag(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--force' => true,
            '--dry-run' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '')
        ->expectsOutput('Dry run complete (no files were written)')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_supports_stack_option(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--stack' => 'tailwind',
            '--dry-run' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '')
        ->expectsOutput('Dry run complete (no files were written)')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_allows_generation_to_be_canceled(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '')
        ->expectsConfirmation('Proceed to generate files?', false) // Cancel
        ->expectsOutput('No changes made. Generation canceled.')
        ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_plan_before_generation(): void
    {
        $this->artisan('make:crud', [
            'name' => 'Post',
            '--interactive' => true,
            '--dry-run' => true,
        ])
        ->expectsQuestion('Field name (leave empty to finish)', '')
        ->expectsOutputToContain('Plan:')
        ->expectsOutputToContain('Resource: Post')
        ->assertExitCode(0);
    }

    /** @test */
    public function command_is_registered(): void
    {
        $commands = array_keys($this->app->make('Illuminate\Contracts\Console\Kernel')->all());
        
        $this->assertTrue(
            in_array('make:crud', $commands),
            'The make:crud command is not registered'
        );
    }
}
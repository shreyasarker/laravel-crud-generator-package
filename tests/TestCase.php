<?php

namespace ShreyaSarker\LaraCrud\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ShreyaSarker\LaraCrud\CrudServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup if needed
    }

    protected function getPackageProviders($app): array
    {
        return [
            CrudServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Set up test environment
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get sample field data for testing
     */
    protected function getSampleFields(): array
    {
        return [
            [
                'name' => 'title',
                'type' => 'string',
                'validations' => 'required|string|max:255',
                'sqlColumn' => 'string',
            ],
            [
                'name' => 'content',
                'type' => 'text',
                'validations' => 'nullable|string',
                'sqlColumn' => 'text',
            ],
            [
                'name' => 'views',
                'type' => 'integer',
                'validations' => 'required|integer|min:0',
                'sqlColumn' => 'integer',
            ],
            [
                'name' => 'is_published',
                'type' => 'boolean',
                'validations' => 'required|boolean',
                'sqlColumn' => 'boolean',
            ],
            [
                'name' => 'published_at',
                'type' => 'datetime',
                'validations' => 'nullable|date',
                'sqlColumn' => 'dateTime',
            ],
        ];
    }

    /**
     * Clean up test files
     */
    protected function tearDown(): void
    {
        // Clean up any generated test files
        $this->cleanUpTestFiles();
        
        parent::tearDown();
    }

    /**
     * Clean up generated files
     */
    protected function cleanUpTestFiles(): void
    {
        // Override in specific tests if needed
    }
}

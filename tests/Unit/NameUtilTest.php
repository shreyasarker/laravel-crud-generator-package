<?php

namespace ShreyaSarker\LaraCrud\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShreyaSarker\LaraCrud\Utils\NameUtil;

class NameUtilTest extends TestCase
{
    /** @test */
    public function it_converts_singular_names_correctly(): void
    {
        $result = NameUtil::getNamingConvention('Post');

        $this->assertEquals('Post', $result['singular_upper']);
        $this->assertEquals('Posts', $result['plural_upper']);
        $this->assertEquals('post', $result['singular_lower']);
        $this->assertEquals('posts', $result['plural_lower']);
        $this->assertEquals('posts', $result['table_name']);
        $this->assertEquals('Post', $result['label_upper']);
        $this->assertEquals('post', $result['label_lower']);
    }

    /** @test */
    public function it_converts_plural_names_correctly(): void
    {
        $result = NameUtil::getNamingConvention('Posts');

        $this->assertEquals('Post', $result['singular_upper']);
        $this->assertEquals('Posts', $result['plural_upper']);
    }

    /** @test */
    public function it_handles_multi_word_names(): void
    {
        $result = NameUtil::getNamingConvention('BlogPost');

        $this->assertEquals('BlogPost', $result['singular_upper']);
        $this->assertEquals('BlogPosts', $result['plural_upper']);
        $this->assertEquals('blogPost', $result['singular_lower']);
        $this->assertEquals('blogPosts', $result['plural_lower']);
        $this->assertEquals('blog_posts', $result['table_name']);
        $this->assertEquals('Blog Post', $result['label_upper']);
        $this->assertEquals('blog post', $result['label_lower']);
    }

    /** @test */
    public function it_handles_snake_case_input(): void
    {
        $result = NameUtil::getNamingConvention('blog_post');

        $this->assertEquals('BlogPost', $result['singular_upper']);
        $this->assertEquals('blog_posts', $result['table_name']);
    }

    /** @test */
    public function it_handles_irregular_plurals(): void
    {
        $result = NameUtil::getNamingConvention('Person');

        $this->assertEquals('Person', $result['singular_upper']);
        $this->assertEquals('People', $result['plural_upper']);
        $this->assertEquals('people', $result['table_name']);
    }

    /** @test */
    public function it_handles_single_letter_names(): void
    {
        $result = NameUtil::getNamingConvention('A');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('singular_upper', $result);
        $this->assertArrayHasKey('plural_upper', $result);
    }
}

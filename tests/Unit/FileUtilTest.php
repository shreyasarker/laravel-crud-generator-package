<?php

namespace ShreyaSarker\LaraCrud\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShreyaSarker\LaraCrud\Utils\FileUtil;

class FileUtilTest extends TestCase
{
    /** @test */
    public function it_generates_correct_file_name(): void
    {
        $this->assertEquals('Post.php', FileUtil::getFileName('Post'));
        $this->assertEquals('BlogPost.php', FileUtil::getFileName('BlogPost'));
        $this->assertEquals('User.php', FileUtil::getFileName('User'));
    }

    /** @test */
    public function it_cleans_last_line_break(): void
    {
        $string = "Line 1\nLine 2\nLine 3\n";
        $cleaned = FileUtil::cleanLastLineBreak($string);
        
        $this->assertEquals("Line 1\nLine 2\nLine 3", $cleaned);
        $this->assertStringEndsNotWith("\n", $cleaned);
    }

    /** @test */
    public function it_handles_string_without_trailing_newline(): void
    {
        $string = "Line 1\nLine 2\nLine 3";
        $cleaned = FileUtil::cleanLastLineBreak($string);
        
        $this->assertEquals($string, $cleaned);
    }

    /** @test */
    public function it_handles_empty_string(): void
    {
        $cleaned = FileUtil::cleanLastLineBreak('');
        
        $this->assertEquals('', $cleaned);
    }

    /** @test */
    public function it_handles_multiple_trailing_newlines(): void
    {
        $string = "Line 1\n\n\n";
        $cleaned = FileUtil::cleanLastLineBreak($string);
        
        $this->assertEquals("Line 1", $cleaned);
    }
}

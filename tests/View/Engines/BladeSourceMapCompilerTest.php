<?php

namespace Laracatch\Client\Tests\View\Engines;

use Illuminate\Filesystem\Filesystem;
use Laracatch\Client\Tests\TestCase;
use Laracatch\Client\View\Engines\BladeSourceMapCompiler;
use Spatie\Snapshots\MatchesSnapshots;

class BladeSourceMapCompilerTest extends TestCase
{
    use MatchesSnapshots;

    protected $compiler;

    public function setUp(): void
    {
        parent::setUp();

        $this->compiler = new BladeSourceMapCompiler(app(Filesystem::class), 'test');
    }

    /** @test */
    public function it_should_compile_the_string_and_add_line_number_next_to_echo_instructions_and_statements()
    {
        $input = file_get_contents(__DIR__ . './../../stubs/views/blade-file.blade.php');

        $return = $this->compiler->compileString($input);

        $this->assertMatchesSnapshot($return);
    }

    /** @test */
    public function it_should_detect_the_exception_line_number()
    {
        $filename = __DIR__ . '/__snapshots__/BladeSourceMapCompilerTest__it_should_compile_the_string_and_add_line_number_next_to_echo_instructions_and_statements__1.php';

        $ret = $this->compiler->detectExceptionLineNumber($filename, 9);

        $this->assertEquals(11, $ret);
    }

}

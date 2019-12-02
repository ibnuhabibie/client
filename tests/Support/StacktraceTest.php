<?php

namespace Laracatch\Client\Tests\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laracatch\Client\Support\Stacktrace;
use Laracatch\Client\Tests\FilesystemInteraction;
use Laracatch\Client\Tests\TestCase;

class StacktraceTest extends TestCase
{
    use FilesystemInteraction;

    /** @test */
    public function it_can_convert_an_exception_to_an_array()
    {
        $stackTrace = Stacktrace::createFromThrowable($this->getException('arg1', 'arg2'), $this->app);

        $this->assertIsArray($stackTrace);
        $this->assertGreaterThan(1, count($stackTrace));
        $this->assertEquals('Laracatch\Client\Tests\Support\StacktraceTest', Arr::get($stackTrace, '0.class'));
        $this->assertEquals('getException', Arr::get($stackTrace, '0.method'));
        $this->assertEquals('method', Arr::get($stackTrace, '0.type'));
        $this->assertEquals(['arg1', 'arg2'], Arr::get($stackTrace, '0.args'));
        $this->assertEquals(32, Arr::get($stackTrace, '0.line_number'));
    }

    protected function getException($arg1, $arg2): \Throwable
    {
        try {
            throw new \Exception('Whoops!');
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    /** @test */
    public function it_can_get_a_snippet_in_the_middle_of_file()
    {
        $filepath = $this->mockFile(20);

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 10, 5);

        $this->assertEquals([
            5 => 'Line 5',
            6 => 'Line 6',
            7 => 'Line 7',
            8 => 'Line 8',
            9 => 'Line 9',
            10 => 'Line 10',
            11 => 'Line 11',
            12 => 'Line 12',
            13 => 'Line 13',
            14 => 'Line 14',
            15 => 'Line 15',
        ], $snippet);
    }

    /** @test */
    public function it_can_get_the_beginning_of_a_file()
    {
        $filepath = $this->mockFile(20);

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 1, 2);

        $this->assertEquals([
            1 => 'Line 1',
            2 => 'Line 2',
            3 => 'Line 3',
        ], $snippet);
    }

    /** @test */
    public function it_can_get_the_end_of_a_file()
    {
        $filepath = $this->mockFile(20);

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 20, 2);

        $this->assertEquals([
            18 => 'Line 18',
            19 => 'Line 19',
            20 => 'Line 20',
        ], $snippet);
    }

    /** @test */
    public function it_will_not_get_the_code_if_line_is_out_of_bounds()
    {
        $filepath = $this->mockFile(20);

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 30, 2);

        $this->assertEquals([], $snippet);
    }

    /** @test */
    public function it_will_get_the_entire_file_if_the_snippet_line_count_is_very_high()
    {
        $filepath = $this->mockFile(3);

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 2, 20);

        $this->assertEquals([
            1 => 'Line 1',
            2 => 'Line 2',
            3 => 'Line 3',
        ], $snippet);
    }

    /** @test */
    public function it_will_return_an_empty_array_for_a_non_existing_file()
    {
        $filepath = $this->path . 'this-file-does-not-exist.txt';

        $stacktrace = new Stacktrace('');

        $snippet = $stacktrace->getCode($filepath, 5, 2);

        $this->assertEquals([], $snippet);
    }
}

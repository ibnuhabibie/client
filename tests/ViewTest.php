<?php

namespace Laracatch\Client\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\View;
use Laracatch\Client\Exceptions\ViewException;

class ViewTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/stubs/views');
    }

    /** @test */
    public function it_should_detect_blade_view_exceptions()
    {
        $this->expectException(ViewException::class);

        View::make('blade-exception')->render();
    }

    /** @test */
    public function it_should_detect_the_original_line_number_in_view_exceptions()
    {
        try {
            View::make('blade-exception')->render();
        } catch (ViewException $exception) {
            $this->assertSame(3, $exception->getLine());
        }
    }

    /** @test */
    public function it_should_add_additional_blade_information_to_the_exception()
    {
        $viewData = [
            'foo' => 'bar',
            'baz' => true,
            'user' => new User(),
        ];

        try {
            View::make('blade-exception', $viewData)->render();
        } catch (ViewException $exception) {
            $this->assertSame($viewData, $exception->getData());
        }
    }

    /** @test */
    public function it_should_detect_php_view_exceptions()
    {
        $this->expectException(ViewException::class);

        View::make('php-exception')->render();
    }
}

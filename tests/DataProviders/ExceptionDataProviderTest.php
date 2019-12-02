<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\ExceptionDataProvider;
use Laracatch\Client\Tests\TestCase;

class ExceptionDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_the_name_of_the_class_that_generated_the_exception()
    {
        $errorModel = $this->makeErrorModel();

        app()->make(ExceptionDataProvider::class)->handle($errorModel, $this->app,
            new \UnexpectedValueException('Whoops'));

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals('UnexpectedValueException', Arr::get($result, 'exception_class'));
    }

    /** @test */
    public function it_should_not_add_the_name_of_the_class_if_no_instance_of_throwable_is_passed()
    {
        $errorModel = $this->makeErrorModel();

        app()->make(ExceptionDataProvider::class)->handle($errorModel, $this->app, null);

        $result = $errorModel->toArray();

        $this->assertNull(Arr::get($result, 'exception_class'));
    }
}

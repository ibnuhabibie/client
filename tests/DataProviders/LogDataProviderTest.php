<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\Collectors\LogCollector;
use Laracatch\Client\Contracts\LogCollectorContract;
use Laracatch\Client\DataProviders\LogDataProvider;
use Laracatch\Client\Tests\TestCase;

class LogDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_collected_logs_attributes_to_error_model_context()
    {
        $mock = $this->getMockBuilder(LogCollector::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();

        $this->app->instance(LogCollectorContract::class, $mock);

        $logs = ['log1', 'log2'];

        $mock->expects($this->once())
            ->method('getItems')
            ->willReturn($logs);

        $errorModel = $this->makeErrorModel();

        app()->make(LogDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($logs, Arr::get($result, 'context.logs'));
    }
}

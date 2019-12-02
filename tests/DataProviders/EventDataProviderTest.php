<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\Collectors\EventCollector;
use Laracatch\Client\Contracts\EventCollectorContract;
use Laracatch\Client\DataProviders\EventDataProvider;
use Laracatch\Client\Tests\TestCase;

class EventDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_collected_events_attributes_to_error_model_context()
    {
        $mock = $this->getMockBuilder(EventCollector::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();

        $this->app->instance(EventCollectorContract::class, $mock);

        $events = ['event1', 'event2'];

        $mock->expects($this->once())
            ->method('getItems')
            ->willReturn($events);

        $errorModel = $this->makeErrorModel();

        app()->make(EventDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($events, Arr::get($result, 'context.events'));
    }
}

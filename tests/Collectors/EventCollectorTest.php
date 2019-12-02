<?php

namespace Laracatch\Client\Tests\Collectors;

use Illuminate\Support\Facades\Event;
use Laracatch\Client\Collectors\EventCollector;
use Laracatch\Client\Tests\TestCase;

class EventCollectorTest extends TestCase
{
    protected $collector;

    public function setUp(): void
    {
        parent::setUp();

        $this->collector = new EventCollector($this->app);
        $this->collector->listen();
    }

    /** @test */
    public function it_should_collect_events()
    {
        $name = 'custom.event';
        $params = ['param1', 'param2'];

        Event::dispatch($name, $params);

        $events = $this->collector->getItems();

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals($name, $events[0]['name']);
        $this->assertEquals($params, $events[0]['params']);
        $this->assertArrayHasKey('listeners', $events[0]);
        $this->assertArrayHasKey('microtime', $events[0]);
    }

    /** @test */
    public function it_should_reset_the_collected_events()
    {
        Event::dispatch('custom.event', ['param1', 'param2']);

        $this->collector->reset();

        $this->assertEmpty($this->collector->getItems());
    }
}

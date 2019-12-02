<?php

namespace Laracatch\Client\Tests\Collectors;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Laracatch\Client\Collectors\LogCollector;
use Laracatch\Client\Tests\TestCase;
use Psr\Log\LogLevel;

class LogCollectorTest extends TestCase
{
    protected $collector;

    public function setUp(): void
    {
        parent::setUp();

        $this->collector = new LogCollector($this->app);
        $this->collector->listen();
    }

    /** @test */
    public function it_should_collect_logs()
    {
        $level = LogLevel::INFO;
        $message = 'test log';
        $context = [
            'one' => 1,
            'two' => 2
        ];

        Event::dispatch(new MessageLogged($level, $message, $context));

        $events = $this->collector->getItems();

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals($message, $events[0]['message']);
        $this->assertEquals($level, $events[0]['level']);
        $this->assertEquals($context, $events[0]['context']);
        $this->assertArrayHasKey('microtime', $events[0]);
    }

    /** @test */
    public function it_should_ignore_message_log_when_context_carries_an_exception()
    {
        Event::dispatch(
            new MessageLogged(LogLevel::ERROR, 'Whoops!', [
                'exception' => new \Exception('Whoops!')
            ])
        );

        $events = $this->collector->getItems();

        $this->assertIsArray($events);
        $this->assertEmpty($events);
    }

    /** @test */
    public function it_should_reset_the_collected_logs()
    {
        Event::dispatch(new MessageLogged(LogLevel::INFO, 'test log'));

        $this->collector->reset();

        $this->assertEmpty($this->collector->getItems());
    }
}

<?php

namespace Laracatch\Client\Tests\Collectors;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Laracatch\Client\Collectors\QueryCollector;
use Laracatch\Client\Tests\TestCase;

class QueryCollectorTest extends TestCase
{
    /** @test */
    public function it_should_collect_queries_without_query_bindings()
    {
        $this->app['config']->set('laracatch.data_providers.report_query_bindings', false);

        $collector = new QueryCollector($this->app);
        $collector->listen();

        $sql = 'SELECT * FROM table WHERE id = ?';
        $bindings = [1];
        $time = 1572960579.9467;

        Event::dispatch(new QueryExecuted($sql, $bindings, $time, $this->getConnection()));

        $events = $collector->getItems();

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals($sql, $events[0]['sql']);
        $this->assertEquals($time, $events[0]['time']);
        $this->assertEquals('ms', $events[0]['measure_unit']);
        $this->assertEquals('mysql', $events[0]['connection_name']);
    }

    /** @test */
    public function it_should_collect_queries_with_query_bindings()
    {
        $this->app['config']->set('laracatch.data_providers.report_query_bindings', true);

        $collector = new QueryCollector($this->app);
        $collector->listen();

        $sql = 'SELECT * FROM table WHERE id = ?';
        $bindings = [1];
        $time = 1572960579.9467;

        Event::dispatch(new QueryExecuted($sql, $bindings, $time, $this->getConnection()));

        $events = $collector->getItems();

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals(Str::replaceArray('?', $bindings, $sql), $events[0]['sql']);
        $this->assertEquals($time, $events[0]['time']);
        $this->assertEquals('ms', $events[0]['measure_unit']);
        $this->assertEquals('mysql', $events[0]['connection_name']);
    }

    /** @test */
    public function it_should_reset_the_collected_queries()
    {
        $collector = new QueryCollector($this->app);
        $collector->listen();

        Event::dispatch(new QueryExecuted('SELECT * FROM table WHERE id = ?', [1], 1572960579.9467,
            $this->getConnection()));

        $collector->reset();

        $this->assertEmpty($collector->getItems());
    }
}

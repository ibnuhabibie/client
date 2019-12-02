<?php

namespace Laracatch\Client\Tests\Collectors;

use Laracatch\Client\Contracts\DumpCollectorContract;
use Laracatch\Client\Tests\TestCase;

class DumpCollectorTest extends TestCase
{
    /** @var DumpCollectorContract */
    protected $collector;

    public function setUp(): void
    {
        parent::setUp();

        $this->collector = app()->make(DumpCollectorContract::class);
    }

    /** @test */
    function it_should_collect_dumps()
    {
        $dumpContent = 'test';
        $dumpArray = [1, 2, 3];

        dump($dumpContent, $dumpArray);

        $dumps = $this->collector->getItems();

        $this->assertCount(2, $dumps);
        $this->assertEquals($dumpContent, $dumps[0]['original_data']->getValue());
        $this->assertEquals($dumpArray, $dumps[1]['original_data']->getValue(true));
    }

    /** @test */
    public function it_should_reset_the_collected_dumps()
    {
        dump('test');

        $this->collector->reset();

        $this->assertEmpty($this->collector->getItems());
    }
}
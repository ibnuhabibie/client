<?php

namespace Laracatch\Client\Tests\Collectors;

use Laracatch\Client\Contracts\BreadcrumbCollectorContract;
use Laracatch\Client\Facades\Laracatch;
use Laracatch\Client\Tests\TestCase;

class BreadcrumbCollectorTest extends TestCase
{
    /** @var BreadcrumbCollectorContract */
    protected $collector;

    public function setUp(): void
    {
        parent::setUp();

        $this->collector = app()->make(BreadcrumbCollectorContract::class);
    }

    /** @test */
    function it_should_collect_breadcrumbs()
    {
        Laracatch::breadcrumb('test');

        $breadcrumbs = $this->collector->getItems();

        $this->assertCount(1, $breadcrumbs);
        $this->assertEquals('test', $breadcrumbs[0]->getMessage());
    }

    /** @test */
    function it_should_reset()
    {
        Laracatch::breadcrumb('test');

        $this->collector->reset();

        $this->assertEmpty($this->collector->getItems());
    }
}
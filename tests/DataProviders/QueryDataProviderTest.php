<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\Collectors\QueryCollector;
use Laracatch\Client\Contracts\QueryCollectorContract;
use Laracatch\Client\DataProviders\QueryDataProvider;
use Laracatch\Client\Tests\TestCase;

class QueryDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_collected_queries_attributes_to_error_model_context()
    {
        $mock = $this->getMockBuilder(QueryCollector::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();

        $this->app->instance(QueryCollectorContract::class, $mock);

        $queries = ['query1', 'query2'];

        $mock->expects($this->once())
            ->method('getItems')
            ->willReturn($queries);

        $errorModel = $this->makeErrorModel();

        app()->make(QueryDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($queries, Arr::get($result, 'context.queries'));
    }
}

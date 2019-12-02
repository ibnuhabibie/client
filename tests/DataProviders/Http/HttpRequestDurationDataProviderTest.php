<?php

namespace Laracatch\Client\Tests\DataProviders\Http;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\Http\HttpRequestDurationDataProvider;
use Laracatch\Client\Tests\TestCase;

class HttpRequestDurationDataProviderTest extends TestCase
{
    protected $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = app()->make(HttpRequestDurationDataProvider::class);
    }

    /** @test */
    public function it_should_add_perfomance_time_to_error_model()
    {
        $seen_at = microtime(true);
        $duration = 1.0;
        $start = $seen_at - $duration;

        define('LARAVEL_START', $start);

        $errorModel = $this->makeErrorModel('message', $seen_at);

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'performance.time'));
        $this->assertEquals($start, Arr::get($result, 'performance.time.start'));
        $this->assertEquals($seen_at, Arr::get($result, 'performance.time.end'));
        $this->assertEquals($duration, Arr::get($result, 'performance.time.duration'));
        $this->assertEquals('1 second', Arr::get($result, 'performance.time.human_duration'));
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function it_should_convert_seconds_to_human_readable_format($seconds, $human)
    {
        $this->assertEquals($human, HttpRequestDurationDataProvider::secondsToTimeString($seconds));
    }

    public function provider()
    {
        return [
            [0.0001, '0 ms'],
            [0.0014, '1 ms'],
            [0.0016, '2 ms'],
            [0.1, '100 ms'],
            [1, '1 second'],
            [60, '1 minute'],
            [60 * 10, '10 minutes'],
            [60 * 60, '1 hour'],
            [60 * 60 + 60 * 30, '1.5 hours'],
            [60 * 60 * 10, '10 hours'],
            [60 * 60 * 24, '24 hours'],
            [60 * 60 * 48, '48 hours']
        ];
    }
}

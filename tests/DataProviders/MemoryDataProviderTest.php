<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\MemoryDataProvider;
use Laracatch\Client\Tests\TestCase;

class MemoryDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_performance_memory_to_error_model()
    {
        $errorModel = $this->makeErrorModel();

        app()->make(MemoryDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertIsInt(Arr::get($result, 'performance.memory.peak_usage'));
        $this->assertIsString(Arr::get($result, 'performance.memory.human_peak_usage'));
    }

    /**
     * @dataProvider provider
     * @test
     */
    public function it_should_convert_bytes_to_human_readable_format($bytes, $human)
    {
        $this->assertEquals($human, MemoryDataProvider::bytesToString($bytes));
    }

    public function provider()
    {
        return [
            [600, '600 bytes'],
            [1024, '1.00 KB'],
            [1024 + 34, '1.03 KB'],
            [1024 + 39, '1.04 KB'],
            [1024 * 1024, '1.00 MB'],
            [1024 * 1024 * 1024, '1.00 GB'],
        ];
    }
}

<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\EnvironmentDataProvider;
use Laracatch\Client\Tests\TestCase;

class EnvironmentDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_environment_attributes_to_error_model()
    {
        $errorModel = $this->makeErrorModel();

        app()->make(EnvironmentDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals('testing', Arr::get($result, 'environment'));
        $this->assertArrayHasKey('application_path', $result);
        $this->assertEquals(true, Arr::get($result, 'console'));
    }
}

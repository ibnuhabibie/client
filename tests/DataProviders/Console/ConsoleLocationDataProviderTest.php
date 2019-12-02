<?php

namespace Laracatch\Client\Tests\DataProviders\Console;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\Console\ConsoleLocationDataProvider;
use Laracatch\Client\Tests\TestCase;

class ConsoleLocationDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_the_running_script_to_error_model()
    {
        $errorModel = $this->makeErrorModel();

        $this->app->make(ConsoleLocationDataProvider::class)->handle($errorModel, $this->app);

        $this->assertStringStartsWith(Arr::first($_SERVER['argv']), Arr::get($errorModel->toArray(), 'location'));
    }
}

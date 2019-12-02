<?php

namespace Laracatch\Client\Tests\Commands;

use Laracatch\Client\Contracts\StorageContract;
use Laracatch\Client\Tests\TestCase;

class ClearCommandTest extends TestCase
{
    /** @test */
    public function it_clears_the_storage_if_it_exists()
    {
        $stub = $this->getMockBuilder(StorageContract::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('clear');

        $this->app->instance(StorageContract::class, $stub);

        $this->artisan('laracatch:clear')
            ->expectsOutput('Storage cleared!')
            ->assertExitCode(0);
    }
}

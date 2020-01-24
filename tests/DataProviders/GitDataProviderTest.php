<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\GitDataProvider;
use Laracatch\Client\Tests\TestCase;

class GitDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_not_add_git_information_to_the_error_model_if_git_collection_is_disabled()
    {
        $this->app['config']->set('laracatch.data_providers.collect_git_information', false);

        $errorModel = $this->makeErrorModel();

        app()->make(GitDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertNull(Arr::get($result, 'context.git'));
    }

    /** @test */
    public function it_should_add_git_information_to_the_error_model_if_git_collection_is_disabled()
    {
        $this->app['config']->set('laracatch.data_providers.collect_git_information', true);

        $errorModel = $this->makeErrorModel();

        app()->make(GitDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'context.git.is_initialized'));
        $this->assertTrue(Arr::has($result, 'context.git.hash'));
        $this->assertTrue(Arr::has($result, 'context.git.message'));
        $this->assertTrue(Arr::has($result, 'context.git.tag'));
        $this->assertTrue(Arr::has($result, 'context.git.remote'));
        $this->assertTrue(Arr::has($result, 'context.git.is_dirty'));
    }
}

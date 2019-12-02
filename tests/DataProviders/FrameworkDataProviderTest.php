<?php

namespace Laracatch\Client\Tests\DataProviders;

use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\FrameworkDataProvider;
use Laracatch\Client\Tests\TestCase;

class FrameworkDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_framework_and_language_attributes_to_error_model_context()
    {
        $errorModel = $this->makeErrorModel();

        app()->make(FrameworkDataProvider::class)->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'context.framework'));
        $this->assertEquals($this->app->version(), Arr::get($result, 'context.framework.laravel_version'));
        $this->assertEquals($this->app->getLocale(), Arr::get($result, 'context.framework.laravel_locale'));
        $this->assertEquals($this->app->configurationIsCached(),
            Arr::get($result, 'context.framework.laravel_config_cache'));
        $this->assertEquals(phpversion(), Arr::get($result, 'context.framework.php_version'));
    }
}

<?php

namespace Laracatch\Client\Tests\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Laracatch\Client\Http\Middleware\LaracatchEnabled;
use Laracatch\Client\Tests\TestCase;

class LaracatchEnabledTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('middleware-test', function () {
            return 'success';
        })->middleware([LaracatchEnabled::class]);
    }

    /** @test */
    public function it_returns_404_when_debug_mode_disabled()
    {
        $this->app['config']->set('app.debug', false);

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_when_debug_mode_is_enabled_but_laracatch_is_disabled()
    {
        $this->app['config']->set('app.debug', true);
        $this->app['config']->set('laracatch.enabled', false);

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_in_testing_environment()
    {
        $this->app['config']->set('app.debug', true);
        $this->app['config']->set('laracatch.enabled', true);

        $this->get('middleware-test')->assertStatus(404);
    }

    /** @test */
    public function it_returns_ok_in_any_other_case()
    {
        $this->app['config']->set('app.debug', true);
        $this->app['config']->set('laracatch.enabled', true);

        $this->app->detectEnvironment(function () {
            return 'development';
        });

        $this->get('middleware-test')->assertStatus(200);
    }
}

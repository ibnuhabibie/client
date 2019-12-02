<?php

namespace Laracatch\Client\Tests\DataProviders\Http;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laracatch\Client\DataProviders\Http\HttpRequestUserDataProvider;
use Laracatch\Client\Tests\TestCase;

class HttpRequestUserDataProviderTest extends TestCase
{
    protected $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = app()->make(HttpRequestUserDataProvider::class);
    }

    /** @test */
    public function it_should_add_the_authenticated_user_to_error_model_context()
    {
        $user = new User();
        $user->forceFill([
            'id' => 1,
            'email' => 'taylor@laravel.com',
        ]);

        $request = $this->createRequest('GET', '/route');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'context.user'));
        $this->assertEquals($user->toArray(), Arr::get($result, 'context.user'));
    }

    /** @test */
    public function if_the_authenticated_user_model_has_a_toLaracatch_method_it_will_be_used_to_collect_user_data()
    {
        $user = new class extends User
        {
            public function toLaracatch()
            {
                return ['id' => $this->id];
            }
        };

        $user->forceFill([
            'id' => 1,
            'email' => 'taylor@laravel.com',
        ]);

        $request = $this->createRequest('GET', '/route');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertSame(['id' => $user->id], Arr::get($result, 'context.user'));
    }

    /** @test */
    public function it_the_authenticated_user_model_has_no_matching_method_it_will_return_no_user_data()
    {
        $user = new class
        {
        };

        $request = $this->createRequest('GET', '/route');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals([], Arr::get($result, 'context.user'));
    }

    /** @test */
    public function it_the_authenticated_user_model_is_broken_it_will_return_no_user_data()
    {
        $user = new class extends User
        {
            protected $appends = ['invalid'];
        };

        $request = $this->createRequest('GET', '/route', [], ['cookie' => 'noms']);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals([], Arr::get($result, 'context.user'));
    }
}

<?php

namespace Laracatch\Client\Tests\DataProviders\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Laracatch\Client\DataProviders\Http\HttpLocationDataProvider;
use Laracatch\Client\Tests\TestCase;

class HttpLocationDataProviderTest extends TestCase
{
    /** @test */
    public function it_should_add_location_uri_and_method_attributes_to_error_model()
    {
        $route = Route::get('/route/', function () {
        })->name('routeName');

        $request = $this->createRequest('GET', '/route');

        $route->bind($request);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        app()->make(HttpLocationDataProvider::class)->handle($errorModel, $this->app);

        $this->assertEquals('http://localhost/route', Arr::get($errorModel->toArray(), 'location'));
        $this->assertEquals('GET', Arr::get($errorModel->toArray(), 'method'));
    }
}

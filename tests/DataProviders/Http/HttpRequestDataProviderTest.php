<?php

namespace Laracatch\Client\Tests\DataProviders\Http;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Laracatch\Client\DataProviders\Http\HttpRequestDataProvider;
use Laracatch\Client\Handler\Context\RequestContext;
use Laracatch\Client\Tests\FilesystemInteraction;
use Laracatch\Client\Tests\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class HttpRequestDataProviderTest extends TestCase
{
    use FilesystemInteraction {
        setUp as public traitSetUp;
    }

    protected $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->traitSetUp();

        $this->provider = app()->make(HttpRequestDataProvider::class);
    }

    /** @test */
    public function it_should_add_route_to_error_model_context()
    {
        $route = Route::get('/route/{parameter}/{otherParameter}', function () {
        })
            ->name('routeName')
            ->middleware(['test.middleware']);

        $request = $this->createRequest('GET', '/route/value/second');

        $route->bind($request);

        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'context.route'));
        $this->assertEquals('routeName', Arr::get($result, 'context.route.route'));
        $this->assertEquals('Closure', Arr::get($result, 'context.route.controllerAction'));
        $this->assertEquals(['test.middleware'], Arr::get($result, 'context.route.middleware'));
    }

    /** @test */
    public function it_should_add_request_headers_to_error_model_context()
    {
        $server = [
            'HTTP_HOST' => 'example.com'
        ];

        $request = $this->createRequest('GET', '/route', [], [], [], $server);

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertTrue(Arr::has($result, 'context.headers.host'));
    }

    /** @test */
    public function it_should_add_cookies_to_error_model_context()
    {
        $cookies = ['cookie-key-1' => 'cookie-value-1'];

        $request = $this->createRequest('GET', '/route', [], $cookies);

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertCount(1, Arr::get($result, 'context.cookies'));
    }

    /** @test */
    public function it_should_add_session_data_to_error_model_context()
    {
        $laravelRequest = Request::createFromBase($this->createRequest('GET', '/route'));
        $laravelRequest->setLaravelSession($this->app['session']);

        $laravelRequest->session()->put('session-key-1', 'session-value-1');

        $this->app->instance(Request::class, $laravelRequest);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertCount(1, Arr::get($result, 'context.session'));
    }

    /** @test */
    public function it_should_add_request_data_to_error_model_context()
    {
        $server = [
            'HTTP_HOST' => 'example.com',
            'REMOTE_ADDR' => '1.2.3.4',
            'SERVER_PORT' => '80'
        ];

        $request = $this->createRequest('GET', '/route', [], [], [], $server);

        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals('http://localhost/route', Arr::get($result, 'context.request.url'));
        $this->assertEquals('1.2.3.4', Arr::get($result, 'context.request.ip'));
        $this->assertEquals('GET', Arr::get($result, 'context.request.method'));
        $this->assertTrue(Arr::has($result, 'context.request.useragent'));
    }

    /** @test */
    public function it_should_add_query_string_and_body_and_files_to_the_error_model_request_data_context()
    {
        $get = ['get-key-1' => 'get-value-1'];

        $post = ['post-key-1' => 'post-value-1'];

        $request = [];
        $cookies = [];

        $files = [
            'file-one' => new UploadedFile(
                tap($this->path . 'file1.txt', function ($path) {
                    $this->filesystem->put($path, Str::random());
                }),
                'file-name.txt',
                'text/plain',
                UPLOAD_ERR_OK
            ),
            'file-two' => new UploadedFile(
                tap($this->path . 'file2.txt', function ($path) {
                    $this->filesystem->put($path, Str::random());
                }),
                'file-name.txt',
                'text/plain',
                UPLOAD_ERR_OK
            ),
        ];

        $server = [
            'HTTP_HOST' => 'example.com',
            'REMOTE_ADDR' => '1.2.3.4',
            'SERVER_PORT' => '80',
            'REQUEST_METHOD' => 'POST'
        ];

        $content = 'my content';

        $laravelRequest = Request::createFromBase(new SymfonyRequest($get, $post, $request, $cookies, $files, $server,
            $content));

        $this->app->instance(Request::class, $laravelRequest);

        $errorModel = $this->makeErrorModel();

        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();

        $this->assertIsArray($result);
        $this->assertEquals($get, Arr::get($result, 'context.request_data.query_string'));
        $this->assertInstanceOf(ParameterBag::class, Arr::get($result, 'context.request_data.body'));

        $this->assertTrue(Arr::has($result, 'context.request_data.files'));
        $this->assertCount(2, Arr::get($result, 'context.request_data.files'));
    }

    /** @test */
    public function it_should_anonymize_client_ip()
    {
        $request = $this->createRequest('GET', '/route', [], [], [], ['REMOTE_ADDR' => '1.2.3.4',]);
        $this->app->instance(Request::class, $request);

        $errorModel = $this->makeErrorModel();
        $this->provider->handle($errorModel, $this->app);

        $result = $errorModel->toArray();
        $this->assertEquals('1.2.3.4', Arr::get($result, 'context.request.ip'));

        $this->app->make('config')->set('laracatch.data_providers.anonymize_client_ip', true);

        $request = $this->createRequest('GET', '/route', [], [], [], ['REMOTE_ADDR' => '1.2.3.4',]);
        $this->app->instance(Request::class, $request);

        $newErrorModel = $this->makeErrorModel();
        $this->provider->handle($newErrorModel, $this->app);
        $newResult = $newErrorModel->toArray();

        $this->assertNull(Arr::get($newResult, 'context.request.ip'));
    }
}

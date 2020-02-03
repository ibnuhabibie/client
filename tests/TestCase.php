<?php

namespace Laracatch\Client\Tests;

use Illuminate\Http\Request;
use Laracatch\Client\LaracatchServiceProvider;
use Laracatch\Client\Models\ErrorModel;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\VarDumper\VarDumper;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        VarDumper::setHandler(function() {});
    }

    protected function getPackageProviders($app)
    {
        return [LaracatchServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laracatch.unallowed_environments', []);
        $app['config']->set('laracatch.enabled', true);
    }

    protected function createRequest(
        $method,
        $uri,
        $parameters = [],
        $cookies = [],
        $files = [],
        $server = [],
        $content = null
    ) {
        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        $symfonyRequest = SymfonyRequest::create(
            $this->prepareUrlForRequest($uri),
            $method,
            $parameters,
            $cookies,
            $files,
            array_replace($this->serverVariables, $server),
            $content
        );

        return Request::createFromBase($symfonyRequest);
    }

    protected function makeErrorModel($message = 'message', $seen_at = null)
    {
        return new ErrorModel($message, $seen_at ?: microtime(true));
    }
}

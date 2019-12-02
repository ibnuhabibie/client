<?php

namespace Laracatch\Client;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\DataProviderHandlerContract;
use Laracatch\Client\DataProviders\BreadcrumbDataProvider;
use Laracatch\Client\DataProviders\Console\ConsoleLocationDataProvider;
use Laracatch\Client\DataProviders\DumpDataProvider;
use Laracatch\Client\DataProviders\EnvironmentDataProvider;
use Laracatch\Client\DataProviders\EventDataProvider;
use Laracatch\Client\DataProviders\ExceptionDataProvider;
use Laracatch\Client\DataProviders\FrameworkDataProvider;
use Laracatch\Client\DataProviders\GitDataProvider;
use Laracatch\Client\DataProviders\Http\HttpLocationDataProvider;
use Laracatch\Client\DataProviders\Http\HttpRequestDataProvider;
use Laracatch\Client\DataProviders\Http\HttpRequestDurationDataProvider;
use Laracatch\Client\DataProviders\Http\HttpRequestUserDataProvider;
use Laracatch\Client\DataProviders\LogDataProvider;
use Laracatch\Client\DataProviders\MemoryDataProvider;
use Laracatch\Client\DataProviders\QueryDataProvider;
use Laracatch\Client\DataProviders\SolutionDataProvider;
use Laracatch\Client\DataProviders\ViewDataProvider;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class DataProviderHandler implements DataProviderHandlerContract
{
    /** @var array */
    protected $consoleProviders = [
        BreadcrumbDataProvider::class,
        DumpDataProvider::class,
        EnvironmentDataProvider::class,
        EventDataProvider::class,
        ExceptionDataProvider::class,
        FrameworkDataProvider::class,
        LogDataProvider::class,
        MemoryDataProvider::class,
        QueryDataProvider::class,
        SolutionDataProvider::class,
        ViewDataProvider::class,
        GitDataProvider::class,

        ConsoleLocationDataProvider::class,
    ];

    /** @var array */
    protected $httpProviders = [
        BreadcrumbDataProvider::class,
        DumpDataProvider::class,
        EnvironmentDataProvider::class,
        EventDataProvider::class,
        ExceptionDataProvider::class,
        FrameworkDataProvider::class,
        LogDataProvider::class,
        MemoryDataProvider::class,
        QueryDataProvider::class,
        SolutionDataProvider::class,
        ViewDataProvider::class,
        GitDataProvider::class,

        HttpLocationDataProvider::class,
        HttpRequestDataProvider::class,
        HttpRequestDurationDataProvider::class,
        HttpRequestUserDataProvider::class,
    ];

    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Push a new HTTP provider to the list.
     *
     * @param DataProviderContract $provider
     *
     * @return void
     */
    public function pushHttpProvider(DataProviderContract $provider): void
    {
        $this->httpProviders[] = $provider;
    }

    /**
     * Push a new console provider to the list.
     *
     * @param DataProviderContract $provider
     *
     * @return void
     */
    public function pushConsoleProvider(DataProviderContract $provider): void
    {
        $this->consoleProviders[] = $provider;
    }

    /**
     * Resolve data for HTTP requests.
     *
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return ErrorModel
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveHttpData(ErrorModel $errorModel, Throwable $throwable = null): ErrorModel
    {
        return $this->resolve($this->httpProviders, $errorModel, $throwable);
    }

    /**
     * Resolve the error model data using the given providers.
     *
     * @param array $providers
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return ErrorModel
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolve(array $providers, ErrorModel $errorModel, Throwable $throwable = null): ErrorModel
    {
        foreach ($providers as $provider) {
            $this->handle($provider, $errorModel, $throwable);
        }

        return $errorModel;
    }

    /**
     * @param $provider
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function handle($provider, ErrorModel $errorModel, Throwable $throwable = null)
    {
        return $this->app->make($provider)->handle($errorModel, $this->app, $throwable);
    }

    /**
     * Resolve data for console requests.
     *
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return ErrorModel
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveConsoleData(ErrorModel $errorModel, Throwable $throwable = null): ErrorModel
    {
        return $this->resolve($this->consoleProviders, $errorModel, $throwable);
    }
}
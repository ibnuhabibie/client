<?php

namespace Laracatch\Client;

use Illuminate\Foundation\Application;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\PhpEngine;
use Laracatch\Client\Client\Client;
use Laracatch\Client\Collectors\Breadcrumbs\BreadcrumbCollector;
use Laracatch\Client\Collectors\Dumps\DumpCollector;
use Laracatch\Client\Collectors\EventCollector;
use Laracatch\Client\Collectors\Git\GitCollector;
use Laracatch\Client\Collectors\LogCollector;
use Laracatch\Client\Collectors\QueryCollector;
use Laracatch\Client\Commands\ClearCommand;
use Laracatch\Client\Contracts\BreadcrumbCollectorContract;
use Laracatch\Client\Contracts\DataProviderHandlerContract;
use Laracatch\Client\Contracts\DefinedErrorHandlerContract;
use Laracatch\Client\Contracts\DumpCollectorContract;
use Laracatch\Client\Contracts\EventCollectorContract;
use Laracatch\Client\Contracts\GitCollectorContract;
use Laracatch\Client\Contracts\LogCollectorContract;
use Laracatch\Client\Contracts\QueryCollectorContract;
use Laracatch\Client\Contracts\StorageContract;
use Laracatch\Client\Facades\Laracatch as LaracatchFacade;
use Laracatch\Client\Http\Middleware\LaracatchEnabled;
use Laracatch\Client\Storage\FilesystemStorage;
use Laracatch\Client\Storage\PdoStorage;
use Laracatch\Client\Storage\RedisStorage;
use Laracatch\Client\View\Engines\LaracatchCompilerEngine;
use Laracatch\Client\View\Engines\LaracatchLegacyCompilerEngine;
use Laracatch\Client\View\Engines\LaracatchPhpEngine;
use Laracatch\Client\ViewComposers\ErrorPageComposer;
use Laracatch\Client\ViewComposers\NavigatorPageComposer;
use Whoops\Handler\HandlerInterface;

class LaracatchServiceProvider extends ServiceProvider
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        if (! $this->app['config']->get('laracatch.enabled')) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/laracatch.php' => config_path('laracatch.php')], 'config');
        }

        if ($this->app['config']->get('laracatch.collectors.dumps')) {
            $this->app->make(DumpCollectorContract::class)->listen();
        }

        if ($this->app['config']->get('laracatch.collectors.logs')) {
            $this->app->make(LogCollectorContract::class)->listen();
        }

        if ($this->app['config']->get('laracatch.collectors.queries')) {
            $this->app->make(QueryCollectorContract::class)->listen();
        }

        if ($this->app['config']->get('laracatch.collectors.events')) {
            $this->app->make(EventCollectorContract::class)->listen();
        }

        $this->registerViewEngines();

        $this->app->queue->looping(function () {
            if ($this->app['config']->get('laracatch.collectors.dumps')) {
                $this->app->make(DumpCollectorContract::class)->reset();
            }

            if ($this->app['config']->get('laracatch.collectors.breadcrumbs')) {
                $this->app->make(BreadcrumbCollector::class)->reset();
            }

            if ($this->app['config']->get('laracatch.collectors.logs')) {
                $this->app->make(LogCollectorContract::class)->reset();
            }

            if ($this->app['config']->get('laracatch.collectors.queries')) {
                $this->app->make(QueryCollectorContract::class)->reset();
            }

            if ($this->app['config']->get('laracatch.collectors.events')) {
                $this->app->make(EventCollectorContract::class)->reset();
            }
        });

        $this->app->view->composer('laracatch::errorPage', ErrorPageComposer::class);
        $this->app->view->composer('laracatch::navigator', NavigatorPageComposer::class);

        if (! $this->app->runningInConsole()) {
            $this->bindRoutes();
        }
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laracatch');
        $this->mergeConfigFrom(__DIR__ . '/../config/laracatch.php', 'laracatch');

        if (! $this->app['config']->get('laracatch.enabled')) {
            return;
        }

        $this->registerHandler();
        $this->registerDataProvider();
        $this->registerDefinedErrorHandler();
        $this->registerWhoopsHandler();
        $this->registerCollectors();
        $this->registerClient();
        $this->registerFacade();

        if ($this->app['config']->get('laracatch.storage.enabled')) {
            $this->registerStorageClass();
            $this->registerRecorder();
            $this->registerCommands();
        }
    }

    /**
     * @return void
     */
    protected function bindRoutes(): void
    {
        $this->app['router']->group([
            'prefix' => $this->app['config']->get('laracatch.route_prefix', '_laracatch'),
            'middleware' => [LaracatchEnabled::class]
        ], function () {
            // don't use array notation, which is not compatible with L5.5 and L.5.6
            $this->app['router']->get('navigator', '\Laracatch\Client\Http\Controllers\NavigatorController');

            $this->app['router']->get('errors', '\Laracatch\Client\Http\Controllers\ErrorApiController@index');
            $this->app['router']->get('errors/{id}', '\Laracatch\Client\Http\Controllers\ErrorApiController@show');
            $this->app['router']->delete('errors', '\Laracatch\Client\Http\Controllers\ErrorApiController@clear');

            $this->app['router']->post('share', '\Laracatch\Client\Http\Controllers\ShareErrorController');
        });
    }

    /**
     * Register the error handler.
     *
     * @return void
     */
    protected function registerHandler(): void
    {
        $this->app->bind('laracatch.handler', static function (Application $app) {
            return new LaracatchHandler($app);
        });
    }

    /**
     * Register the data provider.
     *
     * @return void
     */
    protected function registerDataProvider(): void
    {
        $this->app->bind(DataProviderHandlerContract::class, static function (Application $app) {
            return new DataProviderHandler($app);
        });
    }

    /**
     * Register Laracatch as a handler for whoops errors.
     *
     * @return void
     */
    protected function registerWhoopsHandler(): void
    {
        $this->app->bind(HandlerInterface::class, static function (Application $app) {
            return new LaracatchWhoopsHandler($app->make('laracatch.handler'));
        });
    }

    /**
     * Register the data provider.
     *
     * @return void
     */
    protected function registerDefinedErrorHandler(): void
    {
        $this->app->bind(DefinedErrorHandlerContract::class, static function (Application $app) {
            return new DefinedErrorHandler($app);
        });
    }

    /**
     * Register the collectors we use to generate our data.
     *
     * @return void
     */
    protected function registerCollectors(): void
    {
        $this->app->singleton(QueryCollectorContract::class, static function (Application $app) {
            return new QueryCollector($app);
        });

        $this->app->singleton(LogCollectorContract::class, static function (Application $app) {
            return new LogCollector($app);
        });

        $this->app->singleton(EventCollectorContract::class, static function (Application $app) {
            return new EventCollector($app);
        });

        $this->app->singleton(BreadcrumbCollectorContract::class, static function (Application $app) {
            return new BreadcrumbCollector($app);
        });

        $this->app->singleton(DumpCollectorContract::class, static function (Application $app) {
            return new DumpCollector($app);
        });

        $this->app->singleton(GitCollectorContract::class, static function (Application $app) {
            return new GitCollector($app);
        });
    }

    /**
     * Register the share API client.
     *
     * @return void
     */
    protected function registerClient(): void
    {
        $this->app->singleton('laracatch.client', function () {
            return new Client(
                $this->app['config']->get('laracatch.share_url'),
                10
            );
        });

        $this->app->alias('laracatch.client', Client::class);
    }

    /**
     * Register the Laracatch facade.
     *
     * @return void
     */
    protected function registerFacade(): void
    {
        $this->app->singleton('laracatch.facade', static function () {
            return new Laracatch();
        });

        $this->app->alias('laracatch.facade', LaracatchFacade::class);
    }

    /**
     * Register our storage strategy.
     *
     * @return void
     */
    protected function registerStorageClass(): void
    {
        $this->app->singleton(StorageContract::class, function () {
            $driver = $this->app['config']->get('laracatch.storage.driver');

            switch ($driver) {
                case 'pdo':
                    $connection = $this->app['config']->get('laracatch.storage.connection');

                    $table = $this->app['db']->getTablePrefix() . 'laracatch';
                    $pdo = $this->app['db']->connection($connection)->getPdo();
                    $storageClass = new PdoStorage($pdo, $table);
                    break;

                case 'redis':
                    $this->app->register(RedisServiceProvider::class);

                    $connection = $this->app['config']->get('laracatch.storage.connection');
                    $client = $this->app['redis']->connection($connection);

                    if (is_a($client, 'Illuminate\Redis\Connections\Connection', false)) {
                        $client = $client->client();
                    }

                    $storageClass = new RedisStorage($client);
                    break;

                case 'file':
                default:
                    $path = $this->app['config']->get('laracatch.storage.path');
                    $storageClass = new FilesystemStorage($this->app['files'], $path);

                    break;
            }

            return $storageClass;
        });
    }

    /**
     * Register the exception log handler.
     *
     * @return void
     */
    protected function registerRecorder(): void
    {
        $this->app['events']->listen('Illuminate\Log\Events\MessageLogged', function ($log) {
            if (isset($log->context['exception']) && ($log->context['exception'] instanceof \Throwable)) {
                $errorModel = $this->app->make('laracatch.handler')->buildFromThrowable($log->context['exception']);

                $this->app->make(StorageContract::class)->save($errorModel->toArray());
            }
        });
    }

    /**
     * Register the Laracatch view handlers.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerViewEngines(): void
    {
        if (! $this->hasCustomViewEnginesRegistered()) {
            return;
        }

        $this->app->make('view.engine.resolver')->register('php', static function () {
            return new LaracatchPhpEngine;
        });

        $this->app->make('view.engine.resolver')->register('blade', function () {
            if ($this->detectLaravelVersion(['5', '6'])) {
                return new LaracatchLegacyCompilerEngine($this->app['blade.compiler']);
            }

            return new LaracatchCompilerEngine($this->app['blade.compiler']);
        });
    }

    /**
     * Check whether custom view engines are registered.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function hasCustomViewEnginesRegistered(): bool
    {
        $resolver = $this->app->make('view.engine.resolver');

        if (! $resolver->resolve('php') instanceof PhpEngine) {
            return false;
        }

        if (! $resolver->resolve('blade') instanceof CompilerEngine) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->app->bind('command.laracatch:clear', ClearCommand::class);

        $this->commands(['command.laracatch:clear']);
    }

    /**
     * Determine what version of Laravel we're dealing with.
     */
    protected function detectLaravelVersion(array $versions): bool
    {
        return Str::startsWith(Application::VERSION, $versions);
    }
}

<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\DefinedErrorHandler;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class SolutionDataProvider implements DataProviderContract
{

    /**
     * @var DefinedErrorHandler
     */
    protected $definedErrorHandler;

    /**
     * @param DefinedErrorHandler $definedErrorHandler
     */
    public function __construct(DefinedErrorHandler $definedErrorHandler)
    {
        $this->definedErrorHandler = $definedErrorHandler;
    }

    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     * @return mixed|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        if ( ! $throwable)
        {
            return;
        }

        $solutions = $this->definedErrorHandler->resolveSolutions($throwable);

        $errorModel->setSolutions($solutions);
    }
}
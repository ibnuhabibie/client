<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class EnvironmentDataProvider implements DataProviderContract
{
    /**
     * Handle providing environment data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setEnvironmentAttributes($app->environment(), base_path(), $app->runningInConsole());
    }
}

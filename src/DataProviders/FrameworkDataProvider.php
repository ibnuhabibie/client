<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class FrameworkDataProvider implements DataProviderContract
{
    /**
     * Handle providing framework data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setContextFrameworkData(
            $app->version(),
            $app->getLocale(),
            $app->configurationIsCached(),
            phpversion()
        );
    }
}

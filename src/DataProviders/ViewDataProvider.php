<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Exceptions\ViewException;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class ViewDataProvider implements DataProviderContract
{

    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     * @return mixed|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        if ( ! $throwable instanceof ViewException)
        {
            return;
        }

        if ( ! $app['config']->get('laracatch.data_providers.report_view_data'))
        {
            return;
        }

        $errorModel->setContextView($throwable);
    }
}
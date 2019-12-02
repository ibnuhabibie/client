<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\GitCollectorContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class GitDataProvider implements DataProviderContract
{
    /**
     * Handle providing git data to the error model.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        if ( ! $app['config']->get('laracatch.data_providers.collect_git_information')) {
            return;
        }

        /** @var GitCollectorContract $collector */
        $collector = $app->make(GitCollectorContract::class);
        $collector->collect();

        $errorModel->setContextGit($collector->getItems());
    }
}
<?php

namespace Laracatch\Client\Contracts;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

interface DataProviderContract
{
    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null);
}

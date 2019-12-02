<?php

namespace Laracatch\Client\DataProviders\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class HttpLocationDataProvider implements DataProviderContract
{
    /**
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        /**
         * @var $request Request
         */
        $request = $app->make(Request::class);

        $errorModel->setLocationAttributes($request->getUri(), $request->getMethod());
    }
}

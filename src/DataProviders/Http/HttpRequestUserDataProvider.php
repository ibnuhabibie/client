<?php

namespace Laracatch\Client\DataProviders\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class HttpRequestUserDataProvider implements DataProviderContract
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
        $userData = $this->resolveUserData($request);

        $errorModel->setContextUser($userData);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function resolveUserData(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return [];
        }

        try {
            if (method_exists($user, 'toLaracatch')) {
                return $user->toLaracatch();
            }

            if (method_exists($user, 'toArray')) {
                return $user->toArray();
            }
        } catch (\Throwable $e) {
        }

        return [];
    }
}

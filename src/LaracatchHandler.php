<?php

namespace Laracatch\Client;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Laracatch\Client\Contracts\DataProviderHandlerContract;
use Laracatch\Client\Models\ErrorModel;
use Laracatch\Client\Support\Stacktrace;
use Throwable;

class LaracatchHandler
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * LaracatchHandler constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function buildFromThrowable(Throwable $throwable)
    {
        $errorModel = new ErrorModel($throwable->getMessage(), microtime(true));

        $errorModel->setStacktrace(Stacktrace::createFromThrowable($throwable, $this->app));

        return $this->buildModel($errorModel, $throwable);
    }

    /**
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function buildModel(ErrorModel $errorModel, Throwable $throwable = null)
    {
        $dataProvider = $this->app->make(DataProviderHandlerContract::class);

        if ($this->app->runningInConsole()) {
            return $dataProvider->resolveConsoleData($errorModel, $throwable);
        }

        return $dataProvider->resolveHttpData($errorModel, $throwable);
    }

    /**
     * @param $message
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function buildFromMessage($message)
    {
        $errorModel = new ErrorModel($message, microtime(true));
        $errorModel->setStacktrace(Stacktrace::create($this->app));

        return $this->buildModel($errorModel);
    }

    /**
     * @param ErrorModel $errorModel
     * @param string $defaultTab
     * @param array|null $defaultProps
     * @return array|string
     * @throws Throwable
     */
    public function getHtmlResponse(ErrorModel $errorModel, string $defaultTab = 'StackTab', array $defaultProps = null)
    {
        $data = [
            'errorModel' => $errorModel,
            'title' => Arr::get($errorModel->toArray(), 'message'),
            'defaultTab' => $defaultTab,
            'defaultProps' => $defaultProps
        ];

        return view('laracatch::errorPage', $data)->render();
    }
}

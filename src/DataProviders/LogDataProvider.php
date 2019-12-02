<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\LogCollectorContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class LogDataProvider implements DataProviderContract
{
    /** @var LogCollectorContract */
    protected $logCollector;

    public function __construct(LogCollectorContract $logCollector)
    {
        $this->logCollector = $logCollector;
    }

    /**
     * Handle providing log data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setContextLogs($this->logCollector->getItems());
    }
}

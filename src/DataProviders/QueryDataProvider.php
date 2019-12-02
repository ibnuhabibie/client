<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\QueryCollectorContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class QueryDataProvider implements DataProviderContract
{
    /** @var QueryCollectorContract */
    protected $queryCollector;

    public function __construct(QueryCollectorContract $queryCollector)
    {
        $this->queryCollector = $queryCollector;
    }

    /**
     * Handle providing query data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setContextQueries($this->queryCollector->getItems());
    }
}
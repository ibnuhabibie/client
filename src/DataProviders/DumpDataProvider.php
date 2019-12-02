<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Collectors\Dumps\DumpCollector;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\DumpCollectorContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class DumpDataProvider implements DataProviderContract
{
    /** @var DumpCollectorContract */
    protected $collector;

    public function __construct(DumpCollectorContract $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Handle providing dump data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setContextDumps($this->collector->getItems());
    }
}
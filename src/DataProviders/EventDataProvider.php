<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Contracts\EventCollectorContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class EventDataProvider implements DataProviderContract
{
    /** @var EventCollectorContract */
    protected $eventCollector;

    public function __construct(EventCollectorContract $eventCollector)
    {
        $this->eventCollector = $eventCollector;
    }

    /**
     * Handle providing event data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setContextEvents($this->eventCollector->getItems());
    }
}

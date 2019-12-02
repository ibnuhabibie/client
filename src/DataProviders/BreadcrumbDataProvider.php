<?php

namespace Laracatch\Client\DataProviders;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\BreadcrumbCollectorContract;
use Laracatch\Client\Contracts\DataProviderContract;
use Laracatch\Client\Models\ErrorModel;
use Throwable;

class BreadcrumbDataProvider implements DataProviderContract
{
    /**
     * @var BreadcrumbCollectorContract
     */
    protected $breadcrumbCollector;

    public function __construct(BreadcrumbCollectorContract $breadcrumbCollector)
    {
        $this->breadcrumbCollector = $breadcrumbCollector;
    }

    /**
     * Handle providing breadcrumb data.
     *
     * @param ErrorModel $errorModel
     * @param Application $app
     * @param Throwable|null $throwable
     *
     * @return mixed
     */
    public function handle(ErrorModel $errorModel, Application $app, Throwable $throwable = null)
    {
        $errorModel->setBreadcrumbs($this->breadcrumbCollector->getItems());
    }
}
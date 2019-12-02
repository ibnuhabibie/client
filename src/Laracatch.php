<?php

namespace Laracatch\Client;

use Laracatch\Client\Collectors\Breadcrumbs\Breadcrumb;
use Laracatch\Client\Contracts\BreadcrumbCollectorContract;
use Psr\Log\LogLevel;

class Laracatch
{
    public function breadcrumb(string $name, string $logLevel = LogLevel::INFO, array $meta = [])
    {
        /** @var BreadcrumbCollectorContract $collector */
        $collector = app()->make(BreadcrumbCollectorContract::class);

        $collector->collect(new Breadcrumb($name, $logLevel, $meta));
    }
}
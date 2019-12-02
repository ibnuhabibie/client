<?php

namespace Laracatch\Client\Collectors\Breadcrumbs;

use Illuminate\Contracts\Foundation\Application;
use Laracatch\Client\Contracts\BreadcrumbCollectorContract;

class BreadcrumbCollector implements BreadcrumbCollectorContract
{
    /** @var Application */
    protected $app;

    /** @var array */
    protected $breadcrumbs = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Collect a breadcrumb.
     *
     * @param Breadcrumb $breadcrumb
     *
     * @return void
     */
    public function collect(Breadcrumb $breadcrumb): void
    {
        // Other collectors operate on events, and so can be conditionally made to listen. We can't do that
        // with breadcrumbs, so we check the configuration here instead and perform a NOP if we've turned
        // the collector off.
        if ( ! $this->app['config']->get('laracatch.collectors.breadcrumbs')) {
            return;
        }

        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * Get breadcrumbs.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * Reset breadcrumbs.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->breadcrumbs = [];
    }
}
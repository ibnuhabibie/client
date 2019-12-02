<?php

namespace Laracatch\Client\Contracts;

use Laracatch\Client\Collectors\Breadcrumbs\Breadcrumb;

interface BreadcrumbCollectorContract
{
    /**
     * Collect a breadcrumb.
     *
     * @param Breadcrumb $breadcrumb
     *
     * @return void
     */
    public function collect(Breadcrumb $breadcrumb): void;

    /**
     * Get breadcrumbs.
     *
     * @return array
     */
    public function getItems(): array;

    /**
     * Reset breadcrumbs.
     *
     * @return void
     */
    public function reset(): void;
}

<?php

namespace Laracatch\Client\Contracts;

use Laracatch\Client\Models\ErrorModel;
use Throwable;

interface DataProviderHandlerContract
{
    /**
     * Push a new HTTP provider to the list.
     *
     * @param DataProviderContract $provider
     *
     * @return void
     */
    public function pushHttpProvider(DataProviderContract $provider): void;

    /**
     * Push a new console provider to the list.
     *
     * @param DataProviderContract $provider
     *
     * @return void
     */
    public function pushConsoleProvider(DataProviderContract $provider): void;

    /**
     * Resolve data for HTTP requests.
     *
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return ErrorModel
     */
    public function resolveHttpData(ErrorModel $errorModel, Throwable $throwable = null): ErrorModel;

    /**
     * Resolve data for console requests.
     *
     * @param ErrorModel $errorModel
     * @param Throwable|null $throwable
     *
     * @return ErrorModel
     */
    public function resolveConsoleData(ErrorModel $errorModel, Throwable $throwable = null): ErrorModel;
}
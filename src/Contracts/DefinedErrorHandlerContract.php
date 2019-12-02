<?php

namespace Laracatch\Client\Contracts;

use Throwable;

interface DefinedErrorHandlerContract
{

    /**
     * Push a new defined error to the list.
     *
     * @param DefinedErrorContract $definedError
     * @return void
     */
    public function push(DefinedErrorContract $definedError): void;

    /**
     * @param Throwable $error
     * @return array|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveSolutions(Throwable $error): array;
}
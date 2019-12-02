<?php

namespace Laracatch\Client\Contracts;

use Throwable;

interface DefinedErrorContract
{

    /**
     * @param Throwable $error
     * @return bool
     */
    public function match(Throwable $error): bool;

    /**
     * @param Throwable $error
     * @return array
     */
    public function getSolutions(Throwable $error): array;
}
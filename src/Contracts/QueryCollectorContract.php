<?php

namespace Laracatch\Client\Contracts;

interface QueryCollectorContract
{
    /**
     * @return mixed
     */
    public function listen();

    /**
     * @return array
     */
    public function getItems(): array;

    /**
     * @return void
     */
    public function reset(): void;
}
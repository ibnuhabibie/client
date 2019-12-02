<?php

namespace Laracatch\Client\Contracts;

interface GitCollectorContract
{
    /**
     * Collect git data.
     *
     * @return void
     */
    public function collect(): void;

    /**
     * Get git data.
     *
     * @return array
     */
    public function getItems(): array;
}
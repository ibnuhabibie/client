<?php

namespace Laracatch\Client\Contracts;

use Symfony\Component\VarDumper\Cloner\Data;

interface DumpCollectorContract
{
    /**
     * Collect dump data.
     *
     * @param Data $data
     *
     * @return void
     */
    public function collect(Data $data): void;

    /**
     * Register the collector as a handler for dumping.
     *
     * @return void
     */
    public function listen(): void;

    /**
     * Get dumps.
     *
     * @return array
     */
    public function getItems(): array;

    /**
     * Reset dumps.
     *
     * @return void
     */
    public function reset(): void;
}
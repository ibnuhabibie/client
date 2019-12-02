<?php

namespace Laracatch\Client\Collectors\Dumps;

class DumpCollectionHandlerGroup
{
    /** @var array */
    protected $handlers = [];

    /**
     * Dump the given value into the known handlers.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function dump($value): void
    {
        foreach ($this->handlers as $handler) {
            $handler($value);
        }
    }

    /**
     * Add a new handler to the group.
     *
     * @param callable|null $handler
     *
     * @return void
     */
    public function add(callable $handler = null): void
    {
        $this->handlers[] = $handler;
    }
}

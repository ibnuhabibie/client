<?php

namespace Laracatch\Client\Contracts;

interface EventCollectorContract
{
    /**
     * @return mixed
     */
    public function listen();

    /**
     * @return array
     */
    public function getItems();

    /**
     * @return array
     */
    public function reset();
}

<?php

namespace Laracatch\Client\Contracts;

interface SolutionContract
{

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return array
     */
    public function getLinks(): array;

    /**
     * @return array
     */
    public function toArray(): array;
}
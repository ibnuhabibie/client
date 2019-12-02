<?php

namespace Laracatch\Client\Solutions;

use Laracatch\Client\Contracts\SolutionContract;

abstract class BaseSolution implements SolutionContract
{

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'links' => $this->getLinks(),
        ];
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return [];
    }
}

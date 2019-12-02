<?php

namespace Laracatch\Client\DefinedErrors;

use Illuminate\Support\Str;
use Laracatch\Client\Contracts\DefinedErrorContract;
use Laracatch\Client\Solutions\SuggestDefiningRouteName;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class RouteNameNotDefined implements DefinedErrorContract
{
    /**
     * @param Throwable $error
     *
     * @return bool
     */
    public function match(Throwable $error): bool
    {
        if ( ! $error instanceof RouteNotFoundException) {
            return false;
        }

        return Str::contains($error->getMessage(), 'not defined.');
    }

    /**
     * @param Throwable $error
     *
     * @return array
     */
    public function getSolutions(Throwable $error): array
    {
        return [
            new SuggestDefiningRouteName()
        ];
    }
}